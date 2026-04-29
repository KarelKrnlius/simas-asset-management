<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

use App\Models\User;

use App\Http\Controllers\LoanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetReturnController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =====================
// HOME → LOGIN
// =====================
Route::get('/', function () {
    return redirect()->route('login');
});

// =====================
// LOGIN
// =====================
Route::get('/login', function () {
    // If user is already authenticated, redirect to dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->withErrors(['email' => 'Email tidak ditemukan']);
    }

    if (!Hash::check($request->password, $user->password)) {
        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    // 🔥 TAMBAHAN PRO (USER NONAKTIF GA BISA LOGIN)
    if (!$user->is_active) {
        return back()->withErrors(['email' => 'Akun anda nonaktif']);
    }

    Auth::login($user);

    return redirect()->route('dashboard');

})->name('login.process');

// =====================
// FORGOT PASSWORD
// =====================
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function (Request $request) {

    $request->validate([
        'email' => 'required|email'
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with('status', 'Link reset sudah dikirim!')
        : back()->withErrors(['email' => 'Email tidak ditemukan']);

})->name('password.email');

// =====================
// RESET PASSWORD
// =====================
Route::get('/reset-password/{token}', function (Request $request, $token) {
    $email = $request->query('email');
    return view('auth.reset-password', ['token' => $token, 'email' => $email]);
})->name('password.reset');

Route::post('/reset-password', function (Request $request) {

    // Debug: Log semua input
    \Log::info('Reset password attempt:', [
        'email' => $request->email,
        'token' => $request->token,
        'password_length' => strlen($request->password),
        'password_confirmed' => $request->password === $request->password_confirmation
    ]);

    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    // Debug: Cari user berdasarkan email
    $user = \App\Models\User::where('email', $request->email)->first();
    
    if (!$user) {
        \Log::error('User not found for email:', ['email' => $request->email]);
        return back()->withErrors(['email' => 'User tidak ditemukan']);
    }
    
    \Log::info('User found:', ['user_id' => $user->id, 'user_email' => $user->email]);

    // Cek token validitas (sederhana - bypass untuk testing)
    // TODO: Implement proper token validation
    
    // Update password langsung
    try {
        $user->password = Hash::make($request->password);
        $user->save();
        
        \Log::info('Password updated successfully for user:', ['user_id' => $user->id]);
        
        // Logout user yang sedang login (jika ada)
        Auth::logout();
        
        // Invalidate semua session
        session()->invalidate();
        session()->regenerateToken();
        
        return redirect('/login')->with('status', 'Password berhasil diubah! Silakan login kembali.');
        
    } catch (\Exception $e) {
        \Log::error('Error updating password:', ['error' => $e->getMessage()]);
        return back()->withErrors(['email' => 'Gagal mengubah password: ' . $e->getMessage()]);
    }

})->name('password.update');


// =====================
// LOGOUT
// =====================
Route::post('/logout', function () {
    // Invalidate current session
    Auth::logout();
    
    // Clear all session data
    session()->invalidate();
    session()->regenerateToken();
    
    // Clear cookies
    Cookie::queue(Cookie::forget('laravel_session'));
    Cookie::queue(Cookie::forget('XSRF-TOKEN'));
    
    return redirect('/login')->with('status', 'Anda telah berhasil logout.');
})->name('logout');


// =====================
// AUTH AREA
// =====================
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

    // =====================
    // 🔥 MASTER USER (Admin Only)
    // =====================
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset');
        Route::post('/users/{id}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::get('/users/{id}/history', [UserController::class, 'getHistory'])->name('users.history');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    });

    // =====================
    // PROFILE (All Authenticated Users)
    // =====================
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/logout-all-devices', [ProfileController::class, 'logoutAllDevices'])->name('profile.logout-all-devices');

});