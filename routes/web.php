<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\WordExportController;

Volt::route('test', 'pages.test');

// ── Export Word ──────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/konsultasi/{id}/export/{template}', [WordExportController::class, 'export'])
        ->name('konsultasi.export');
});

//Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Volt::route('admin/dashboard', 'pages.admin.dashboard')->name('admin.dashboard');
    Volt::route('admin/konsultasi', 'pages.admin.konsultasi.index')->name('admin.konsultasi.index');
    Volt::route('admin/siswa', 'pages.admin.siswa.index')->name('admin.siswa.index');
    Volt::route('admin/user', 'pages.admin.user.index')->name('admin.user.index');
    Volt::route('/admin/konsultasi/{id}/detail', 'pages.admin.konsultasi.detail')
        ->name('admin.konsultasi.detail');
});

// Guru / Konselor
Route::middleware(['auth', 'role:guru_bk'])->group(function () {

    // Dashboard
    Volt::route('konselor/dashboard', 'pages.konselor.dashboard')->name('konselor.dashboard');
    // Konsultasi (lama)
    Volt::route('konselor/konsultasi', 'pages.konselor.konsultasi.index')->name('konselor.konsultasi.index');
    Volt::route('konselor/konsultasi/{id}/detail', 'pages.konselor.konsultasi.detail')->name('konselor.konsultasi.detail');

    // Pembinaan Siswa
    Volt::route('konselor/kehadiran-siswa', 'pages.konselor.kehadiran-siswa.index')->name('konselor.kehadiran-siswa.index');
    Volt::route('konselor/layanan-konseling/individu', 'pages.konselor.layanan-konseling.individu')->name('konselor.layanan-konseling.individu');
    Volt::route('konselor/layanan-konseling/kelompok', 'pages.konselor.layanan-konseling.kelompok')->name('konselor.layanan-konseling.kelompok');
    Volt::route('konselor/kunjungan-rumah', 'pages.konselor.kunjungan-rumah.index')->name('konselor.kunjungan-rumah.index');
    Volt::route('konselor/kunjungan-rumah/{id}/detail','pages.konselor.kunjungan-rumah.detail')->name('konselor.home-visit.detail');
    Volt::route('konselor/alih-tangan-kasus', 'pages.konselor.alih-tangan-kasus.index')->name('konselor.alih-tangan-kasus.index');
    Volt::route('konselor/konferensi-kasus', 'pages.konselor.konferensi-kasus.index')->name('konselor.konferensi-kasus.index');
    Volt::route('konselor/peminatan', 'pages.konselor.peminatan.index')->name('konselor.peminatan.index');
});

require __DIR__ . '/auth.php';