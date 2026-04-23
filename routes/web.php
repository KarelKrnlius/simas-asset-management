<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ResetPasswordController;

// Route untuk menampilkan halaman (saat link di email diklik)
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Route untuk proses update (yang dipanggil tombol "MASUK" di form)
Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('password.update');

Route::get('/', function () {
    return view('welcome');
});
