<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Device;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Vehicle::class);

        $query = Vehicle::with(['device', 'owner']);

        if ($request->filled('search')) {
            $search = $request->get('search');

            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhere('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%")
                  ->orWhereHas('device', function ($q2) use ($search) {
                      $q2->where('device_name', 'like', "%{$search}%")
                         ->orWhere('serial_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('assignment')) {
            if ($request->assignment === 'with_device') {
                $query->whereNotNull('device_id');
            } elseif ($request->assignment === 'without_device') {
                $query->whereNull('device_id');
            }
        }

        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $vehicles = $query->paginate($request->get('per_page', 15))
            ->appends($request->except('page'));

        $stats = $this->getVehicleStatistics();

        return view('management.vehicles.index', compact('vehicles', 'stats'));
    }

    public function create()
    {
        $this->authorize('create', Vehicle::class);

        $devices = Device::select('id', 'device_name', 'serial_number')
            ->whereDoesntHave('vehicle')
            ->orderBy('device_name')
            ->get();

        return view('management.vehicles.create', compact('devices'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Vehicle::class);

        $validated = $request->validate([
            'registration_number' => ['required', 'string', 'max:30', 'unique:vehicles,registration_number'],
            'make'                => ['nullable', 'string', 'max:50'],
            'model'               => ['nullable', 'string', 'max:50'],
            'year'                => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'vin'                 => ['nullable', 'string', 'max:17'],
            'status'              => ['required', 'in:active,inactive,service'],
            'device_id'           => ['nullable', 'exists:devices,id'],
        ]);

        $vehicle = Vehicle::create([
            'registration_number' => $validated['registration_number'],
            'make'                => $validated['make'] ?? null,
            'model'               => $validated['model'] ?? null,
            'year'                => $validated['year'] ?? null,
            'vin'                 => $validated['vin'] ?? null,
            'status'              => $validated['status'],
            'device_id'           => $validated['device_id'] ?? null,
            'owner_id'            => auth()->id(),
        ]);

        if (!empty($validated['device_id'])) {
            Device::where('id', $validated['device_id'])
                ->update(['owner_id' => auth()->id()]);
        }

        return redirect()
            ->route('management.vehicles.index')
            ->with('success', 'Vehicle created successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        // deocamdată doar redirect la edit
        return redirect()->route('management.vehicles.edit', $vehicle);
    }

    public function edit(Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);

        $vehicle->load(['device', 'drivers', 'owner']);
        
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->isEffectiveSuperAdmin();
        $isAdmin = $currentUser->hasRole('admin');
        $isSuperuser = $currentUser->isSuperuser();
        
        // Devices: filtrate în funcție de rol și owner
        $devices = [];
        if ($isSuperAdmin || $isAdmin) {
            $devices = Device::with('owner', 'vehicle')
                ->where(function ($q) use ($vehicle) {
                    // Device-uri libere sau al acestui vehicul
                    $q->whereDoesntHave('vehicle')
                      ->orWhereHas('vehicle', fn($q2) => $q2->where('id', $vehicle->id));
                })
                ->orderBy('device_name')
                ->get();
        }
        
        // Drivers: filtrate în funcție de rol
        if ($isSuperAdmin) {
            $drivers = \App\Models\Driver::orderBy('name')->get();
        } elseif ($isAdmin) {
            // Admin: driverii superuserilor pe care îi administrează
            $superuserIds = $currentUser->managedSuperusers->pluck('id');
            $drivers = \App\Models\Driver::whereHas('employers', fn($q) => $q->whereIn('superuser_id', $superuserIds))
                ->orderBy('name')
                ->get();
        } else {
            // Superuser: doar driverii proprii
            $drivers = $currentUser->employedDrivers;
        }
        
        // Owners (superusers): doar pentru super-admin și admin
        $allSuperusers = [];
        if ($isSuperAdmin) {
            $allSuperusers = \App\Models\User::role('superuser')->orderBy('name')->get();
        } elseif ($isAdmin) {
            $allSuperusers = $currentUser->managedSuperusers;
        }

        return view('management.vehicles.edit', compact('vehicle', 'devices', 'drivers', 'allSuperusers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->isEffectiveSuperAdmin();
        $isAdmin = $currentUser->hasRole('admin');

        $validated = $request->validate([
            'registration_number' => [
                'required',
                'string',
                'max:30',
                'unique:vehicles,registration_number,' . $vehicle->id,
            ],
            'make'       => ['nullable', 'string', 'max:50'],
            'model'      => ['nullable', 'string', 'max:50'],
            'year'       => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'vin'        => ['nullable', 'string', 'max:17'],
            'status'     => ['required', 'in:active,inactive,service'],
            'device_id'  => ['nullable', 'exists:devices,id'],
            'owner_id'   => ['nullable', 'exists:users,id'],
            'driver_ids' => ['nullable', 'array'],
            'driver_ids.*' => ['integer', 'exists:drivers,id'],
        ]);

        $updateData = [
            'registration_number' => $validated['registration_number'],
            'make'                => $validated['make'] ?? null,
            'model'               => $validated['model'] ?? null,
            'year'                => $validated['year'] ?? null,
            'vin'                 => $validated['vin'] ?? null,
            'status'              => $validated['status'],
        ];
        
        // Device și Owner: doar super-admin și admin pot modifica
        if ($isSuperAdmin || $isAdmin) {
            // Verifică că device-ul aparține aceluiași owner
            if (!empty($validated['device_id'])) {
                $device = Device::find($validated['device_id']);
                $ownerId = $validated['owner_id'] ?? $vehicle->owner_id;
                
                // Dacă device-ul are owner și e diferit de owner-ul vehiculului, eroare
                if ($device && $device->owner_id && $ownerId && $device->owner_id != $ownerId) {
                    return back()->withInput()->with('error', 'Device belongs to a different owner. Cannot assign.');
                }
            }
            
            $updateData['device_id'] = $validated['device_id'] ?? null;
            $updateData['owner_id'] = $validated['owner_id'] ?? null;
        }

        $vehicle->update($updateData);
        
        // Sync drivers
        if ($request->has('driver_ids')) {
            $vehicle->drivers()->sync($validated['driver_ids'] ?? []);
        }

        return redirect()
            ->route('management.vehicles.index')
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorize('delete', $vehicle);

        $vehicle->update(['device_id' => null]);
        $vehicle->delete();

        return redirect()
            ->route('management.vehicles.index')
            ->with('success', 'Vehicle deleted successfully.');
    }

    private function getVehicleStatistics()
    {
        $query = Vehicle::query();
        $base = clone $query;

        return [
            'total'       => $base->count(),
            'active'      => (clone $base)->where('status', 'active')->count(),
            'inactive'    => (clone $base)->where('status', 'inactive')->count(),
            'service'     => (clone $base)->where('status', 'service')->count(),
            'with_device' => (clone $base)->whereNotNull('device_id')->count(),
            'no_device'   => (clone $base)->whereNull('device_id')->count(),
        ];
    }
}
