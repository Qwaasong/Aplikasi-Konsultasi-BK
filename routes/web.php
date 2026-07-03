<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('test', 'pages.test');

//Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Volt::route('admin/dashboard', 'pages.admin.dashboard')->name('admin.dashboard');
    Volt::route('admin/konsultasi', 'pages.admin.konsultasi.index')->name('admin.konsultasi.index');
    Volt::route('admin/siswa', 'pages.admin.siswa.index')->name('admin.siswa.index');
    Volt::route('admin/user', 'pages.admin.user.index')->name('admin.user.index');
    Volt::route('/admin/konsultasi/{id}/detail', 'pages.admin.konsultasi.detail')
        ->name('admin.konsultasi.detail');
});

//Guru / Konselor
Route::middleware(['auth', 'role:Guru_BK'])->group(function () {
    Volt::route('konselor/dashboard', 'pages.konselor.dashboard')->name('konselor.dashboard');
    Volt::route('konselor/konsultasi', 'pages.konselor.konsultasi.index')->name('konselor.konsultasi.index');
    Volt::route('/konselor/konsultasi/{id}/detail', 'pages.konselor.konsultasi.detail')
        ->name('konselor.konsultasi.detail');
});

require __DIR__ . '/auth.php';