<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController; // Sesuaikan dengan nama controller loginmu

// 1. Halaman awal (Biasanya diarahkan ke Login dulu)
Route::get('/', function () {
    return view('auth.login'); 
});

// 2. Route Dashboard (WAJIB pakai middleware 'auth')
// Middleware ini yang akan otomatis mengusir user ke halaman login kalau belum terautentikasi
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth'); 

// 3. Tambahan Route sesuai menu di Navbar-mu
Route::middleware(['auth'])->group(function () {
    Route::get('/layanan', [DashboardController::class, 'layanan'])->name('layanan');
    Route::get('/aset', [DashboardController::class, 'aset'])->name('aset');
    Route::get('/peminjaman', [DashboardController::class, 'peminjaman'])->name('peminjaman');
    Route::get('/riwayat', [DashboardController::class, 'riwayat'])->name('riwayat');
});