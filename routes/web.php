<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// =====================
// REDIRECT UTAMA
// =====================
Route::get('/', function () {
    return redirect()->route('login');
});

// =====================
// LOGIN & AUTH
// =====================
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();
    if ($user && Hash::check($request->password, $user->password)) {
        Auth::login($user);
        return redirect()->route('dashboard');
    }
    return back()->withErrors(['email' => 'Email atau password salah']);
})->name('login.process');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// =====================
// PROTECTED (WAJIB LOGIN)
// =====================
Route::middleware(['auth'])->group(function () {

    // DASHBOARD UTAMA
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PROFILE (Sesuai kode temen lo)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');

    // =====================
    // ROUTE ADMIN (Sesuai Nama di Blade)
    // =====================
    // Kita pake prefix admin biar rapih
    Route::name('admin.')->group(function () {
        Route::get('/users', function () { return view('users.index'); })->name('users');
        Route::get('/users/create', function () { return view('users.create'); })->name('users.create');
        
        Route::get('/assets', function () { return view('assets.index'); })->name('assets');
        Route::get('/assets/create', function () { return view('assets.create'); })->name('assets.create');
    });

    // =====================
    // ROUTE STAFF / LAYANAN
    // =====================
    Route::get('/layanan', [DashboardController::class, 'layanan'])->name('layanan');
    Route::get('/peminjaman', [DashboardController::class, 'peminjaman'])->name('peminjaman');
    Route::get('/riwayat', [DashboardController::class, 'riwayat'])->name('riwayat');
});