<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =====================
// HOME → LOGIN
// =====================
Route::get('/', function () {
    return redirect('/login');
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
        return redirect()->route('dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah'
    ]);

})->name('login.process');

// AUTH SIMPLE
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    return back()->with('status', 'Link reset sudah dikirim ke email kamu');
})->name('password.email');


// =====================
// LOGOUT (🔥 FIX ERROR KAMU)
// =====================
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');


// =====================
// DASHBOARD (WAJIB LOGIN)
// =====================
Route::middleware(['auth'])->group(function () {

    // DASHBOARD UTAMA
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // MENU DASHBOARD (punya temen kamu)
    Route::get('/layanan', [DashboardController::class, 'layanan'])->name('layanan');
    Route::get('/aset', [DashboardController::class, 'aset'])->name('aset');
    Route::get('/peminjaman', [DashboardController::class, 'peminjaman'])->name('peminjaman');
    Route::get('/riwayat', [DashboardController::class, 'riwayat'])->name('riwayat');


    // =====================
    // ASSET
    // =====================
    Route::get('/assets', function () {
        return view('assets.index');
    })->name('assets');

    Route::get('/assets/create', function () {
        return view('assets.create');
    })->name('assets.create');


    // =====================
    // USER
    // =====================
    Route::get('/users', function () {
        return view('users.index');
    })->name('users');

    Route::get('/users/create', function () {
        return view('users.create');
    })->name('users.create');

});