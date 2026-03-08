<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Volt::route('test', 'pages.test');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

//Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Volt::route('admin/dashboard', 'pages.admin.dashboard')->name('admin.dashboard');
    Volt::route('admin/konsultasi', 'pages.admin.konsultasi.index')->name('admin.konsultasi.index');
    Volt::route('admin/siswa', 'pages.admin.siswa.index')->name('admin.siswa.index');
    Volt::route('admin/user', 'pages.admin.user.index')->name('admin.user.index');
});

//Guru / Konselor
Route::middleware(['auth', 'role:guru'])->group(function () {
    Volt::route('konselor/dashboard', 'pages.konselor.dashboard')->name('konselor.dashboard');
    Volt::route('konselor/konsultasi', 'pages.konselor.konsultasi.index')->name('konselor.konsultasi.index');
});

require __DIR__ . '/auth.php';