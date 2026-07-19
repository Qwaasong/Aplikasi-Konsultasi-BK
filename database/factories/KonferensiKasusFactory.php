<?php

namespace Database\Factories;

use App\Models\KasusBk;
use App\Models\KonferensiKasus;
use Illuminate\Database\Eloquent\Factories\Factory;

class KonferensiKasusFactory extends Factory
{
    protected $model = KonferensiKasus::class;

    public function definition(): array
    {
        return [
            'kasus_id' => KasusBk::factory(),
            'tanggal_konferensi' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'uraian_masalah' => $this->faker->sentence(),
            'penanganan' => $this->faker->sentence(),
            'tindak_lanjut' => $this->faker->optional()->paragraph(),
        ];
    }
}
