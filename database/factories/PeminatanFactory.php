<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use App\Models\Peminatan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeminatanFactory extends Factory
{
    protected $model = Peminatan::class;

    public function definition(): array
    {
        return [
            'siswa_id' => DataSiswa::factory(),
            'tanggal' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'pilihan1' => $this->faker->randomElement(['RPL', 'TKJ', 'MM', 'Akuntansi', 'Administrasi Perkantoran']),
            'pilihan2' => $this->faker->randomElement(['RPL', 'TKJ', 'MM', 'Akuntansi', 'Administrasi Perkantoran']),
            'pilihan3' => $this->faker->randomElement(['RPL', 'TKJ', 'MM', 'Akuntansi', 'Administrasi Perkantoran']),
            'hasil' => $this->faker->sentence(),
            'catatan' => $this->faker->optional()->paragraph(),
        ];
    }
}
