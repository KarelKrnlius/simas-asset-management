<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Proteksi Auth (Balikin dari file lama lo)
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // 2. Statistik Dashboard (Lengkap untuk Admin & Staff)
        $stats = [
            'total'       => Asset::count(),
            'available'   => Asset::where('status', 'Tersedia')->count(),
            'loaned'      => Asset::where('status', 'Dipinjam')->count(),
            'maintenance' => Asset::where('status', 'Maintenance')->count(),
            'rusak'       => Asset::where('status', 'Rusak')->count(),
        ];

        // 3. Logic Grafik Maintenance untuk PostgreSQL (Balikin dari file lama)
        $chartRaw = Asset::select(
            DB::raw("COUNT(*) as count"), 
            DB::raw("to_char(created_at, 'Mon') as month"),
            DB::raw("EXTRACT(MONTH FROM created_at) as month_num")
        )
        ->where('status', 'Maintenance')
        ->groupBy('month', 'month_num')
        ->orderBy('month_num')
        ->get();

        $chartData = [
            'labels' => $chartRaw->pluck('month')->toArray(),
            'values' => $chartRaw->pluck('count')->toArray(),
        ];

        // 4. Ambil Data Aset (Buat List Gallery Staff & Recent Activity Admin)
        // Ambil 6 data biar grid-nya penuh dan elegan
        $recentAssets = Asset::latest()->take(6)->get();

        // 5. Kirim semua data ke SATU view utama
        return view('dashboard', compact('user', 'stats', 'chartData', 'recentAssets'));
    }

    // Placeholder route agar tidak error saat navigasi diklik
    public function layanan() { return view('layanan'); }
    public function aset() { return view('assets.index'); }
    public function peminjaman() { return view('peminjaman'); }
    public function riwayat() { return view('riwayat'); }
} 