<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Actions\Logout;

// 1. Logic halaman depan
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role == 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('konselor.dashboard');
    }
    return redirect()->route('login');
});

// 2. Route untuk yang BELUM login (Ini yang tadi hilang)
Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    Volt::route('login', 'pages.auth.login')
        ->name('login'); // Nama ini yang dicari oleh sistem
});

// 3. Route untuk yang SUDAH login
Route::middleware('auth')->group(function () {
    Route::get('logout', function (Logout $logout) {
        $logout();
        return redirect('/');
    })->name('logout');
});