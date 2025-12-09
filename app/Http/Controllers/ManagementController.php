<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagementController extends Controller
{
    /**
     * Display management dashboard with global search
     */
    public function index(Request $request)
    {
        $searchResults = null;
        $searchQuery = null;
        $user = auth()->user();

        // Global search functionality
        if ($request->filled('search')) {
            $searchQuery = $request->search;
            $searchResults = $this->performGlobalSearch($searchQuery);
        }

        // Get statistics for dashboard cards (respecting scope permissions)
        $stats = $this->getStats($user);

        return view('management.index', compact('stats', 'searchResults', 'searchQuery'));
    }

    /**
     * Get statistics respecting user's scope permissions
     */
    private function getStats($user)
    {
        // Devices stats
        $devicesQuery = Device::query();
        if (!$user->can('devices.scope.all')) {
            $devicesQuery->whereHas('vehicle', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        }
        
        // Vehicles stats
        $vehiclesQuery = Vehicle::query();
        if (!$user->can('vehicles.scope.all')) {
            $vehiclesQuery->where('owner_id', $user->id);
        }
        
        // Users stats
        $usersQuery = User::query();
        if (!$user->can('users.scope.all')) {
            $usersQuery->where('created_by', $user->id);
        }
        
        // Drivers stats
        $driversQuery = Driver::query();
        if (!$user->can('drivers.scope.all')) {
            $driversQuery->where('user_id', $user->id);
        }

        return [
            'devices' => [
                'total' => (clone $devicesQuery)->count(),
                'active' => (clone $devicesQuery)->where('status', 'active')->count(),
                'inactive' => (clone $devicesQuery)->where('status', 'inactive')->count(),
            ],
            'vehicles' => [
                'total' => (clone $vehiclesQuery)->count(),
                'active' => (clone $vehiclesQuery)->where('status', 'active')->count(),
                'maintenance' => (clone $vehiclesQuery)->where('status', 'maintenance')->count(),
            ],
            'users' => [
                'total' => (clone $usersQuery)->count(),
                'active' => (clone $usersQuery)->where('status', 'active')->count(),
                'admins' => (clone $usersQuery)->role(['admin', 'super-admin'])->count(),
            ],
            'drivers' => [
                'total' => (clone $driversQuery)->count(),
                'active' => (clone $driversQuery)->where('status', 'active')->count(),
                'on_leave' => (clone $driversQuery)->where('status', 'on_leave')->count(),
            ],
        ];
    }

    /**
     * Perform global search across all entities WITH PROPER AUTHORIZATION
     */
    private function performGlobalSearch($query)
    {
        $results = [
            'devices' => collect(),
            'vehicles' => collect(),
            'users' => collect(),
            'drivers' => collect(),
        ];

        $user = auth()->user();

        // === DEVICES ===
        $devicesQuery = Device::where(function($q) use ($query) {
            $q->where('device_name', 'like', "%{$query}%")
              ->orWhere('serial_number', 'like', "%{$query}%")
              ->orWhere('id', $query);
        });

        // Apply scope: if user doesn't have 'all' permission, show only own
        if (!$user->can('devices.scope.all')) {
            $devicesQuery->whereHas('vehicle', function($vehicleQuery) use ($user) {
                $vehicleQuery->where('owner_id', $user->id);
            });
        }

        if ($user->can('devices.view')) {
            $results['devices'] = $devicesQuery->with('vehicle')->limit(10)->get();
        }

        // === VEHICLES ===
        $vehiclesQuery = Vehicle::where(function($q) use ($query) {
            $q->where('registration_number', 'like', "%{$query}%")
              ->orWhere('vin', 'like', "%{$query}%")
              ->orWhere('make', 'like', "%{$query}%")
              ->orWhere('model', 'like', "%{$query}%")
              ->orWhere('id', $query);
        });

        // Apply scope
        if (!$user->can('vehicles.scope.all')) {
            $vehiclesQuery->where('owner_id', $user->id);
        }

        if ($user->can('vehicles.view')) {
            $results['vehicles'] = $vehiclesQuery->with('primaryDriver')->limit(10)->get();
        }

        // === USERS ===
        $usersQuery = User::where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%")
              ->orWhere('department', 'like', "%{$query}%")
              ->orWhere('id', $query);
        });

        // Apply scope
        if (!$user->can('users.scope.all')) {
            $usersQuery->where('created_by', $user->id);
        }

        if ($user->can('users.view')) {
            $results['users'] = $usersQuery->with('roles')->limit(10)->get();
        }

        // === DRIVERS ===
        $driversQuery = Driver::where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('license_number', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('id', $query);
        });

        // Apply scope
        if (!$user->can('drivers.scope.all')) {
            $driversQuery->where('user_id', $user->id);
        }

        if ($user->can('drivers.view')) {
            $results['drivers'] = $driversQuery->with('vehicles')->limit(10)->get();
        }

        // Calculate totals
        $results['total_found'] = 
            $results['devices']->count() +
            $results['vehicles']->count() +
            $results['users']->count() +
            $results['drivers']->count();

        return $results;
    }
}