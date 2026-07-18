<?php

namespace Database\Factories;

use App\Models\KasusBk;
use App\Models\DataSiswa;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use App\Models\KategoriKasus;
use Illuminate\Database\Eloquent\Factories\Factory;

class KasusBkFactory extends Factory
{
    protected $model = KasusBk::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['Open', 'Pending', 'Closed']);
        $tanggalMulai = $this->faker->dateTimeBetween('-1 month', 'now');
        $tanggalSelesai = $status === 'Closed' ? $this->faker->dateTimeBetween($tanggalMulai, 'now') : null;
        $hasilAkhir = $status === 'Closed' ? $this->faker->sentence() : null;

        return [
            'siswa_id' => DataSiswa::inRandomOrder()->value('id') ?? DataSiswa::factory(),
            'guru_bk_id' => Pegawai::inRandomOrder()->value('id') ?? 1,
            'tahun_ajaran_id' => TahunAjaran::where('status_aktif', true)->value('id') 
                ?? TahunAjaran::inRandomOrder()->value('id') 
                ?? 1,
            'kategori_id' => KategoriKasus::inRandomOrder()->value('id') ?? KategoriKasus::factory(),
            'penanganan' => $this->faker->randomElement(['Konseling Individu', 'Bimbingan Belajar', 'Konsultasi Karir', 'Mediasi Konflik']),
            'uraian_masalah' => $this->faker->paragraph(),
            'status' => $status,
            'prioritas' => $this->faker->randomElement(['Rendah', 'Sedang', 'Tinggi']),
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_selesai' => $tanggalSelesai ? $tanggalSelesai->format('Y-m-d') : null,
            'hasil_akhir' => $hasilAkhir,
        ];
    }
}
