<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use App\Models\GayaBelajar;
use Illuminate\Database\Eloquent\Factories\Factory;

class GayaBelajarFactory extends Factory
{
    protected $model = GayaBelajar::class;

    public function definition(): array
    {
        $visual = $this->faker->numberBetween(10, 50);
        $auditori = $this->faker->numberBetween(10, 50);
        $kinestetik = $this->faker->numberBetween(10, 50);

        $max = max($visual, $auditori, $kinestetik);
        $hasil = match ($max) {
            $visual => 'Visual',
            $auditori => 'Auditori',
            default => 'Kinestetik',
        };

        return [
            'siswa_id' => DataSiswa::factory(),
            'tanggal' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'visual' => $visual,
            'auditori' => $auditori,
            'kinestetik' => $kinestetik,
            'hasil' => $hasil,
            'catatan' => $this->faker->optional()->sentence(),
        ];
    }
}
