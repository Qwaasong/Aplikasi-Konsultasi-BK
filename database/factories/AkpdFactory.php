<?php

namespace Database\Factories;

use App\Models\Akpd;
use App\Models\DataSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class AkpdFactory extends Factory
{
    protected $model = Akpd::class;

    public function definition(): array
    {
        return [
            'siswa_id' => DataSiswa::factory(),
            'tanggal' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'pribadi' => $this->faker->optional()->paragraph(),
            'sosial' => $this->faker->optional()->paragraph(),
            'belajar' => $this->faker->optional()->paragraph(),
            'karir' => $this->faker->optional()->paragraph(),
            'kesimpulan' => $this->faker->optional()->sentence(),
            'catatan' => $this->faker->optional()->sentence(),
        ];
    }
}
