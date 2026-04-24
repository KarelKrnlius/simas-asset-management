<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UserController;

use App\Models\Asset;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
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
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

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


// =====================
// FORGOT PASSWORD
// =====================
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function () {
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
// AREA LOGIN (AUTH)
// =====================
Route::middleware(['auth'])->group(function () {

    // =====================
    // DASHBOARD
    // =====================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/layanan', [DashboardController::class, 'layanan'])->name('layanan');
    Route::get('/aset', [DashboardController::class, 'aset'])->name('aset');
    Route::get('/peminjaman', [DashboardController::class, 'peminjaman'])->name('peminjaman');
    Route::get('/riwayat', [DashboardController::class, 'riwayat'])->name('riwayat');


    // =====================
    // 🔥 ASSET (CRUD + PAGINATION)
    // =====================
    Route::get('/assets', function () {
        $assets = Asset::with('category')->paginate(5);
        return view('assets.index', compact('assets'));
    })->name('assets');

    Route::get('/assets/create', function () {
        return view('assets.create');
    })->name('assets.create');

    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');

    Route::get('/assets/{id}/edit', function ($id) {
        $asset = Asset::findOrFail($id);
        return view('assets.edit', compact('asset'));
    })->name('assets.edit');

    Route::put('/assets/{id}', [AssetController::class, 'update'])->name('assets.update');

    Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->name('assets.destroy');


    // =====================
    // 🔥 USER (FIXED FULL CONTROLLER)
    // =====================

    // ❌ FIX: HAPUS closure (ini penyebab error kamu)
    Route::get('/users', [UserController::class, 'index'])->name('users');

    Route::get('/users/create', function () {
        return view('users.create');
    })->name('users.create');

    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    Route::get('/users/{id}/edit', function ($id) {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    })->name('users.edit');

    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

});