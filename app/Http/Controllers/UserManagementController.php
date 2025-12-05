<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreatedNotification;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with advanced filtering
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::with(['roles', 'permissions'])
            ->when(auth()->user()->hasRole('client'), function ($q) {
                // Clients can only see users they created
                $q->where('created_by', auth()->id());
            });

        // Advanced search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by email verification
        if ($request->filled('email_verified')) {
            if ($request->email_verified === 'verified') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Date range filters
        if ($request->filled('created_from')) {
            $query->where('created_at', '>=', $request->created_from);
        }

        if ($request->filled('created_to')) {
            $query->where('created_at', '<=', $request->created_to);
        }

        // Last login filter
        if ($request->filled('last_login')) {
            switch ($request->last_login) {
                case 'today':
                    $query->whereDate('last_login_at', today());
                    break;
                case 'week':
                    $query->where('last_login_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('last_login_at', '>=', now()->subMonth());
                    break;
                case 'never':
                    $query->whereNull('last_login_at');
                    break;
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $users = $query->paginate($request->get('per_page', 15))
            ->appends($request->except('page'));

        // Calculate statistics
        $stats = $this->getUserStatistics();

        // Get roles for filter dropdown
        $roles = Role::orderBy('name')->get();

        // Get unique departments
        $departments = User::select('department')
            ->distinct()
            ->whereNotNull('department')
            ->orderBy('department')
            ->pluck('department');

        return view('management.users.index', compact('users', 'stats', 'roles', 'departments'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $roles = Role::orderBy('name')->get();
        
        return view('management.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,pending',
            'role' => 'required|exists:roles,name',
            'email_verified' => 'nullable|boolean',
            'send_welcome_email' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'department' => $validated['department'] ?? null,
                'status' => $validated['status'],
                'email_verified_at' => $request->boolean('email_verified') ? now() : null,
                'created_by' => auth()->id(),
            ]);

            // Assign role
            $user->assignRole($validated['role']);

            // Send welcome email if requested
            if ($request->boolean('send_welcome_email')) {
                try {
                    Mail::to($user->email)->send(
                        new UserCreatedNotification($user, $validated['password'])
                    );
                } catch (\Exception $e) {
                    Log::warning('Failed to send welcome email', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();

            Log::info('User created', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $validated['role'],
                'created_by' => auth()->id(),
            ]);

            return redirect()
                ->route('management.users.index')
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('User creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'created_by' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create user. Please try again.');
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load(['roles', 'permissions', 'createdBy', 'updatedBy']);

        // Calculate user statistics
        $stats = [
            'total_logins' => $user->login_count ?? 0,
            'last_login' => $user->last_login_at,
            'account_age_days' => $user->created_at->diffInDays(now()),
            'devices_managed' => $user->devices()->count(),
            'vehicles_managed' => $user->vehicles()->count(),
            'password_last_changed' => $user->password_changed_at,
            'failed_login_attempts' => $user->failed_login_attempts ?? 0,
        ];

        // Get recent activity
        $recentActivity = $user->activityLogs()
            ->latest()
            ->take(20)
            ->get();

        // Get assigned permissions
        $allPermissions = Permission::all()->groupBy(function($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('management.users.show', compact('user', 'stats', 'recentActivity', 'allPermissions'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = Role::orderBy('name')->get();
        
        return view('management.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            'phone' => 'nullable|string|max:20|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/',
            'department' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,pending',
            'role' => 'required|exists:roles,name',
            'email_verified' => 'nullable|boolean',
        ]);

        // Prevent self-demotion and self-deactivation
        if ($user->id === auth()->id()) {
            if ($validated['status'] !== $user->status && $validated['status'] !== 'active') {
                return back()->with('error', 'You cannot deactivate your own account.');
            }
            
            if ($validated['role'] !== $user->roles->first()?->name) {
                return back()->with('error', 'You cannot change your own role.');
            }
        }

        DB::beginTransaction();
        try {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'department' => $validated['department'] ?? null,
                'status' => $validated['status'],
                'updated_by' => auth()->id(),
            ];

            // Update password only if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
                $updateData['password_changed_at'] = now();
                
                // Force re-login if password changed for current user
                if ($user->id === auth()->id()) {
                    session()->put('password_changed', true);
                }
            }

            // Update email verification
            if ($request->has('email_verified') && !$user->email_verified_at) {
                $updateData['email_verified_at'] = now();
            } elseif (!$request->has('email_verified') && $user->email_verified_at) {
                $updateData['email_verified_at'] = null;
            }

            $user->update($updateData);

            // Update role (only if not editing own account)
            if ($user->id !== auth()->id()) {
                $user->syncRoles([$validated['role']]);
            }

            // Reset failed login attempts if status changed to active
            if ($validated['status'] === 'active' && $user->wasChanged('status')) {
                $user->update(['failed_login_attempts' => 0]);
            }

            DB::commit();

            Log::info('User updated', [
                'user_id' => $user->id,
                'changes' => $user->getChanges(),
                'updated_by' => auth()->id()
            ]);

            return redirect()->route('management.users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update user. Please try again.');
        }
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Check for dependencies
        $issues = [];

        if ($user->devices()->count() > 0) {
            $issues[] = $user->devices()->count() . ' device(s) created';
        }

        if ($user->vehicles()->count() > 0) {
            $issues[] = $user->vehicles()->count() . ' vehicle(s) created';
        }

        if ($user->driver) {
            $issues[] = 'linked driver profile';
        }

        if (!empty($issues)) {
            return back()->with('error', 
                'Cannot delete user with: ' . implode(', ', $issues) . '. Please reassign or remove these items first.');
        }

        DB::beginTransaction();
        try {
            $email = $user->email;
            $name = $user->name;
            
            // Soft delete to preserve audit trail
            $user->delete();

            DB::commit();

            Log::warning('User deleted', [
                'email' => $email,
                'name' => $name,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('management.users.index')
                ->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User deletion failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'deleted_by' => auth()->id()
            ]);

            return back()->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Calculate user statistics
     */
    private function getUserStatistics()
    {
        $query = User::query();

        if (auth()->user()->hasRole('client')) {
            $query->where('created_by', auth()->id());
        }

        return [
            'total' => $query->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'inactive' => (clone $query)->where('status', 'inactive')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'admins' => (clone $query)->role(['admin', 'super-admin'])->count(),
            'email_verified' => (clone $query)->whereNotNull('email_verified_at')->count(),
            'email_unverified' => (clone $query)->whereNull('email_verified_at')->count(),
            'logged_in_today' => (clone $query)->whereDate('last_login_at', today())->count(),
            'never_logged_in' => (clone $query)->whereNull('last_login_at')->count(),
            'created_this_month' => (clone $query)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }
}
