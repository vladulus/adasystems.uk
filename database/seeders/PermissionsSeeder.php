<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Curățăm cache-ul de permisiuni Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ===== TOATE PERMISIUNILE =====
        $permissions = [
            // Devices
            'devices.add',
            'devices.edit',
            'devices.delete',
            'devices.view',
            'devices.scope.own',
            'devices.scope.all',

            // Vehicles
            'vehicles.add',
            'vehicles.edit',
            'vehicles.delete',
            'vehicles.view',
            'vehicles.scope.own',
            'vehicles.scope.all',

            // Users
            'users.add',
            'users.edit',
            'users.delete',
            'users.view',
            'users.scope.own',
            'users.scope.all',

            // Drivers
            'drivers.add',
            'drivers.edit',
            'drivers.delete',
            'drivers.move',
            'drivers.view',
            'drivers.scope.own',
            'drivers.scope.all',

            // Dashboard
            'dashboard.access',
            'dashboard.gps',
            'dashboard.obd',
            'dashboard.system',
            'dashboard.ups',
            'dashboard.network',
            'dashboard.modem',
            'dashboard.bluetooth',
            'dashboard.tachograph',
            'dashboard.logs',
            'dashboard.scope.own',
            'dashboard.scope.all',

            // Settings
            'settings.access',

            // Permissions management
            'permissions.edit',
        ];

        // Creăm permisiunile
        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // ===== ROLURI (doar pentru ierarhie, fără permisiuni) =====
        Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'superuser']);
        Role::firstOrCreate(['name' => 'user']);

        // NU dăm permisiuni pe roluri!
        // Toate permisiunile se setează individual pe fiecare user din UI.
        // Root (vlad@impulsive.ro) are bypass hardcodat în policies.
    }
}