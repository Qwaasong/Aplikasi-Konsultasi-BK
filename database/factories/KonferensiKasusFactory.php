<?php

namespace Database\Factories;

use App\Models\KasusBk;
use App\Models\KonferensiKasus;
use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Factories\Factory;

class KonferensiKasusFactory extends Factory
{
    protected $model = KonferensiKasus::class;

    public function definition(): array
    {
        return [
            'kasus_id' => KasusBk::factory(),
            'guru_bk_id' => Pegawai::factory()->guruBk(),
            'tanggal_konferensi' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'tempat_pertemuan' => $this->faker->optional()->word(),
        ];
    }
}
