<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});