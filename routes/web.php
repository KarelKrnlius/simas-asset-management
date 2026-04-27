<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

use App\Models\User;

use App\Http\Controllers\LoanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;

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
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

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


// =====================
// LOGOUT
// =====================
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');


// =====================
// AUTH AREA
// =====================
Route::middleware(['auth'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // MENU
    Route::get('/layanan', [DashboardController::class, 'layanan'])->name('layanan');
    Route::get('/peminjaman', [LoanController::class, 'index'])->name('peminjaman');
    Route::get('/riwayat', [DashboardController::class, 'riwayat'])->name('riwayat');

    // =====================
    // 🔥 ASSET
    // =====================
    Route::resource('assets', AssetController::class);
    Route::get('/assets/next-code', [AssetController::class, 'getNextCode']);

    // =====================
    // 🔥 CATEGORY
    // =====================
    Route::resource('categories', CategoryController::class);

    // =====================
    // 🔥 MASTER USER PRO
    // =====================
    Route::resource('users', UserController::class);

    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset');
    Route::post('/users/{id}/toggle', [UserController::class, 'toggle'])->name('users.toggle');

    // =====================
    // PROFILE
    // =====================
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

});