<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for each resource
        $resources = [
            'dashboard', 'gps', 'obd', 'system', 'ups', 
            'network', 'modem', 'bluetooth', 'tachograph', 
            'logs', 'settings', 'users'
        ];

        foreach ($resources as $resource) {
            Permission::create(['name' => "view_{$resource}"]);
            Permission::create(['name' => "edit_{$resource}"]);
            Permission::create(['name' => "delete_{$resource}"]);
        }

        // Create roles
        $superAdmin = Role::create(['name' => 'super-admin']);
        $admin = Role::create(['name' => 'admin']);
        $superuser = Role::create(['name' => 'superuser']);
        $user = Role::create(['name' => 'user']);

        // Super-Admin: All permissions
        $superAdmin->givePermissionTo(Permission::all());

        // Admin: Almost all permissions
        $admin->givePermissionTo(Permission::all());
        $admin->revokePermissionTo(['delete_users', 'edit_users']); // Cannot manage users

        // Superuser: View all, edit some (will be customized per client)
        $superuser->givePermissionTo([
            'view_dashboard', 'view_gps', 'view_obd', 'view_system',
            'view_ups', 'view_network', 'view_modem', 'view_bluetooth',
            'view_tachograph', 'view_logs'
        ]);

        // User (Driver): View only basic features
        $user->givePermissionTo([
            'view_dashboard', 'view_gps', 'view_tachograph'
        ]);
    }
}