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
                'name' => 'Laptop Dell XPS 15',
                'code' => 'LPT-001',
                'qr_code' => 'QR-LPT-001-DELL-XPS15',
                'description' => 'Laptop Dell XPS 15 dengan spesifikasi tinggi untuk development',
                'brand' => 'Dell',
                'model' => 'XPS 15',
                'year' => 2023,
                'serial_number' => 'DLXPS15001',
                'location' => 'Ruang IT Lantai 2',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $categories['Laptop'] ?? 1,
                'name' => 'Laptop HP EliteBook',
                'code' => 'LPT-002',
                'qr_code' => 'QR-LPT-002-HP-ELITE',
                'description' => 'Laptop HP EliteBook untuk kebutuhan office',
                'brand' => 'HP',
                'model' => 'EliteBook 840',
                'year' => 2022,
                'serial_number' => 'HPEB84002',
                'location' => 'Ruang Meeting Lantai 1',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $categories['Monitor'] ?? 2,
                'name' => 'Monitor LG UltraWide',
                'code' => 'MTR-001',
                'qr_code' => 'QR-MTR-001-LG-ULTRA',
                'description' => 'Monitor LG UltraWide 34 inch untuk multitasking',
                'brand' => 'LG',
                'model' => '34UC89-W',
                'year' => 2023,
                'serial_number' => 'LGUW34001',
                'location' => 'Workstation Area A',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $categories['Monitor'] ?? 2,
                'name' => 'Monitor Samsung Curved',
                'code' => 'MTR-002',
                'qr_code' => 'QR-MTR-002-SAMSUNG-CURVE',
                'description' => 'Monitor Samsung Curved 27 inch untuk gaming',
                'brand' => 'Samsung',
                'model' => 'LC27F591FD',
                'year' => 2022,
                'serial_number' => 'SSCV27002',
                'location' => 'Workstation Area B',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Dipinjam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $categories['Keyboard'] ?? 3,
                'name' => 'Keyboard Mechanical Logitech',
                'code' => 'KBD-001',
                'qr_code' => 'QR-KBD-001-LOGITECH-MECH',
                'description' => 'Keyboard mechanical Logitech MX Keys',
                'brand' => 'Logitech',
                'model' => 'MX Keys',
                'year' => 2023,
                'serial_number' => 'LGMXK001',
                'location' => 'Developer Desk 1',
                'stock' => 1,
                'condition' => 'Baik',
                'status' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $categories['Mouse'] ?? 4,
                'name' => 'Mouse Wireless Logitech',
                'code' => 'MSE-001',
                'qr_code' => 'QR-MSE-001-LOGITECH-WIRELESS',
                'description' => 'Mouse wireless Logitech MX Master 3',
                'brand' => 'Logitech',
                'model' => 'MX Master 3',
                'year' => 2023,
                'serial_number' => 'LGMM3001',
                'location' => 'Developer Desk 2',
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
