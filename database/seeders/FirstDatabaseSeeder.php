<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pegawai;
use App\Models\Sekolah;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\KategoriKonsultasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FirstDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────
        // 1. BUAT USER
        // ─────────────────────────────────────────

        // Admin
        $admin = User::create([
            'role' => 'Admin',
            'nama' => 'Administrator Utama',
            'username' => 'admin',
            'email' => 'admin@sekolah.sch.id',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-laki',
            'no_hp' => '081234567890',
            'foto' => '',
            'status' => 'Aktif',
        ]);

        // Konselor
        $konselor = User::create([
            'role' => 'Guru_BK',
            'nama' => 'Budi Guru BK',
            'username' => 'konselor1',
            'email' => 'budi@sekolah.sch.id',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-laki',
            'no_hp' => '081234567891',
            'foto' => '',
            'status' => 'Aktif',
        ]);

        // ─────────────────────────────────────────
        // 2. BUAT PEGAWAI (untuk konselor)
        // ─────────────────────────────────────────

        Pegawai::create([
            'user_id' => $konselor->id,
            'nip' => '1987654321',
            'jabatan' => 'Guru BK',
        ]);

        // ─────────────────────────────────────────
        // 3. BUAT SEKOLAH
        // ─────────────────────────────────────────

        $sekolah = Sekolah::create([
            'nama_sekolah' => 'SMK Negeri 1 Contoh',
            'alamat' => 'Jl. Pendidikan No. 1, Kota Contoh',
            'telepon' => '(021) 12345678',
            'email' => 'info@smkn1contoh.sch.id',
            'logo' => 'logo-default.png',
        ]);

        // ─────────────────────────────────────────
        // 4. BUAT JURUSAN
        // ─────────────────────────────────────────

        $rpl = Jurusan::create([
            'sekolah_id' => $sekolah->id,
            'kode_jurusan' => 1,
            'nama_jurusan' => 'RPL',
        ]);

        $tkj = Jurusan::create([
            'sekolah_id' => $sekolah->id,
            'kode_jurusan' => 2,
            'nama_jurusan' => 'TKJ',
        ]);

        $mm = Jurusan::create([
            'sekolah_id' => $sekolah->id,
            'kode_jurusan' => 3,
            'nama_jurusan' => 'MM',
        ]);

        // ─────────────────────────────────────────
        // 5. BUAT KELAS
        // ─────────────────────────────────────────

        $tingkats = ['X', 'XI', 'XII'];
        $jurusans = [$rpl, $tkj, $mm];

        foreach ($jurusans as $jurusan) {
            foreach ($tingkats as $tingkat) {
                Kelas::create([
                    'jurusan_id' => $jurusan->id,
                    'nama_kelas' => $tingkat . ' ' . $jurusan->nama_jurusan,
                    'tingkat' => $tingkat,
                ]);
            }
        }

        // ─────────────────────────────────────────
        // 6. BUAT TAHUN AJARAN
        // ─────────────────────────────────────────

        TahunAjaran::create([
            'tahun' => 2025,
            'semester' => 'Genap',
            'status' => 'Aktif',
        ]);

        // ─────────────────────────────────────────
        // 7. BUAT KATEGORI KONSULTASI
        // ─────────────────────────────────────────

        $kategoris = ['Pribadi', 'Belajar', 'Karir', 'Sosial'];
        foreach ($kategoris as $nama) {
            KategoriKonsultasi::create([
                'nama_kategori' => $nama,
            ]);
        }
    }
}