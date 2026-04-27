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
// LOGIN & AUTH
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

    Auth::login($user);

    return redirect()->route('dashboard');

})->name('login.process');



// FORM
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');


// KIRIM EMAIL
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


// FORM RESET
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');


// SIMPAN PASSWORD BARU
Route::post('/reset-password', function (Request $request) {

    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect('/login')->with('status', 'Password berhasil diubah!')
        : back()->withErrors(['email' => 'Gagal reset password']);

})->name('password.update');


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

    // DASHBOARD UTAMA
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // MENU DASHBOARD
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
    // PROFILE
    // =====================
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    
    Route::get('/users', function () {
        return view('users.index');
    })->name('users');

    Route::get('/users/create', function () {
        return view('users.create');
    })->name('users.create');

});