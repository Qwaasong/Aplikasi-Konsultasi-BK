<?php

namespace Database\Factories;

use App\Models\BimbinganIndividu;
use App\Models\KasusBk;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class BimbinganIndividuFactory extends Factory
{
    protected $model = BimbinganIndividu::class;

    public function definition(): array
    {
        return [
            'kasus_id' => KasusBk::factory(),
            'guru_bk_id' => Pegawai::factory()->guruBk(),
            'tahun_ajaran_id' => TahunAjaran::factory()->aktif(),
            'tanggal_layanan' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'uraian_masalah' => $this->faker->paragraph(),
            'penanganan' => $this->faker->paragraph(),
            'tindak_lanjut' => $this->faker->optional()->paragraph(),
        ];
    }
}
