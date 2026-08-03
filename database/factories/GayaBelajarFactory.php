<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class GayaBelajarFactory extends Factory
{
    protected $model = \App\Models\GayaBelajar::class;

    public function definition(): array
    {
        $visual = $this->faker->numberBetween(10, 30);
        $auditori = $this->faker->numberBetween(10, 30);
        $kinestetik = $this->faker->numberBetween(10, 30);
        $scores = ['visual' => $visual, 'auditori' => $auditori, 'kinestetik' => $kinestetik];
        $hasil = array_keys($scores, max($scores))[0];

        return [
            'siswa_id' => DataSiswa::factory(),
            'tanggal' => $this->faker->date(),
            'visual' => $visual,
            'auditori' => $auditori,
            'kinestetik' => $kinestetik,
            'hasil' => $hasil,
            'catatan' => $this->faker->optional()->sentence(),
            'faktor_penghambat' => $this->faker->optional()->sentence(),
            'faktor_pendukung' => $this->faker->optional()->sentence(),
        ];
    }
}
