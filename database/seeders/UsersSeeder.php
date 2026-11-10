<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(
            ['email' => 'admin@simas.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'role_id' => 1,
                'is_active' => true,
            ]
        );

        // Staff account
        User::updateOrCreate(
            ['email' => 'staff@simas.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('staff123'),
                'email_verified_at' => now(),
                'role_id' => 2,
                'is_active' => true,
            ]
        );
    }
}
