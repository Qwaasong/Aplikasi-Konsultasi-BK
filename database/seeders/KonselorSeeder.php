<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KonselorSeeder extends Seeder
{
    /**
     * Seeder data konselor (Guru BK).
     * Aman dijalankan berkali-kali (idempoten via email unik).
     */
    public function run(): void
    {
        $konselor = [
            [
                'nama'          => 'Danny',
                'username'      => 'pak.danny',
                'email'         => 'danny@sekolah.sch.id',
                'jenis_kelamin' => 'L',
                'no_hp'         => '081200000001',
                'nip'           => 'GBK' . date('Ymd') . '01',
                'jabatan'       => 'Guru BK',
            ],
            [
                'nama'          => 'Bening',
                'username'      => 'bu.bening',
                'email'         => 'bening@sekolah.sch.id',
                'jenis_kelamin' => 'P',
                'no_hp'         => '081200000002',
                'nip'           => 'GBK' . date('Ymd') . '02',
                'jabatan'       => 'Guru BK',
            ],
            [
                'nama'          => 'Mayang',
                'username'      => 'bu.mayang',
                'email'         => 'mayang@sekolah.sch.id',
                'jenis_kelamin' => 'P',
                'no_hp'         => '081200000003',
                'nip'           => 'GBK' . date('Ymd') . '03',
                'jabatan'       => 'Guru BK',
            ],
            [
                'nama'          => 'Bawon',
                'username'      => 'bu.bawon',
                'email'         => 'bawon@sekolah.sch.id',
                'jenis_kelamin' => 'P',
                'no_hp'         => '081200000004',
                'nip'           => 'GBK' . date('Ymd') . '04',
                'jabatan'       => 'Guru BK',
            ],
        ];

        foreach ($konselor as $data) {
            // Buat / ambil user yang sudah ada berdasarkan email
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'role'          => 'guru_bk',
                    'nama'          => $data['nama'],
                    'username'      => $data['username'],
                    'password'      => Hash::make('password123'),
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'no_hp'         => $data['no_hp'],
                    'foto'          => '',
                    'status'        => 'aktif',
                ]
            );

            // Buat / ambil pegawai berdasarkan user_id
            Pegawai::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nip'     => $data['nip'],
                    'jabatan' => $data['jabatan'],
                ]
            );
        }

        $this->command->info('✅ 4 konselor berhasil di-seed: Pak Danny, Bu Bening, Bu Mayang, Bu Bawon');
        $this->command->info('   Password default: password123');
    }
}
