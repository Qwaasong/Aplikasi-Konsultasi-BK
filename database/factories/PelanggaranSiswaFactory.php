<?php

namespace Database\Factories;

use App\Models\KasusBk;
use App\Models\PelanggaranSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class PelanggaranSiswaFactory extends Factory
{
    protected $model = PelanggaranSiswa::class;

    public function definition(): array
    {
        return [
            'kasus_id' => KasusBk::factory(),
            'tanggal_pernyataan' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'sanksi' => $this->faker->sentence(),
            'bukti_foto' => $this->faker->optional()->imageUrl(),
        ];
    }
}
