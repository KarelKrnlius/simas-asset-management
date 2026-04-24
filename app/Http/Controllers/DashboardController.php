<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class DashboardController extends Controller
{
    // =====================
    // DASHBOARD HOME
    // =====================
    public function index()
    {
        // tetap get karena dashboard hanya statistik
        $assets = Asset::latest()->get();

        $total = $assets->count();
        $tersedia = $assets->where('status', 'tersedia')->count();
        $maintenance = $assets->where('status', 'maintenance')->count();
        $rusak = $assets->where('status', 'rusak')->count();

        return view('dashboard', compact(
            'assets',
            'total',
            'tersedia',
            'maintenance',
            'rusak'
        ));
    }


    // =====================
    // LAYANAN
    // =====================
    public function layanan()
    {
        return view('dashboard.layanan');
    }


    // =====================
    // ASSET LIST (AMAN)
    // =====================
    public function aset()
    {
        $assets = Asset::with('category')->paginate(10); // ✅ FIX

        return view('assets.index', compact('assets'));
    }


    // =====================
    // PEMINJAMAN
    // =====================
    public function peminjaman()
    {
        return view('dashboard.peminjaman');
    }


    // =====================
    // RIWAYAT
    // =====================
    public function riwayat()
    {
        return view('dashboard.riwayat');
    }
}