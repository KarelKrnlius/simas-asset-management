<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Laptop',
                'category_code' => '01',
                'description' => 'Perangkat laptop dan notebook untuk keperluan kerja',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Monitor',
                'category_code' => '02',
                'description' => 'Layar monitor desktop dan monitor eksternal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Printer',
                'category_code' => '03',
                'description' => 'Perangkat printer untuk mencetak dokumen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Keyboard',
                'category_code' => '04',
                'description' => 'Perangkat keyboard untuk input data',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Mouse',
                'category_code' => '05',
                'description' => 'Perangkat mouse untuk navigasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categories')->delete(); // Clear existing data
        DB::table('categories')->insert($categories);
    }
}
