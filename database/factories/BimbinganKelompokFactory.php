<?php

namespace Database\Factories;

use App\Models\BimbinganKelompok;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class BimbinganKelompokFactory extends Factory
{
    protected $model = BimbinganKelompok::class;

    public function definition(): array
    {
        return [
            'guru_bk_id' => Pegawai::factory()->guruBk(),
            'tahun_ajaran_id' => TahunAjaran::factory()->aktif(),
            'tanggal_layanan' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'uraian_masalah' => $this->faker->sentence(),
            'penanganan' => $this->faker->optional()->paragraph(),
            'tindak_lanjut' => $this->faker->optional()->paragraph(),
        ];
    }
}
