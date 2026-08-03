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
        $jawaban = [];

        foreach (Peminatan::SECTIONS as $section) {
            $codes = array_keys(Peminatan::QUESTION_GROUPS[$section]);
            $jawaban[$section] = $this->faker->randomElements($codes, $this->faker->numberBetween(1, 5));
        }

        $peminatan = new Peminatan();
        $peminatan->jawaban = $jawaban;
        $dominant = $peminatan->dominantIntelligences();

        return [
            'siswa_id' => DataSiswa::factory(),
            'tanggal' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'pilihan1' => $this->faker->randomElement(['RPL', 'TKJ', 'MM', 'Akuntansi', 'Administrasi Perkantoran']),
            'pilihan2' => $this->faker->randomElement(['RPL', 'TKJ', 'MM', 'Akuntansi', 'Administrasi Perkantoran']),
            'pilihan3' => $this->faker->randomElement(['RPL', 'TKJ', 'MM', 'Akuntansi', 'Administrasi Perkantoran']),
            'jawaban' => $jawaban,
            'hasil' => $dominant[0] ?: 'Belum dianalisis',
            'catatan' => $this->faker->optional()->paragraph(),
        ];
    }
}
