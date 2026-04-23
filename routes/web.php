<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

use App\Models\User;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// HOME → LOGIN
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


// =====================
// FORGOT PASSWORD
// =====================

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


// =====================
// RESET PASSWORD
// =====================

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
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');


// =====================
// AUTH AREA
// =====================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/layanan', [DashboardController::class, 'layanan'])->name('layanan');
    Route::get('/aset', [DashboardController::class, 'aset'])->name('aset');
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman');
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