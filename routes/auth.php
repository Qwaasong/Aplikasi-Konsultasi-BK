<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Actions\Logout;

// 1. Route untuk yang BELUM login (Ini yang tadi hilang)
Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    Volt::route('login', 'pages.auth.login')
        ->name('login'); // Nama ini yang dicari oleh sistem
});

// 2. Route untuk yang SUDAH login
Route::middleware('auth')->group(function () {
    Route::get('logout', function (Logout $logout) {
        $logout();
        return redirect('/');
    })->name('logout');
});