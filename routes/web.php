<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PeminjamanController;

Route::get('/', function () {
    return view('welcome');
});

// AUTH SIMPLE
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    return back()->with('status', 'Link reset sudah dikirim ke email kamu');
})->name('password.email');

// PEMINJAMAN
Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');