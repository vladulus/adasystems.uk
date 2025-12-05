<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RootUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('ada.root_email');

        if (! $email) {
            return;
        }

        // Parola pentru root: din .env sau fallback (trebuie schimbată!)
        $password = env('ADA_ROOT_PASSWORD', 'Vla0 M!n3a');

        // Căutăm sau creăm userul root
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'System Owner',
                'password' => Hash::make($password),
                'is_active' => true,
            ]
        );

        // Ne asigurăm că are rol de super-admin
        $role = Role::firstOrCreate(['name' => 'super-admin']);

        if (! $user->hasRole($role->name)) {
            $user->assignRole($role);
        }
    }
}
