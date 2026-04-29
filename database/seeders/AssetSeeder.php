<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = DB::table('categories')->pluck('id', 'name')->toArray();

        $assets = [
            [
                'category_id' => $categories['Laptop'] ?? 1,
                'name' => 'Laptop',
                'code' => 'LPT-001',
                'description' => 'Laptop Dell',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $categories['Laptop'] ?? 1,
                'name' => 'Laptop',
                'code' => 'LPT-002',
                'description' => 'Laptop HP',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'category_id' => $categories['Monitor'] ?? 2,
                'name' => 'Monitor',
                'code' => 'MTR-001',
                'description' => 'Monitor LG',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $categories['Monitor'] ?? 2,
                'name' => 'Monitor Samsung S24F350',
                'code' => 'MTR-002',
                'description' => 'Monitor Samsung',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('assets')->insert($assets);
    }
}
