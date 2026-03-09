<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataSiswa;

class DataSiswaSeeder extends Seeder
{
    public function run()
    {
        $siswas = [
            ['nis' => 21001, 'nama' => 'Ahmad Zaki', 'kelas' => 12, 'jenis_kelamin' => 'Laki-laki', 'jurusan' => 'RPL', 'periode_ajaran' => '2023/2024'],
            ['nis' => 21002, 'nama' => 'Budi Santoso', 'kelas' => 12, 'jenis_kelamin' => 'Laki-laki', 'jurusan' => 'TKJ', 'periode_ajaran' => '2023/2024'],
            ['nis' => 21003, 'nama' => 'Citra Lestari', 'kelas' => 12, 'jenis_kelamin' => 'Perempuan', 'jurusan' => 'MM', 'periode_ajaran' => '2023/2024'],
            ['nis' => 21004, 'nama' => 'Dina Amalia', 'kelas' => 11, 'jenis_kelamin' => 'Perempuan', 'jurusan' => 'RPL', 'periode_ajaran' => '2023/2024'],
            ['nis' => 21005, 'nama' => 'Eko Prasetyo', 'kelas' => 12, 'jenis_kelamin' => 'Laki-laki', 'jurusan' => 'TKJ', 'periode_ajaran' => '2023/2024'],
            ['nis' => 21006, 'nama' => 'Farhan Kamil', 'kelas' => 10, 'jenis_kelamin' => 'Laki-laki', 'jurusan' => 'RPL', 'periode_ajaran' => '2023/2024'],
            ['nis' => 21007, 'nama' => 'Gita Permata', 'kelas' => 11, 'jenis_kelamin' => 'Perempuan', 'jurusan' => 'MM', 'periode_ajaran' => '2023/2024'],
            ['nis' => 21008, 'nama' => 'Hadi Wijaya', 'kelas' => 12, 'jenis_kelamin' => 'Laki-laki', 'jurusan' => 'RPL', 'periode_ajaran' => '2023/2024'],
            ['nis' => 21009, 'nama' => 'Indah Sari', 'kelas' => 10, 'jenis_kelamin' => 'Perempuan', 'jurusan' => 'TKJ', 'periode_ajaran' => '2023/2024'],
            ['nis' => 21010, 'nama' => 'Joko Susilo', 'kelas' => 12, 'jenis_kelamin' => 'Laki-laki', 'jurusan' => 'MM', 'periode_ajaran' => '2023/2024'],
        ];

        foreach ($siswas as $siswa) {
            DataSiswa::create($siswa);
        }
    }
}