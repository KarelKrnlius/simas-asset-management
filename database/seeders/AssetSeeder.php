<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('assets')->delete(); // Clear existing data

        $assets = [
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'Laptop Dell Latitude 5420',
                'code' => 'BRIN-01-000001',
                'description' => 'Laptop Dell dengan processor Intel Core i5, RAM 8GB, SSD 256GB',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'name' => 'Laptop HP ProBook 440 G9',
                'code' => 'BRIN-01-000002',
                'description' => 'Laptop HP dengan processor Intel Core i7, RAM 16GB, SSD 512GB',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'category_id' => 2,
                'name' => 'Monitor LG 24MP59G-P',
                'code' => 'BRIN-02-000003',
                'description' => 'Monitor LG 24 inch Full HD dengan IPS Panel',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'category_id' => 2,
                'name' => 'Monitor Samsung S24R350',
                'code' => 'BRIN-02-000004',
                'description' => 'Monitor Samsung 24 inch Full HD dengan Eye Care',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'category_id' => 3,
                'name' => 'Printer Canon PIXMA G3010',
                'code' => 'BRIN-03-000005',
                'description' => 'Printer Canon Ink Tank All-in-One',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'category_id' => 4,
                'name' => 'Keyboard Logitech K120',
                'code' => 'BRIN-04-000006',
                'description' => 'Keyboard USB Logitech dengan layout standar',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'category_id' => 5,
                'name' => 'Mouse Logitech M185',
                'code' => 'BRIN-05-000007',
                'description' => 'Mouse wireless Logitech dengan nano receiver',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('assets')->insert($assets);
    }
}
