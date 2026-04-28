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
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@simas.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
            
        // Staff account
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@simas.com',
            'password' => Hash::make('staff123'),
            'email_verified_at' => now(),
        ]);
    }
}
