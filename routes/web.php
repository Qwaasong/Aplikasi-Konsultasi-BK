<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\WordExportController;

// Landing Page — hanya untuk yang belum login
Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'guru_bk') {
            return redirect()->route('konselor.dashboard');
        }
    }

    return view('landing.index');
})->name('landing');

Volt::route('test', 'pages.test');

// ── Export Word ──────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/konsultasi/{id}/export/{template}', [WordExportController::class, 'export'])
        ->name('konsultasi.export');
});

//Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    // route lama
    Volt::route('admin/dashboard', 'pages.admin.dashboard')->name('admin.dashboard');
    Volt::route('admin/konsultasi', 'pages.admin.konsultasi.index')->name('admin.konsultasi.index');
    Volt::route('admin/siswa', 'pages.admin.siswa.index')->name('admin.siswa.index');
    Volt::route('admin/user', 'pages.admin.user.index')->name('admin.user.index');
    Volt::route('/admin/konsultasi/{id}/detail', 'pages.admin.konsultasi.detail')
        ->name('admin.konsultasi.detail');
    Volt::route('admin/kasus-bk', 'pages.admin.kasus-bk.index')->name('admin.kasus-bk.index');
    Volt::route('admin/kasus-bk/{id}/detail', 'pages.admin.kasus-bk.detail')->name('admin.kasus-bk.detail');

    // route baru
    Volt::route('/admin/kelola-user/siswa', 'pages.admin.kelola-user.siswa.index')->name('admin.kelola-user.siswa.index');
    Volt::route('/admin/kelola-user/pegawai', 'pages.admin.kelola-user.pegawai.index')->name('admin.kelola-user.pegawai.index');
    Volt::route('/admin/kelola-data/daftar-sekolah', 'pages.admin.kelola-data.daftar-sekolah.index')->name('admin.kelola-data.daftar-sekolah.index');
    Volt::route('/admin/kelola-data/daftar-jurusan', 'pages.admin.kelola-data.daftar-jurusan.index')->name('admin.kelola-data.daftar-jurusan.index');
    Volt::route('/admin/kelola-data/daftar-kelas', 'pages.admin.kelola-data.daftar-kelas.index')->name('admin.kelola-data.daftar-kelas.index');
    Volt::route('/admin/kelola-data/daftar-tahun-ajaran', 'pages.admin.kelola-data.daftar-tahun-ajaran.index')->name('admin.kelola-data.daftar-tahun-ajaran.index');
    Volt::route('/admin/log-kasus', 'pages.admin.log-kasus.index')->name('admin.log-kasus.index');
    Volt::route('/admin/rekap-absensi', 'pages.admin.rekap-absensi.index')->name('admin.rekap-absensi.index');
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
    Volt::route('konselor/layanan-konseling/individu/{id}/detail', 'pages.konselor.layanan-konseling.individu-detail')->name('konselor.layanan-konseling.individu.detail');
    Volt::route('konselor/layanan-konseling/kelompok', 'pages.konselor.layanan-konseling.kelompok')->name('konselor.layanan-konseling.kelompok');
    Volt::route('konselor/layanan-konseling/kelompok/{id}/detail', 'pages.konselor.layanan-konseling.kelompok-detail')->name('konselor.layanan-konseling.kelompok.detail');
    Volt::route('konselor/kunjungan-rumah', 'pages.konselor.kunjungan-rumah.index')->name('konselor.kunjungan-rumah.index');
    Volt::route('konselor/kunjungan-rumah/{id}/detail', 'pages.konselor.kunjungan-rumah.detail')->name('konselor.home-visit.detail');
    Volt::route('konselor/alih-tangan-kasus', 'pages.konselor.alih-tangan-kasus.index')->name('konselor.alih-tangan-kasus.index');
    Volt::route('konselor/konferensi-kasus', 'pages.konselor.konferensi-kasus.index')->name('konselor.konferensi-kasus.index');
    Volt::route('konselor/konferensi-kasus/{id}/detail', 'pages.konselor.konferensi-kasus.konferensi-kasus-detail')->name('konselor.konferensi-kasus.detail');
    Volt::route('konselor/pengunduran-diri', 'pages.konselor.pengunduran-diri.index')->name('konselor.pengunduran-diri.index');
    Volt::route('konselor/pengunduran-diri/{id}/detail', 'pages.konselor.pengunduran-diri.detail')->name('konselor.pengunduran-diri.detail');
    Volt::route('konselor/peminatan', 'pages.konselor.peminatan.index')->name('konselor.peminatan.index');

    // Asesmen
    Volt::route('konselor/asesmen', 'pages.konselor.asesmen.index')->name('konselor.asesmen.index');
    Volt::route('konselor/asesmen/akpd', 'pages.konselor.asesmen.akpd.index')->name('konselor.asesmen.akpd.index');
    Volt::route('konselor/asesmen/gaya-belajar', 'pages.konselor.asesmen.gaya-belajar.index')->name('konselor.asesmen.gaya-belajar.index');
    Volt::route('konselor/asesmen/dcm', 'pages.konselor.asesmen.dcm.index')->name('konselor.asesmen.dcm.index');
    Volt::route('konselor/asesmen/sosiometri', 'pages.konselor.asesmen.sosiometri.index')->name('konselor.asesmen.sosiometri.index');
    Volt::route('konselor/asesmen/sosiometri/form', 'pages.konselor.asesmen.sosiometri.form')->name('konselor.asesmen.sosiometri.form');
    Volt::route('konselor/asesmen/sosiometri/form','pages.konselor.asesmen.sosiometri.form')->name('konselor.asesmen.sosiometri.form');
    Volt::route('konselor/asesmen/tes-bakat-minat', 'pages.konselor.asesmen.tes-bakat-minat.index')->name('konselor.asesmen.tes-bakat-minat.index');
});

require __DIR__ . '/auth.php';
