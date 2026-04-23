<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\DashboardController;

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


// 🔥 PROSES LOGIN (FIX BCRYPT ERROR)
Route::post('/login', function (Request $request) {
<<<<<<< HEAD

    $user = User::where('email', $request->email)->first();

    // cek user & password manual
    if ($user && Hash::check($request->password, $user->password)) {

        Auth::login($user); // login manual
        return redirect()->route('dashboard');
=======
    
    // Debug: cek input
    \Log::info('Login attempt for email: ' . $request->email);
    
    // Cek user exists
    $user = \App\Models\User::where('email', $request->email)->first();
    if (!$user) {
        \Log::info('User not found: ' . $request->email);
        return back()->withErrors(['email' => 'Email tidak ditemukan']);
>>>>>>> origin/feature/dashboard-utama
    }
    
    \Log::info('User found: ' . $user->email . ', Active: ' . $user->is_active . ', Role: ' . $user->role_id);
    
    // Cek password manual
    if (\Hash::check($request->password, $user->password)) {
        \Log::info('Password match for: ' . $request->email);
        Auth::login($user);
        return redirect()->route('dashboard');
    } else {
        \Log::info('Password mismatch for: ' . $request->email);
    }
    
    return back()->withErrors(['email' => 'Email atau password salah']);

})->name('login.process');

// AUTH SIMPLE
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    return back()->with('status', 'Link reset sudah dikirim ke email kamu');
})->name('password.email');


// =====================
// LOGOUT
// =====================
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');


// =====================
// DASHBOARD (WAJIB LOGIN)
// =====================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/layanan', [DashboardController::class, 'layanan'])->name('layanan');
    Route::get('/aset', [DashboardController::class, 'aset'])->name('aset');
    Route::get('/peminjaman', [DashboardController::class, 'peminjaman'])->name('peminjaman');
    Route::get('/riwayat', [DashboardController::class, 'riwayat'])->name('riwayat');

    Route::get('/assets', function () {
        return view('assets.index');
    })->name('assets');

    Route::get('/assets/create', function () {
        return view('assets.create');
    })->name('assets.create');

    Route::get('/users', function () {
        return view('users.index');
    })->name('users');

    Route::get('/users/create', function () {
        return view('users.create');
    })->name('users.create');

});