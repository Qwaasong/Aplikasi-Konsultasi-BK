<?php

namespace Database\Factories;

use App\Models\HomeVisit;
use App\Models\KasusBk;
use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Factories\Factory;

class HomeVisitFactory extends Factory
{
    protected $model = HomeVisit::class;

    public function definition(): array
    {
        return [
            'kasus_id' => KasusBk::factory(),
            'guru_bk_id' => Pegawai::factory()->guruBk(),
            'tanggal_kunjungan' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'uraian_masalah' => $this->faker->paragraph(),
            'penanganan' => $this->faker->paragraph(),
            'tindak_lanjut' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement(['diproses', 'ditunda', 'dibatalkan']),
        ];
    }
}
