<?php

namespace Database\Factories;

use App\Models\KasusBk;
use App\Models\DataSiswa;
use App\Models\KategoriKasus;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class KasusBkFactory extends Factory
{
    protected $model = KasusBk::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['Open', 'Pending', 'Closed']);
        $tanggalMulai = $this->faker->dateTimeBetween('-1 month', 'now');
        $tanggalSelesai = $status === 'Closed' ? $this->faker->dateTimeBetween($tanggalMulai, 'now') : null;
        $tindakLanjut = $status === 'Closed' ? $this->faker->sentence() : null;

        return [
            'siswa_id' => DataSiswa::factory(),
            'guru_bk_id' => Pegawai::factory()->guruBk(),
            'tahun_ajaran_id' => TahunAjaran::factory()->aktif(),
            'kategori_id' => KategoriKasus::factory(),
            'penanganan' => $this->faker->randomElement(['Konseling Individu', 'Bimbingan Belajar', 'Konsultasi Karir', 'Mediasi Konflik']),
            'uraian_masalah' => $this->faker->paragraph(),
            'status' => $status,
            'prioritas' => $this->faker->randomElement(['Rendah', 'Sedang', 'Tinggi']),
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_selesai' => $tanggalSelesai ? $tanggalSelesai->format('Y-m-d') : null,
            'tindak_lanjut' => $tindakLanjut,
        ];
    }
}
