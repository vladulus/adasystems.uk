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
            'email'              => ['nullable', 'email', 'max:255'],
            'phone'              => [
                'required',
                'string',
                'max:20',
                'regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/',
            ],
            'date_of_birth'      => ['nullable', 'date'],
            'address'            => ['nullable', 'string', 'max:500'],
            'license_number'     => [
                'required',
                'string',
                'max:50',
                'unique:drivers,license_number',
                'regex:/^[A-Z0-9\-]+$/i',
            ],
            'license_type'       => ['required', 'string', 'max:255'],
            'license_issue_date' => ['nullable', 'date', 'before_or_equal:today'],
            'license_expiry_date'=> ['required', 'date', 'after_or_equal:license_issue_date'],
            'status'             => ['required', 'in:active,inactive,on_leave'],
            'hire_date'          => ['nullable', 'date', 'before_or_equal:today'],
            'emergency_contact'  => ['nullable', 'string', 'max:255'],
            'notes'              => ['nullable', 'string', 'max:2000'],
            'vehicle_ids'        => ['nullable', 'array'],
            'vehicle_ids.*'      => ['integer', 'exists:vehicles,id'],
        ]);

        // Normalize license number
        $validated['license_number'] = strtoupper($validated['license_number']);

        $driver = Driver::create($validated);

        // Attach vehicles (many-to-many)
        $driver->vehicles()->sync($validated['vehicle_ids'] ?? []);

        return redirect()
            ->route('management.drivers.index')
            ->with('success', 'Driver created successfully.');
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

        $driver->load('vehicles');
        $vehicles = Vehicle::orderBy('registration_number')->get();

        return view('management.drivers.edit', compact('driver', 'vehicles'));
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
            'phone'              => [
                'required',
                'string',
                'max:20',
                'regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/',
            ],
            'date_of_birth'      => ['nullable', 'date'],
            'address'            => ['nullable', 'string', 'max:500'],
            'license_number'     => [
                'required',
                'string',
                'max:50',
                'unique:drivers,license_number,' . $driver->id,
                'regex:/^[A-Z0-9\-]+$/i',
            ],
            'license_type'       => ['required', 'string', 'max:255'],
            'license_issue_date' => ['nullable', 'date', 'before_or_equal:today'],
            'license_expiry_date'=> ['required', 'date', 'after_or_equal:license_issue_date'],
            'status'             => ['required', 'in:active,inactive,on_leave'],
            'hire_date'          => ['nullable', 'date', 'before_or_equal:today'],
            'emergency_contact'  => ['nullable', 'string', 'max:255'],
            'notes'              => ['nullable', 'string', 'max:2000'],
            'vehicle_ids'        => ['nullable', 'array'],
            'vehicle_ids.*'      => ['integer', 'exists:vehicles,id'],
        ]);

        $validated['license_number'] = strtoupper($validated['license_number']);

        $driver->update($validated);

        // Sync vehicles
        $driver->vehicles()->sync($validated['vehicle_ids'] ?? []);

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
        $base = Driver::query();

        return [
            'total'    => (clone $base)->count(),
            'active'   => (clone $base)->where('status', 'active')->count(),
            'inactive' => (clone $base)->where('status', 'inactive')->count(),
            'on_leave' => (clone $base)->where('status', 'on_leave')->count(),
        ];
    }
}
