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

    $user = User::where('email', $request->email)->first();

    // cek user & password manual
    if ($user && Hash::check($request->password, $user->password)) {

        Auth::login($user); // login manual
        return redirect()->route('dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah'
    ]);

})->name('login.process');


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