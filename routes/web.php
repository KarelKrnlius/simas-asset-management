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
use App\Http\Controllers\RoleController;

// HOME → LOGIN
Route::get('/', function () { 
    return redirect()->route('login');
});

// AUTH
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// AREA LOGIN
Route::middleware(['auth', 'nocache'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // MENU
    Route::get('/layanan', [DashboardController::class, 'layanan'])->name('layanan');

    // PEMINJAMAN
    Route::get('/peminjaman', [LoanController::class, 'index'])->name('peminjaman');
    Route::post('/peminjaman', [LoanController::class, 'store'])->name('peminjaman.store');

    // RIWAYAT
    Route::get('/riwayat', [DashboardController::class, 'riwayat'])->name('riwayat');

    // Assets Resource Routes (Admin Only)
    Route::resource('assets', AssetController::class)->middleware('role:admin');
    Route::get('/assets/next-code', [AssetController::class, 'getNextCode'])->middleware('role:admin');
    Route::post('/assets/bulk-delete', [AssetController::class, 'bulkDelete'])->middleware('role:admin');
    Route::get('/test-photo/{id}', [AssetController::class, 'testPhoto'])->middleware('role:admin');
    
    // Categories Resource Routes (Admin Only)
    Route::resource('categories', CategoryController::class)->middleware('role:admin');

    Route::middleware('role:admin')->group(function () {

        // CRUD ROLE
        Route::resource('roles', RoleController::class)->except(['create','edit','show']);

        // BULK DELETE
        Route::post('/roles/bulk-delete', [RoleController::class, 'bulkDelete'])->name('roles.bulkDelete');

        // DELETE ALL
        Route::get('/roles-delete-all', [RoleController::class, 'deleteAll'])->name('roles.deleteAll');

    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);
        Route::post('/users/{id}/toggle', [UserController::class, 'toggle']);
    });

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

});