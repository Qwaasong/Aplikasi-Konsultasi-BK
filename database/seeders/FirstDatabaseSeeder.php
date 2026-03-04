<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DataSiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FirstDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Admin
        User::create([
            'role' => 'admin',
            'nama' => 'Administrator Utama',
            'username' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        // Buat Konselor
        User::create([
            'role' => 'konselor',
            'nama' => 'Budi Guru BK',
            'username' => 'konselor1',
            'password' => Hash::make('password123'),
        ]);

        // Contoh Data Siswa
        DataSiswa::create([
            'nis' => 12345,
            'nama' => 'Siswa Contoh',
            'kelas' => 12,
            'jenis_kelamin' => 'Laki-laki',
            'jurusan' => 'RPL',
            'periode_ajaran' => '2023/2024',
        ]);
    }
}