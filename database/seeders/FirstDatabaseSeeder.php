<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pegawai;
use App\Models\Sekolah;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\KategoriKasus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FirstDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────
        // 1. BUAT USER (idempoten via email unik)
        // ─────────────────────────────────────────

        $admin = User::firstOrCreate(
            ['email' => 'admin@sekolah.sch.id'],
            [
                'role' => 'admin',
                'nama' => 'Administrator Utama',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'jenis_kelamin' => 'L',
                'no_hp' => '081234567890',
                'foto' => '',
                'status' => 'aktif',
            ]
        );

        $konselor = User::firstOrCreate(
            ['email' => 'budi@sekolah.sch.id'],
            [
                'role' => 'guru_bk',
                'nama' => 'Budi Guru BK',
                'username' => 'konselor1',
                'password' => Hash::make('password123'),
                'jenis_kelamin' => 'L',
                'no_hp' => '081234567891',
                'foto' => '',
                'status' => 'aktif',
            ]
        );

        // ─────────────────────────────────────────
        // 2. BUAT PEGAWAI (idempoten via nip unik)
        // ─────────────────────────────────────────

        Pegawai::firstOrCreate(
            ['nip' => '1987654321'],
            [
                'user_id' => $konselor->id,
                'jabatan' => 'Guru BK',
            ]
        );

        // ─────────────────────────────────────────
        // 3. BUAT SEKOLAH (idempoten via email unik)
        // ─────────────────────────────────────────

        $sekolah = Sekolah::firstOrCreate(
            ['email' => 'info@smkn1contoh.sch.id'],
            [
                'nama_sekolah' => 'SMK Negeri 1 Contoh',
                'alamat' => 'Jl. Pendidikan No. 1, Kota Contoh',
                'telepon' => '(021) 12345678',
                'logo' => 'logo-default.png',
            ]
        );

        // ─────────────────────────────────────────
        // 4. BUAT JURUSAN (idempoten via kode_jurusan + sekolah_id unik)
        // ─────────────────────────────────────────

        $rpl = Jurusan::firstOrCreate(
            ['sekolah_id' => $sekolah->id, 'kode_jurusan' => 1],
            ['nama_jurusan' => 'RPL']
        );

        $tkj = Jurusan::firstOrCreate(
            ['sekolah_id' => $sekolah->id, 'kode_jurusan' => 2],
            ['nama_jurusan' => 'TKJ']
        );

        $mm = Jurusan::firstOrCreate(
            ['sekolah_id' => $sekolah->id, 'kode_jurusan' => 3],
            ['nama_jurusan' => 'MM']
        );

        // ─────────────────────────────────────────
        // 5. BUAT KELAS (idempoten via nama_kelas unik per jurusan)
        // ─────────────────────────────────────────

        $tingkats = ['X', 'XI', 'XII'];
        $jurusans = [$rpl, $tkj, $mm];

        foreach ($jurusans as $jurusan) {
            foreach ($tingkats as $tingkat) {
                $namaKelas = $tingkat . ' ' . $jurusan->nama_jurusan;
                Kelas::firstOrCreate(
                    ['jurusan_id' => $jurusan->id, 'nama_kelas' => $namaKelas],
                    ['tingkat' => $tingkat]
                );
            }
        }

        // ─────────────────────────────────────────
        // 6. BUAT TAHUN AJARAN (idempoten via tahun + semester unik)
        // ─────────────────────────────────────────

        TahunAjaran::firstOrCreate(
            ['tahun' => '2025', 'semester' => 'Genap'],
            ['status_aktif' => true]
        );

        // ─────────────────────────────────────────
        // 7. BUAT KATEGORI KASUS (idempoten via nama_kategori unik)
        // ─────────────────────────────────────────

        $kategoris = ['Pribadi', 'Belajar', 'Karir', 'Sosial'];
        foreach ($kategoris as $nama) {
            KategoriKasus::firstOrCreate(
                ['nama_kategori' => $nama]
            );
        }
    }
}