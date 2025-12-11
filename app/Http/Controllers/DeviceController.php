<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Listă de devices cu filtre simple.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Device::class);

        $query = Device::with('vehicle');

        // search simplu
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('device_name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($q2) use ($search) {
                      $q2->where('registration_number', 'like', "%{$search}%");
                  });
            });
        }

        // filtrare status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // filtrare assignment (assigned / unassigned)
        if ($request->filled('assignment')) {
            if ($request->get('assignment') === 'assigned') {
                $query->whereHas('vehicle');
            } elseif ($request->get('assignment') === 'unassigned') {
                $query->whereDoesntHave('vehicle');
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'device_name');
        $sortDir = $request->get('dir', 'asc');
        
        // Validare direcție
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'asc';
        }
        
        // Validare câmpuri permise
        $allowedSorts = ['device_name', 'serial_number', 'status', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->orderBy('device_name', 'asc');
        }

        $devices = $query->paginate($request->get('per_page', 15))
            ->appends($request->except('page'));

        $stats = $this->getDeviceStatistics();

        return view('management.devices.index', compact('devices', 'stats'));
    }

    /**
     * Formular "add device".
     */
    public function create()
    {
        $this->authorize('create', Device::class);

        $user = auth()->user();
        
        // vehicule neatribuite (filtrate după scope)
        $vehicles = Vehicle::select('id', 'registration_number', 'make', 'model')
            ->when(!$user->can('vehicles.scope.all'), function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
            ->whereDoesntHave('device')
            ->orderBy('registration_number')
            ->get();

        return view('management.devices.create', compact('vehicles'));
    }

    /**
     * Salvează un device nou.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Device::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255', 'unique:devices,serial_number'],
            'status' => ['required', 'in:active,inactive,maintenance'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
        ]);

        $device = Device::create([
            'device_name'   => $validated['name'],
            'serial_number' => $validated['serial_number'] ?? null,
            'owner_id'      => auth()->id(),
            'status'        => $validated['status'],
        ]);

        // leagă device-ul de vehicul, dacă s-a selectat unul
        if (!empty($validated['vehicle_id'])) {
            Vehicle::where('id', $validated['vehicle_id'])
                ->update(['device_id' => $device->id]);
        }

        return redirect()
            ->route('management.devices.index')
            ->with('success', 'Device created successfully.');
    }

    /**
     * Formular "edit device".
     */
    public function edit(Device $device)
    {
        $this->authorize('update', $device);

        $device->load(['vehicle', 'owner', 'admins']);
        
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->isEffectiveSuperAdmin();
        $isAdmin = $currentUser->hasRole('admin');
        
        // Vehicles: filtrate în funcție de owner-ul device-ului
        $vehicles = Vehicle::with('owner')
            ->where(function ($q) use ($device) {
                $q->whereNull('device_id')
                  ->orWhere('device_id', $device->id);
            })
            ->when($device->owner_id, function($q) use ($device) {
                // Dacă device-ul are owner, arată doar vehiculele aceluiași owner
                $q->where('owner_id', $device->owner_id);
            })
            ->orderBy('registration_number')
            ->get();
        
        // Owners (superusers)
        $allSuperusers = [];
        if ($isSuperAdmin) {
            $allSuperusers = \App\Models\User::role('superuser')->orderBy('name')->get();
        } elseif ($isAdmin) {
            $allSuperusers = $currentUser->managedSuperusers;
        }
        
        // Admins: doar pentru super-admin
        $allAdmins = [];
        if ($isSuperAdmin) {
            $allAdmins = \App\Models\User::role('admin')->orderBy('name')->get();
        }

        return view('management.devices.edit', compact('device', 'vehicles', 'allSuperusers', 'allAdmins'));
    }

    /**
     * Update device.
     */
    public function update(Request $request, Device $device)
    {
        $this->authorize('update', $device);

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->isEffectiveSuperAdmin();

        $validated = $request->validate([
            'device_name' => ['required', 'string', 'max:255'],
            'serial_number' => [
                'nullable',
                'string',
                'max:255',
                'unique:devices,serial_number,' . $device->id,
            ],
            'status' => ['required', 'in:active,inactive,maintenance'],
            'upload_interval' => ['nullable', 'integer', 'min:5', 'max:300'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'admin_ids' => ['nullable', 'array'],
            'admin_ids.*' => ['integer', 'exists:users,id'],
        ]);

        // Verifică că vehiculul aparține aceluiași owner
        if (!empty($validated['vehicle_id']) && !empty($validated['owner_id'])) {
            $vehicle = Vehicle::find($validated['vehicle_id']);
            if ($vehicle && $vehicle->owner_id && $vehicle->owner_id != $validated['owner_id']) {
                return back()->withInput()->with('error', 'Vehicle belongs to a different owner. Cannot assign.');
            }
        }

        $device->update([
            'device_name'     => $validated['device_name'],
            'serial_number'   => $validated['serial_number'] ?? null,
            'status'          => $validated['status'],
            'upload_interval' => $validated['upload_interval'] ?? 15,
            'owner_id'        => $validated['owner_id'] ?? null,
        ]);

        // Curățăm device_id de pe toate vehiculele unde era device-ul ăsta
        Vehicle::where('device_id', $device->id)->update(['device_id' => null]);

        // Dacă s-a ales un vehicul nou, îl legăm
        if (!empty($validated['vehicle_id'])) {
            Vehicle::where('id', $validated['vehicle_id'])
                ->update(['device_id' => $device->id]);
        }
        
        // Sync admins: doar super-admin poate
        if ($isSuperAdmin && $request->has('admin_ids')) {
            $device->admins()->sync($validated['admin_ids'] ?? []);
        }

        return redirect()
            ->route('management.devices.index')
            ->with('success', 'Device updated successfully.');
    }

    /**
     * Șterge device.
     */
    public function destroy(Device $device)
    {
        $this->authorize('delete', $device);

        // scoatem legătura cu vehicle
        Vehicle::where('device_id', $device->id)->update(['device_id' => null]);

        $device->delete();

        return redirect()
            ->route('management.devices.index')
            ->with('success', 'Device deleted successfully.');
    }

    /**
     * Statistici simple pentru cardul din dreapta.
     */
    private function getDeviceStatistics()
    {
        $user = auth()->user();
        $query = Device::query();

        // Apply scope permission
        if (!$user->can('devices.scope.all')) {
            $query->whereHas('vehicle', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        }

        $base = clone $query;

        return [
            'total'       => $base->count(),
            'active'      => (clone $base)->where('status', 'active')->count(),
            'inactive'    => (clone $base)->where('status', 'inactive')->count(),
            'maintenance' => (clone $base)->where('status', 'maintenance')->count(),
            'unassigned'  => (clone $base)->whereDoesntHave('vehicle')->count(),
            'assigned'    => (clone $base)->whereHas('vehicle')->count(),
        ];
    }
}
