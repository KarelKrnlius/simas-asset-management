<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;

// =====================
// HOME
// =====================
Route::get('/', function () {
    return view('auth.login');
});

// =====================
// LOGIN
// =====================

// halaman login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// proses login
Route::post('/login', function (Request $request) {

    if (Auth::attempt([
        'email' => $request->email,
        'password' => $request->password
    ])) {
        return redirect('/dashboard'); // diarahkan ke dashboard
    }

    return back()->withErrors([
        'email' => 'Email atau password salah'
    ]);

});

// =====================
// DASHBOARD (PROTECTED)
// =====================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/layanan', [DashboardController::class, 'layanan'])->name('layanan');
    Route::get('/aset', [DashboardController::class, 'aset'])->name('aset');
    Route::get('/peminjaman', [DashboardController::class, 'peminjaman'])->name('peminjaman');
    Route::get('/riwayat', [DashboardController::class, 'riwayat'])->name('riwayat');

    // =====================
    // ASSET (punya temen kamu)
    // =====================
    Route::get('/assets', function () {
        return view('assets.index');
    });

    Route::get('/assets/create', function () {
        return view('assets.create');
    });

    // =====================
    // USER
    // =====================
    Route::get('/users', function () {
        return view('users.index');
    });

    Route::get('/users/create', function () {
        return view('users.create');
    });

});