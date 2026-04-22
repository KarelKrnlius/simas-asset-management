<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController; // Sesuaikan dengan nama controller loginmu

// 1. Halaman awal (Biasanya diarahkan ke Login dulu)
=======
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =====================
// HOME
// =====================
>>>>>>> origin/feature/halaman-login
Route::get('/', function () {
    return view('auth.login'); 
});

<<<<<<< HEAD
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
=======

// =====================
// LOGIN
// =====================

// halaman login
Route::get('/login', function () {
    return view('auth.login');
});

// 🔥 PROSES LOGIN (SUDAH NYAMBUNG DATABASE)
Route::post('/login', function (Request $request) {

    if (Auth::attempt([
        'email' => $request->email,
        'password' => $request->password
    ])) {
        return redirect('/assets'); // kalau berhasil login
    }

    return back()->withErrors([
        'email' => 'Email atau password salah'
    ]);

})->name('login');


// =====================
// ASSET
// =====================

// halaman daftar asset
Route::get('/assets', function () {
    return view('assets.index');
});

// halaman form tambah asset
Route::get('/assets/create', function () {
    return view('assets.create');
});


// =====================
// USER
// =====================

// halaman daftar user
Route::get('/users', function () {
    return view('users.index');
});

// halaman form tambah user
Route::get('/users/create', function () {
    return view('users.create');
>>>>>>> origin/feature/halaman-login
});