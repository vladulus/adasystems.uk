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

        // Global search functionality
        if ($request->filled('search')) {
            $searchQuery = $request->search;
            $searchResults = $this->performGlobalSearch($searchQuery);
        }

        // Get statistics for dashboard cards
        $stats = [
            'devices' => [
                'total' => Device::count(),
                'active' => Device::where('status', 'active')->count(),
                'inactive' => Device::where('status', 'inactive')->count(),
            ],
            'vehicles' => [
                'total' => Vehicle::count(),
                'active' => Vehicle::where('status', 'active')->count(),
                'maintenance' => Vehicle::where('status', 'maintenance')->count(),
            ],
            'users' => [
                'total' => User::count(),
                'active' => User::where('status', 'active')->count(),
                'admins' => User::role(['admin', 'super-admin'])->count(),
            ],
            'drivers' => [
                'total' => Driver::count(),
                'active' => Driver::where('status', 'active')->count(),
                'on_leave' => Driver::where('status', 'on_leave')->count(),
            ],
        ];

        return view('management.index', compact('stats', 'searchResults', 'searchQuery'));
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
            $q->where('imei', 'like', "%{$query}%")
              ->orWhere('serial_number', 'like', "%{$query}%")
              ->orWhere('name', 'like', "%{$query}%")
              ->orWhere('model', 'like', "%{$query}%")
              ->orWhere('manufacturer', 'like', "%{$query}%")
              ->orWhere('id', $query);
        });

        // Clients/Superusers only see their own devices
        if ($user->hasRole('client')) {
            $devicesQuery->whereHas('vehicle', function($vehicleQuery) use ($user) {
                $vehicleQuery->where('created_by', $user->id);
            });
        }

        if ($user->can('devices.view')) {
            $results['devices'] = $devicesQuery->with('vehicle')->limit(10)->get();
        }

        // === VEHICLES ===
        $vehiclesQuery = Vehicle::where(function($q) use ($query) {
            $q->where('plate_number', 'like', "%{$query}%")
              ->orWhere('vin', 'like', "%{$query}%")
              ->orWhere('make', 'like', "%{$query}%")
              ->orWhere('model', 'like', "%{$query}%")
              ->orWhere('id', $query);
        });

        // Clients/Superusers only see their own vehicles
        if ($user->hasRole('client')) {
            $vehiclesQuery->where('created_by', $user->id);
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

        // Clients can only see users they created
        if ($user->hasRole('client')) {
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

        // Clients only see their own drivers
        if ($user->hasRole('client')) {
            $driversQuery->where('created_by', $user->id);
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