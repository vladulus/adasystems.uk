<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutocompleteController extends Controller
{
    /**
     * Global search across all entities (for Management Dashboard)
     */
    public function global(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];
        $limit = 5; // results per category

        // Search Devices
        if (Auth::user()->can('devices.view')) {
            $devices = Device::where(function($q) use ($query) {
                $q->where('device_name', 'LIKE', "%{$query}%")
                  ->orWhere('serial_number', 'LIKE', "%{$query}%");
            })
            ->limit($limit)
            ->get();

            foreach ($devices as $device) {
                $results[] = [
                    'type' => 'device',
                    'icon' => 'fa-microchip',
                    'color' => '#6366f1',
                    'title' => $device->device_name,
                    'subtitle' => 'Serial: ' . ($device->serial_number ?? 'N/A'),
                    'url' => route('management.devices.edit', $device),
                    'status' => $device->status,
                ];
            }
        }

        // Search Vehicles
        if (Auth::user()->can('vehicles.view')) {
            $vehicles = Vehicle::where(function($q) use ($query) {
                $q->where('registration_number', 'LIKE', "%{$query}%")
                  ->orWhere('make', 'LIKE', "%{$query}%")
                  ->orWhere('model', 'LIKE', "%{$query}%")
                  ->orWhere('vin', 'LIKE', "%{$query}%");
            })
            ->limit($limit)
            ->get();

            foreach ($vehicles as $vehicle) {
                $results[] = [
                    'type' => 'vehicle',
                    'icon' => 'fa-car',
                    'color' => '#10b981',
                    'title' => $vehicle->registration_number ?? 'Unregistered',
                    'subtitle' => trim(($vehicle->make ?? '') . ' ' . ($vehicle->model ?? '')),
                    'url' => route('management.vehicles.edit', $vehicle),
                    'status' => $vehicle->status,
                ];
            }
        }

        // Search Users
        if (Auth::user()->can('users.view')) {
            $users = User::where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%");
            })
            ->limit($limit)
            ->get();

            foreach ($users as $user) {
                $results[] = [
                    'type' => 'user',
                    'icon' => 'fa-user',
                    'color' => '#0ea5e9',
                    'title' => $user->name,
                    'subtitle' => $user->email,
                    'url' => route('management.users.edit', $user),
                    'status' => $user->status,
                ];
            }
        }

        // Search Drivers
        if (Auth::user()->can('drivers.view')) {
            $drivers = Driver::where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%")
                  ->orWhere('license_number', 'LIKE', "%{$query}%");
            })
            ->limit($limit)
            ->get();

            foreach ($drivers as $driver) {
                $results[] = [
                    'type' => 'driver',
                    'icon' => 'fa-id-card',
                    'color' => '#f59e0b',
                    'title' => $driver->name,
                    'subtitle' => 'License: ' . ($driver->license_number ?? 'N/A'),
                    'url' => route('management.drivers.edit', $driver),
                    'status' => $driver->status,
                ];
            }
        }

        return response()->json([
            'results' => $results,
            'query' => $query,
        ]);
    }

    /**
     * Search Devices only
     */
    public function devices(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $devices = Device::where(function($q) use ($query) {
            $q->where('device_name', 'LIKE', "%{$query}%")
              ->orWhere('serial_number', 'LIKE', "%{$query}%");
        })
        ->with('vehicle')
        ->limit(10)
        ->get();

        $results = $devices->map(function($device) {
            return [
                'type' => 'device',
                'icon' => 'fa-microchip',
                'color' => '#6366f1',
                'title' => $device->device_name,
                'subtitle' => $device->vehicle 
                    ? 'Vehicle: ' . $device->vehicle->registration_number 
                    : 'Serial: ' . ($device->serial_number ?? 'N/A'),
                'url' => route('management.devices.edit', $device),
                'status' => $device->status,
            ];
        });

        return response()->json([
            'results' => $results,
            'query' => $query,
        ]);
    }

    /**
     * Search Vehicles only
     */
    public function vehicles(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $vehicles = Vehicle::where(function($q) use ($query) {
            $q->where('registration_number', 'LIKE', "%{$query}%")
              ->orWhere('make', 'LIKE', "%{$query}%")
              ->orWhere('model', 'LIKE', "%{$query}%")
              ->orWhere('vin', 'LIKE', "%{$query}%");
        })
        ->with('device')
        ->limit(10)
        ->get();

        $results = $vehicles->map(function($vehicle) {
            return [
                'type' => 'vehicle',
                'icon' => 'fa-car',
                'color' => '#10b981',
                'title' => $vehicle->registration_number ?? 'Unregistered',
                'subtitle' => trim(($vehicle->make ?? '') . ' ' . ($vehicle->model ?? '') . ($vehicle->year ? ' · ' . $vehicle->year : '')),
                'url' => route('management.vehicles.edit', $vehicle),
                'status' => $vehicle->status,
            ];
        });

        return response()->json([
            'results' => $results,
            'query' => $query,
        ]);
    }

    /**
     * Search Users only
     */
    public function users(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $users = User::where(function($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%")
              ->orWhere('phone', 'LIKE', "%{$query}%");
        })
        ->with('roles')
        ->limit(10)
        ->get();

        $results = $users->map(function($user) {
            $role = $user->roles->first();
            return [
                'type' => 'user',
                'icon' => 'fa-user',
                'color' => '#0ea5e9',
                'title' => $user->name,
                'subtitle' => $user->email . ($role ? ' · ' . ucfirst($role->name) : ''),
                'url' => route('management.users.edit', $user),
                'status' => $user->status,
            ];
        });

        return response()->json([
            'results' => $results,
            'query' => $query,
        ]);
    }

    /**
     * Search Drivers only
     */
    public function drivers(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $drivers = Driver::where(function($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%")
              ->orWhere('phone', 'LIKE', "%{$query}%")
              ->orWhere('license_number', 'LIKE', "%{$query}%");
        })
        ->limit(10)
        ->get();

        $results = $drivers->map(function($driver) {
            return [
                'type' => 'driver',
                'icon' => 'fa-id-card',
                'color' => '#f59e0b',
                'title' => $driver->name,
                'subtitle' => 'License: ' . ($driver->license_number ?? 'N/A') . ' · ' . ($driver->license_type ?? ''),
                'url' => route('management.drivers.edit', $driver),
                'status' => $driver->status,
            ];
        });

        return response()->json([
            'results' => $results,
            'query' => $query,
        ]);
    }
}
