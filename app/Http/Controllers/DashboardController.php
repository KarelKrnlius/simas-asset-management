<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil data statistik dari database
        $data = [
            'total'       => Asset::count(),
            'tersedia'    => Asset::where('status', 'Tersedia')->count(),
            'maintenance' => Asset::where('status', 'Maintenance')->count(),
            'rusak'       => Asset::where('status', 'Rusak')->count(),
            'assets'      => Asset::latest()->take(5)->get() // Ambil 5 aset terbaru
        ];

        return view('dashboard', $data);
    }
}