<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetReturnController;

    // HOME → LOGIN
    Route::get('/', function () { return redirect()->route('login');});

    // AUTHENTICATION ROUTES
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // AUTH AREA
    Route::middleware(['auth', 'nocache'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // MENU
    Route::get('/layanan', [DashboardController::class, 'layanan'])->name('layanan');
    Route::get('/peminjaman', [LoanController::class, 'index'])->name('peminjaman');
    Route::post('/peminjaman', [LoanController::class, 'store'])->name('peminjaman.store');
    Route::get('/riwayat', [DashboardController::class, 'riwayat'])->name('riwayat');
    
    // Assets Resource Routes (Admin Only)
    Route::resource('assets', AssetController::class)->middleware('role:admin');
    Route::get('/assets/next-code', [AssetController::class, 'getNextCode'])->middleware('role:admin');
    Route::post('/assets/bulk-delete', [AssetController::class, 'bulkDelete'])->middleware('role:admin');
    
    // Categories Resource Routes (Admin Only)
    Route::resource('categories', CategoryController::class)->middleware('role:admin');

    // Asset Return Routes (Admin Only)
    Route::get('/pengembalian', [AssetReturnController::class, 'index'])->middleware('role:admin')->name('pengembalian');
    Route::post('/pengembalian', [AssetReturnController::class, 'store'])->middleware('role:admin')->name('pengembalian.store');

    // MASTER USER (Admin Only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset');
    });

    // PROFILE (All Authenticated Users)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
});