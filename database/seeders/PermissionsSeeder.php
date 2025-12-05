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

        $permissions = [
            // Devices
            'devices.add',
            'devices.edit',
            'devices.delete',
            'devices.view',

            // Vehicles
            'vehicles.add',
            'vehicles.edit',
            'vehicles.delete',
            'vehicles.view',

            // Users
            'users.add',
            'users.edit',
            'users.delete',
            'users.view',

            // Drivers
            'drivers.add',
            'drivers.edit',
            'drivers.delete',
            'drivers.move',
            'drivers.view',

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

            // Settings
            'settings.access',
        ];

        // Creăm permisiunile dacă nu există deja
        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // Roluri de bază
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'superuser']); // client
        Role::firstOrCreate(['name' => 'user']);      // driver

        // Super-admin are TOATE permisiunile
        $superAdmin->syncPermissions(Permission::all());
    }
}
