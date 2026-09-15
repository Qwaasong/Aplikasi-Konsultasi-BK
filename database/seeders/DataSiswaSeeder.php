<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DataSiswa;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;

class DataSiswaSeeder extends Seeder
{
    public function run()
    {
        $kelas = function (string $jurusan, string $tingkat): Kelas {
            return Kelas::query()
                ->where('tingkat', $tingkat)
                ->whereHas('jurusan', fn ($query) => $query->where('nama_jurusan', $jurusan))
                ->orderBy('id')
                ->firstOrFail();
        };

        $data = [
            ['nis' => 21001, 'nama' => 'Ahmad Zaki',  'jk' => 'L', 'kelas' => $kelas('RPL', 'XII'), 'alamat' => 'Jl. Merdeka No. 1'],
            ['nis' => 21002, 'nama' => 'Budi Santoso', 'jk' => 'L', 'kelas' => $kelas('TKJ', 'XII'), 'alamat' => 'Jl. Sudirman No. 2'],
            ['nis' => 21003, 'nama' => 'Citra Lestari','jk' => 'P', 'kelas' => $kelas('ANM', 'XII'), 'alamat' => 'Jl. Gatot Subroto No. 3'],
            ['nis' => 21004, 'nama' => 'Dina Amalia',  'jk' => 'P', 'kelas' => $kelas('RPL', 'XI'),  'alamat' => 'Jl. Ahmad Yani No. 4'],
            ['nis' => 21005, 'nama' => 'Eko Prasetyo', 'jk' => 'L', 'kelas' => $kelas('TKJ', 'XII'), 'alamat' => 'Jl. Diponegoro No. 5'],
            ['nis' => 21006, 'nama' => 'Farhan Kamil', 'jk' => 'L', 'kelas' => $kelas('RPL', 'X'),   'alamat' => 'Jl. Pahlawan No. 6'],
            ['nis' => 21007, 'nama' => 'Gita Permata', 'jk' => 'P', 'kelas' => $kelas('ANM', 'XI'),  'alamat' => 'Jl. Kemerdekaan No. 7'],
            ['nis' => 21008, 'nama' => 'Hadi Wijaya',  'jk' => 'L', 'kelas' => $kelas('RPL', 'XII'), 'alamat' => 'Jl. Imam Bonjol No. 8'],
            ['nis' => 21009, 'nama' => 'Indah Sari',   'jk' => 'P', 'kelas' => $kelas('TKJ', 'X'),   'alamat' => 'Jl. Siliwangi No. 9'],
            ['nis' => 21010, 'nama' => 'Joko Susilo',  'jk' => 'L', 'kelas' => $kelas('ANM', 'XII'), 'alamat' => 'Jl. Veteran No. 10'],
        ];

        foreach ($data as $item) {
            $user = User::firstOrCreate(
                ['email' => 'siswa' . $item['nis'] . '@sekolah.sch.id'],
                [
                    'role' => 'siswa',
                    'nama' => $item['nama'],
                    'username' => 'siswa' . $item['nis'],
                    'password' => Hash::make('password123'),
                    'jenis_kelamin' => $item['jk'],
                    'no_hp' => '08' . $item['nis'],
                    'foto' => '',
                    'status' => 'aktif',
                ]
            );

            DataSiswa::firstOrCreate(
                ['nis' => $item['nis']],
                [
                    'user_id'  => $user->id,
                    'kelas_id' => $item['kelas']->id,
                    'alamat'   => $item['alamat'],
                ]
            );
        }
    }
}