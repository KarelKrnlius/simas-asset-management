<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete(); // Clear existing data
        
        // Admin account
        User::create([
            'id' => 1,
            'name' => 'Admin User',
            'email' => 'admin@simas.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'role_id' => 1,
            'is_active' => true,
        ]);
            
        // Staff account
        User::create([
            'id' => 2,
            'name' => 'Staff User',
            'email' => 'staff@simas.com',
            'password' => Hash::make('staff123'),
            'email_verified_at' => now(),
            'role_id' => 2,
            'is_active' => true,
        ]);
    }
}
