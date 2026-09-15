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
                'nama'          => 'Dani',
                'username'      => 'pak.dani',
                'email'         => 'danny@sekolah.sch.id',
                'jenis_kelamin' => 'L',
                'no_hp'         => '081200000001',
                'nip'           => 'GBK001',
                'jabatan'       => 'Guru BK',
            ],
            [
                'nama'          => 'Bening',
                'username'      => 'bu.bening',
                'email'         => 'bening@sekolah.sch.id',
                'jenis_kelamin' => 'P',
                'no_hp'         => '081200000002',
                'nip'           => 'GBK002',
                'jabatan'       => 'Guru BK',
            ],
            [
                'nama'          => 'Mayang',
                'username'      => 'bu.mayang',
                'email'         => 'mayang@sekolah.sch.id',
                'jenis_kelamin' => 'P',
                'no_hp'         => '081200000003',
                'nip'           => 'GBK003',
                'jabatan'       => 'Guru BK',
            ],
            [
                'nama'          => 'Bawon',
                'username'      => 'bu.bawon',
                'email'         => 'bawon@sekolah.sch.id',
                'jenis_kelamin' => 'P',
                'no_hp'         => '081200000004',
                'nip'           => 'GBK004',
                'jabatan'       => 'Guru BK',
            ],
        ];

        foreach ($konselor as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'nama' => $data['nama'],
                    'username' => $data['username'],
                    'no_hp' => $data['no_hp'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'foto' => '',
                    'status' => 'aktif',
                    'role' => 'guru_bk',
                    'password' => Hash::make('password123'),
                ]
            );

            Pegawai::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => $data['nip'],
                    'jabatan' => $data['jabatan'],
                ]
            );

            $this->command->info("Konselor {$data['nama']} siap login dengan username: {$data['username']}");
        }

        $this->command->info('4 konselor berhasil di-seed: Dani, Bening, Mayang, Bawon');
        $this->command->info('   Password default: password123');
    }
}
