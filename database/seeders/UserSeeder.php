<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a demo admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@adasystems.uk',
            'password' => Hash::make('password123'), // Change this in production!
            'email_verified_at' => now(),
        ]);

        // Create a demo user
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@adasystems.uk',
            'password' => Hash::make('demo123'), // Change this in production!
            'email_verified_at' => now(),
        ]);
    }
}
