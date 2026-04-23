<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Statistik Utama (Berdasarkan ERD Assets)
        $stats = [
            'total' => Asset::count(),
            'available' => Asset::where('status', 'Tersedia')->count(),
            'maintenance' => Asset::where('status', 'Maintenance')->count(),
            'broken' => Asset::where('status', 'Rusak')->count(),
        ];

        // Data Grafik Maintenance per Bulan (Logic untuk Grafik Baru)
        $chartData = Asset::select(DB::raw("COUNT(*) as count"), DB::raw("to_char(created_at, 'Mon') as month"))
            ->where('status', 'Maintenance')
            ->groupBy('month')
            ->get();

        // Data Recent Activity (Berdasarkan ERD Loans & Assets)
        $recentAssets = Asset::with('category')->latest()->take(5)->get();
        
        // Sesuaikan tampilan berdasarkan Role (Admin vs Staff)
        if ($user->role_id == 1) {
            return view('admin.dashboard', compact('stats', 'chartData', 'recentAssets'));
        } else {
            return view('staff.dashboard', compact('stats', 'recentAssets'));
        }
    }
}