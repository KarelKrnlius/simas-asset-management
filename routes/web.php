<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =====================
// HOME
// =====================
Route::get('/', function () {
    return view('welcome');
});


// =====================
// LOGIN
// =====================

// halaman login
Route::get('/login', function () {
    return view('auth.login');
});

// 🔥 PROSES LOGIN (SUDAH NYAMBUNG DATABASE)
Route::post('/login', function (Request $request) {

    if (Auth::attempt([
        'email' => $request->email,
        'password' => $request->password
    ])) {
        return redirect('/assets'); // kalau berhasil login
    }

    return back()->withErrors([
        'email' => 'Email atau password salah'
    ]);

})->name('login');


// =====================
// ASSET
// =====================

// halaman daftar asset
Route::get('/assets', function () {
    return view('assets.index');
});

// halaman form tambah asset
Route::get('/assets/create', function () {
    return view('assets.create');
});


// =====================
// USER
// =====================

// halaman daftar user
Route::get('/users', function () {
    return view('users.index');
});

// halaman form tambah user
Route::get('/users/create', function () {
    return view('users.create');
});