<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\KasusBk;
use App\Models\DataSiswa;
use App\Models\User;
use App\Models\KategoriKasus;
use App\Models\TahunAjaran;

class KonsultasiSeeder extends Seeder
{
    public function run()
    {
        // Lewati jika data kasus_bk sudah ada (idempoten)
        if (KasusBk::count() > 0) {
            $this->command->info('KasusBk sudah ada, seeder konsultasi dilewati.');
            return;
        }

        $konselorUser = User::where('role', 'guru_bk')->first();
        $pegawai = \App\Models\Pegawai::where('user_id', $konselorUser->id)->first();
        $tahunAjaran = TahunAjaran::first();
        $kategoris = KategoriKasus::pluck('id', 'nama_kategori');
        $siswaIds = DataSiswa::pluck('id');

        if ($siswaIds->count() < 10) {
            $this->command->warn('DataSiswa kurang dari 10, seeder konsultasi dilewati.');
            return;
        }

        $data = [
            ['siswa_no' => 0, 'kategori' => 'Karir',  'judul' => 'Bingung memilih karir',           'isi' => 'Bingung memilih antara kuliah atau langsung kerja setelah lulus RPL.',                          'status' => 'Pending', 'hari' => 10],
            ['siswa_no' => 1, 'kategori' => 'Pribadi', 'judul' => 'Kesulitan mengatur waktu',         'isi' => 'Kesulitan membagi waktu antara hobi dan tugas sekolah.',                                        'status' => 'Pending', 'hari' => 9],
            ['siswa_no' => 2, 'kategori' => 'Belajar', 'judul' => 'Nilai matematika menurun',         'isi' => 'Nilai matematika menurun drastis pada semester ini.',                                           'status' => 'Pending', 'hari' => 8],
            ['siswa_no' => 3, 'kategori' => 'Sosial',  'judul' => 'Perselisihan dengan teman',        'isi' => 'Terjadi perselisihan dengan teman sekelas karena tugas kelompok.',                               'status' => 'Pending', 'hari' => 7],
            ['siswa_no' => 4, 'kategori' => 'Karir',   'judul' => 'Sertifikasi internasional',        'isi' => 'Minat untuk mengambil sertifikasi internasional networking.',                                    'status' => 'Open', 'hari' => 6],
            ['siswa_no' => 5, 'kategori' => 'Pribadi',  'judul' => 'Cemas saat presentasi',           'isi' => 'Kecemasan saat akan menghadapi presentasi di depan kelas.',                                       'status' => 'Open', 'hari' => 5],
            ['siswa_no' => 6, 'kategori' => 'Belajar', 'judul' => 'Sulit konsentrasi belajar',        'isi' => 'Kurang konsentrasi saat belajar di rumah karena suasana bising.',                                 'status' => 'Open', 'hari' => 4],
            ['siswa_no' => 7, 'kategori' => 'Karir',   'judul' => 'Portofolio GitHub',                'isi' => 'Ingin tahu cara membangun portofolio GitHub yang baik.',                                          'status' => 'Closed',  'hari' => 3],
            ['siswa_no' => 8, 'kategori' => 'Sosial',  'judul' => 'Dikucilkan di lingkungan PKL',     'isi' => 'Merasa dikucilkan oleh teman-teman di lingkungan PKL.',                                          'status' => 'Open', 'hari' => 2],
            ['siswa_no' => 9, 'kategori' => 'Pribadi',  'judul' => 'Masalah ekonomi keluarga',        'isi' => 'Masalah ekonomi keluarga yang mengganggu konsentrasi sekolah.',                                   'status' => 'Open', 'hari' => 1],
        ];

        foreach ($data as $item) {
            $status = $item['status'];
            $tanggalMulai = Carbon::now()->subDays($item['hari']);
            $tanggalSelesai = $status === 'Closed' ? Carbon::now() : null;
            $tindakLanjut = $status === 'Closed' ? 'Selesai ditangani' : null;

            KasusBk::create([
                'siswa_id'           => $siswaIds[$item['siswa_no']],
                'guru_bk_id'         => $pegawai->id,
                'tahun_ajaran_id'    => $tahunAjaran->id,
                'kategori_id'        => $kategoris[$item['kategori']],
                'penanganan'         => $item['judul'],
                'uraian_masalah'     => $item['isi'],
                'status'             => $status,
                'prioritas'          => 'Sedang',
                'tanggal_mulai'      => $tanggalMulai->format('Y-m-d'),
                'tanggal_selesai'    => $tanggalSelesai ? $tanggalSelesai->format('Y-m-d') : null,
                'tindak_lanjut'      => $tindakLanjut,
            ]);
        }
    }
}