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
use App\Http\Controllers\AssetLibraryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LoanCheckController;
use App\Http\Controllers\LoanHistoryController;

// HOME ? LOGIN
Route::get('/', function () { return redirect()->route('login');});

// PUBLIC ASSET DETAIL (Accessible without login for QR scanning)
Route::get('/asset/{code}', [AssetLibraryController::class, 'publicShow'])->name('asset.public');

// PUBLIC SCAN PAGE (Accessible without login for QR scanning)
Route::get('/scan', [AssetLibraryController::class, 'publicScan'])->name('scan.public');

// PUBLIC ASSET SEARCH (Accessible without login for QR scanning)
Route::get('/asset-library/search', [AssetLibraryController::class, 'searchByQrCode'])->name('asset-library.search');

// PUBLIC ASSET PHOTO PROXY (Serve RustFS photos through Laravel for external access)
Route::get('/asset-photo/{path}', [AssetLibraryController::class, 'servePhoto'])
    ->where('path', '.*')
    ->name('asset.photo');

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

    // PEMINJAMAN
    Route::get('/peminjaman', [LoanController::class, 'index'])->name('peminjaman');
    Route::post('/peminjaman', [LoanController::class, 'store'])->name('peminjaman.store');

    // RIWAYAT
    Route::get('/riwayat', [DashboardController::class, 'riwayat'])->name('riwayat');
    
    // RIWAYAT PEMINJAMAN (All Roles)
    Route::get('/riwayat-peminjaman', [LoanHistoryController::class, 'index'])->name('riwayat-peminjaman');
    Route::get('/riwayat-peminjaman/{id}', [LoanHistoryController::class, 'show'])->name('riwayat-peminjaman.show');
    Route::post('/riwayat-peminjaman/bulk-delete', [LoanHistoryController::class, 'bulkDelete'])->name('riwayat-peminjaman.bulkDelete');
    
    // PENGEMBALIAN
    Route::get('/pengembalian', [AssetReturnController::class, 'index'])->name('pengembalian');
    Route::post('/pengembalian', [AssetReturnController::class, 'store'])->name('pengembalian.store');
    
    // ASSET LIBRARY (All Authenticated Users)
    Route::get('/asset-library', [AssetLibraryController::class, 'index'])->name('asset-library.index');
    Route::get('/asset-library/scan', [AssetLibraryController::class, 'scan'])->name('asset-library.scan');
    Route::get('/asset-library/qr-generator', [AssetLibraryController::class, 'qrGenerator'])->name('asset-library.qr-generator');
    Route::get('/asset-library/{code}', [AssetLibraryController::class, 'showAsset'])->name('asset-library.show');
    
    // Assets Resource Routes (Admin Only)
    Route::resource('assets', AssetController::class)->middleware('role:admin');
    Route::get('/assets-export', [AssetController::class, 'export'])->middleware('role:admin')->name('assets.export');
    Route::get('/assets/next-code', [AssetController::class, 'getNextCode'])->middleware('role:admin');
    Route::post('/assets/bulk-delete', [AssetController::class, 'bulkDelete'])->middleware('role:admin');
    Route::get('/assets/{id}/history', [AssetController::class, 'history'])->middleware('role:admin')->name('assets.history');
    
    // Categories Resource Routes (Admin Only)
    Route::get('/categories/list', [CategoryController::class, 'list'])->middleware('role:admin');
    Route::resource('categories', CategoryController::class)->middleware('role:admin');

    // MASTER USER (Admin Only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users/{id}/history', [UserController::class, 'history'])->name('users.history');
        Route::resource('users', UserController::class);
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset');
    });

    // MASTER ROLE (Admin Only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('roles', RoleController::class)->except(['create','edit','show']);
        Route::post('/roles/bulk-delete', [RoleController::class, 'bulkDelete'])->name('roles.bulkDelete');
        Route::get('/roles-delete-all', [RoleController::class, 'deleteAll'])->name('roles.deleteAll');
    });

    // LOAN CHECK / PENGECEK PEMINJAMAN (Admin Only)
    Route::get('/pengecek-peminjaman', [LoanCheckController::class, 'index'])->middleware('role:admin')->name('pengecek-peminjaman');
    Route::get('/pengecek-peminjaman/{id}', [LoanCheckController::class, 'show'])->middleware('role:admin')->name('pengecek-peminjaman.show');
    Route::post('/pengecek-peminjaman/bulk-delete', [LoanCheckController::class, 'bulkDelete'])->middleware('role:admin')->name('pengecek-peminjaman.bulkDelete');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});