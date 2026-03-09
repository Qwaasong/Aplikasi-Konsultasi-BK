<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Actions\Logout;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    Volt::route('login', 'pages.auth.login')
        ->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('logout', function (Logout $logout) {
        $logout();
        return redirect('/');
    })->name('logout');
});
