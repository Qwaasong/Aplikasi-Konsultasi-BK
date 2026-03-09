<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Konsultasi;

class KonsultasiSeeder extends Seeder
{
    public function run()
    {
        $konsultasi = [
            [
                'tanggal' => Carbon::now()->subDays(10),
                'id_siswa' => 1,
                'jenis_layanan' => 'Karir',
                'deskripsi_masalah' => 'Bingung memilih antara kuliah atau langsung kerja setelah lulus RPL.',
                'hasil_layanan' => 'Siswa diberikan panduan mengenai prospek kerja developer dan rekomendasi kampus.',
                'tindak_lanjut' => 'Mengikuti workshop bursa kerja minggu depan.',
                'id_user' => 1,
                'file' => null,
            ],
            [
                'tanggal' => Carbon::now()->subDays(9),
                'id_siswa' => 2,
                'jenis_layanan' => 'Pribadi',
                'deskripsi_masalah' => 'Kesulitan membagi waktu antara hobi dan tugas sekolah.',
                'hasil_layanan' => 'Penyusunan jadwal harian yang lebih terstruktur.',
                'tindak_lanjut' => 'Pemantauan jadwal dalam satu minggu ke depan.',
                'id_user' => 1,
                'file' => null,
            ],
            [
                'tanggal' => Carbon::now()->subDays(8),
                'id_siswa' => 3,
                'jenis_layanan' => 'Belajar',
                'deskripsi_masalah' => 'Nilai matematika menurun drastis pada semester ini.',
                'hasil_layanan' => 'Identifikasi kesulitan pada materi kalkulus.',
                'tindak_lanjut' => 'Rekomendasi mengikuti bimbingan belajar tambahan.',
                'id_user' => 1,
                'file' => null,
            ],
            [
                'tanggal' => Carbon::now()->subDays(7),
                'id_siswa' => 4,
                'jenis_layanan' => 'Sosial',
                'deskripsi_masalah' => 'Terjadi perselisihan dengan teman sekelas karena tugas kelompok.',
                'hasil_layanan' => 'Mediasi antara kedua belah pihak.',
                'tindak_lanjut' => 'Siswa sepakat untuk saling memaafkan.',
                'id_user' => 1,
                'file' => null,
            ],
            [
                'tanggal' => Carbon::now()->subDays(6),
                'id_siswa' => 5,
                'jenis_layanan' => 'Karir',
                'deskripsi_masalah' => 'Minat untuk mengambil sertifikasi internasional networking.',
                'hasil_layanan' => 'Informasi mengenai vendor sertifikasi yang diakui industri.',
                'tindak_lanjut' => 'Pendaftaran akun di platform sertifikasi terkait.',
                'id_user' => 1,
                'file' => null,
            ],
            [
                'tanggal' => Carbon::now()->subDays(5),
                'id_siswa' => 6,
                'jenis_layanan' => 'Pribadi',
                'deskripsi_masalah' => 'Kecemasan saat akan menghadapi presentasi di depan kelas.',
                'hasil_layanan' => 'Latihan teknik pernapasan dan tips public speaking.',
                'tindak_lanjut' => 'Berlatih presentasi di depan cermin secara rutin.',
                'id_user' => 1,
                'file' => null,
            ],
            [
                'tanggal' => Carbon::now()->subDays(4),
                'id_siswa' => 7,
                'jenis_layanan' => 'Belajar',
                'deskripsi_masalah' => 'Kurang konsentrasi saat belajar di rumah karena suasana bising.',
                'hasil_layanan' => 'Saran untuk belajar di perpustakaan atau menggunakan earplug.',
                'tindak_lanjut' => 'Mencoba belajar di perpustakaan sekolah setelah pulang.',
                'id_user' => 1,
                'file' => null,
            ],
            [
                'tanggal' => Carbon::now()->subDays(3),
                'id_siswa' => 8,
                'jenis_layanan' => 'Karir',
                'deskripsi_masalah' => 'Ingin tahu cara membangun portofolio GitHub yang baik.',
                'hasil_layanan' => 'Review project yang sudah dibuat dan cara penulisan README.',
                'tindak_lanjut' => 'Mengupload project tugas akhir ke GitHub pribadi.',
                'id_user' => 1,
                'file' => null,
            ],
            [
                'tanggal' => Carbon::now()->subDays(2),
                'id_siswa' => 9,
                'jenis_layanan' => 'Sosial',
                'deskripsi_masalah' => 'Merasa dikucilkan oleh teman-teman di lingkungan PKL.',
                'hasil_layanan' => 'Diskusi cara beradaptasi di lingkungan profesional.',
                'tindak_lanjut' => 'Mencoba lebih proaktif bertanya kepada pembimbing industri.',
                'id_user' => 1,
                'file' => null,
            ],
            [
                'tanggal' => Carbon::now()->subDays(1),
                'id_siswa' => 10,
                'jenis_layanan' => 'Pribadi',
                'deskripsi_masalah' => 'Masalah ekonomi keluarga yang mengganggu konsentrasi sekolah.',
                'hasil_layanan' => 'Konseling penguatan mental dan informasi bantuan siswa.',
                'tindak_lanjut' => 'Pengajuan berkas untuk beasiswa bantuan sekolah.',
                'id_user' => 1,
                'file' => null,
            ],
        ];

        foreach ($konsultasi as $data) {
            Konsultasi::create($data);
        }
    }
}