<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * List drivers with filters and statistics.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Driver::class);

        $query = Driver::with('vehicles');

        // Free text search (name, email, phone, license, vehicle plate)
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%")
                    ->orWhereHas('vehicles', function ($q2) use ($search) {
                        $q2->where('registration_number', 'like', "%{$search}%")
                           ->orWhere('make', 'like', "%{$search}%")
                           ->orWhere('model', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Assignment filter (with / without vehicles)
        if ($request->filled('assignment')) {
            if ($request->assignment === 'with_vehicle') {
                $query->has('vehicles');
            } elseif ($request->assignment === 'without_vehicle') {
                $query->doesntHave('vehicles');
            }
        }

        // Simple license status filter (uses license_expiry_date on drivers table)
        if ($request->filled('license_status')) {
            switch ($request->license_status) {
                case 'expired':
                    $query->where('license_expiry_date', '<', now());
                    break;
                case 'expiring_soon':
                    $query->whereBetween('license_expiry_date', [now(), now()->addDays(30)]);
                    break;
                case 'valid':
                    $query->where('license_expiry_date', '>=', now());
                    break;
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');

        if (! in_array($sortField, ['name', 'status', 'created_at'], true)) {
            $sortField = 'name';
        }
        if (! in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortField, $sortOrder);

        $drivers = $query->paginate($request->get('per_page', 15))
            ->appends($request->except('page'));

        $stats = $this->getDriverStatistics();

        return view('management.drivers.index', compact('drivers', 'stats'));
    }

    /**
     * Show form for creating a new driver.
     */
    public function create()
    {
        $this->authorize('create', Driver::class);

        $vehicles = Vehicle::orderBy('registration_number')->get();

        return view('management.drivers.create', compact('vehicles'));
    }

    /**
     * Store a newly created driver.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Driver::class);

        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'              => ['nullable', 'string', 'max:20'],
            'date_of_birth'      => ['nullable', 'date'],
            'address'            => ['nullable', 'string', 'max:500'],
            'license_number'     => [
                'required',
                'string',
                'max:50',
                'unique:drivers,license_number',
            ],
            'license_type'       => ['required', 'string', 'max:255'],
            'license_issue_date' => ['nullable', 'date', 'before_or_equal:today'],
            'license_expiry_date'=> ['nullable', 'date'],
            'status'             => ['required', 'in:active,inactive,on_leave'],
            'hire_date'          => ['nullable', 'date', 'before_or_equal:today'],
            'emergency_contact'  => ['nullable', 'string', 'max:255'],
            'notes'              => ['nullable', 'string', 'max:2000'],
            'vehicle_ids'        => ['nullable', 'array'],
            'vehicle_ids.*'      => ['integer', 'exists:vehicles,id'],
        ]);

        // Normalize license number
        $validated['license_number'] = strtoupper($validated['license_number']);

        \DB::beginTransaction();
        try {
            $userId = null;
            $successMessage = 'Driver created successfully.';

            // Creează User doar dacă are email
            if (!empty($validated['email'])) {
                $user = \App\Models\User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => \Hash::make($validated['license_number']),
                    'phone' => $validated['phone'] ?? null,
                    'status' => $validated['status'] === 'active' ? 'active' : 'inactive',
                    'email_verified_at' => now(),
                    'created_by' => auth()->id(),
                ]);
                $user->assignRole('user');
                $userId = $user->id;
                $successMessage = 'Driver created with login account. Password: ' . $validated['license_number'];
            }

            // Creează Driver
            $driver = Driver::create([
                'user_id' => $userId,
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'address' => $validated['address'] ?? null,
                'license_number' => $validated['license_number'],
                'license_type' => $validated['license_type'],
                'license_issue_date' => $validated['license_issue_date'] ?? null,
                'license_expiry_date' => $validated['license_expiry_date'] ?? null,
                'status' => $validated['status'],
                'hire_date' => $validated['hire_date'] ?? null,
                'emergency_contact' => $validated['emergency_contact'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // 3. Attach vehicles (many-to-many)
            $driver->vehicles()->sync($validated['vehicle_ids'] ?? []);

            \DB::commit();

            return redirect()
                ->route('management.drivers.index')
                ->with('success', 'Driver created successfully. Login: ' . $validated['email'] . ' / Password: ' . $validated['license_number']);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Driver creation failed: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create driver: ' . $e->getMessage());
        }
    }

    /**
     * For now redirect show to edit.
     */
    public function show(Driver $driver)
    {
        return redirect()->route('management.drivers.edit', $driver);
    }

    /**
     * Show form for editing a driver.
     */
    public function edit(Driver $driver)
    {
        $this->authorize('update', $driver);

        $driver->load(['vehicles', 'employers']);
        
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->isEffectiveSuperAdmin();
        $isAdmin = $currentUser->hasRole('admin');
        $isSuperuser = $currentUser->isSuperuser();
        
        // Vehicles: filtrate în funcție de rol
        if ($isSuperAdmin) {
            $vehicles = Vehicle::with('owner')->orderBy('registration_number')->get();
        } elseif ($isAdmin) {
            // Admin: vehiculele superuserilor pe care îi administrează
            $superuserIds = $currentUser->managedSuperusers->pluck('id');
            $vehicles = Vehicle::with('owner')
                ->whereIn('owner_id', $superuserIds)
                ->orderBy('registration_number')
                ->get();
        } else {
            // Superuser: doar vehiculele proprii
            $vehicles = Vehicle::where('owner_id', $currentUser->id)
                ->orderBy('registration_number')
                ->get();
        }
        
        // Employers (superusers): doar pentru super-admin și admin
        $allSuperusers = [];
        if ($isSuperAdmin) {
            $allSuperusers = \App\Models\User::role('superuser')->orderBy('name')->get();
        } elseif ($isAdmin) {
            $allSuperusers = $currentUser->managedSuperusers;
        }

        return view('management.drivers.edit', compact('driver', 'vehicles', 'allSuperusers'));
    }

    /**
     * Update driver.
     */
    public function update(Request $request, Driver $driver)
    {
        $this->authorize('update', $driver);

        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['nullable', 'email', 'max:255'],
            'phone'              => ['nullable', 'string', 'max:20'],
            'date_of_birth'      => ['nullable', 'date'],
            'address'            => ['nullable', 'string', 'max:500'],
            'license_number'     => [
                'required',
                'string',
                'max:50',
                'unique:drivers,license_number,' . $driver->id,
            ],
            'license_type'       => ['required', 'string', 'max:255'],
            'license_issue_date' => ['nullable', 'date', 'before_or_equal:today'],
            'license_expiry_date'=> ['nullable', 'date'],
            'status'             => ['required', 'in:active,inactive,on_leave'],
            'hire_date'          => ['nullable', 'date', 'before_or_equal:today'],
            'emergency_contact'  => ['nullable', 'string', 'max:255'],
            'notes'              => ['nullable', 'string', 'max:2000'],
            'vehicle_ids'        => ['nullable', 'array'],
            'vehicle_ids.*'      => ['integer', 'exists:vehicles,id'],
            'employer_ids'       => ['nullable', 'array'],
            'employer_ids.*'     => ['integer', 'exists:users,id'],
        ]);

        $validated['license_number'] = strtoupper($validated['license_number']);

        $driver->update($validated);

        // Sync vehicles
        $driver->vehicles()->sync($validated['vehicle_ids'] ?? []);
        
        // Sync employers (doar pentru super-admin și admin)
        $currentUser = auth()->user();
        if ($currentUser->isEffectiveSuperAdmin() || $currentUser->hasRole('admin')) {
            if ($request->has('employer_ids')) {
                $driver->employers()->sync($validated['employer_ids'] ?? []);
            }
        }

        return redirect()
            ->route('management.drivers.index')
            ->with('success', 'Driver updated successfully.');
    }

    /**
     * Delete driver.
     */
    public function destroy(Driver $driver)
    {
        $this->authorize('delete', $driver);

        // Detach vehicles from pivot table
        $driver->vehicles()->detach();

        $driver->delete();

        return redirect()
            ->route('management.drivers.index')
            ->with('success', 'Driver deleted successfully.');
    }

    /**
     * Basic statistics for the overview card.
     */
    private function getDriverStatistics(): array
    {
        $user = auth()->user();
        $base = Driver::query();

        // Apply scope permission
        if (!$user->can('drivers.scope.all')) {
            $base->where('user_id', $user->id);
        }

        return [
            'total'    => (clone $base)->count(),
            'active'   => (clone $base)->where('status', 'active')->count(),
            'inactive' => (clone $base)->where('status', 'inactive')->count(),
            'on_leave' => (clone $base)->where('status', 'on_leave')->count(),
        ];
    }
}
