<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class AkpdFactory extends Factory
{
    protected $model = \App\Models\Akpd::class;

    public function definition(): array
    {
        $jawaban = ['Ya', 'Tidak'];
        $data = [
            'siswa_id' => DataSiswa::factory(),
            'tanggal' => $this->faker->date(),
            'tahun_pelajaran' => '2025/2026',
        ];

        for ($i = 1; $i <= 50; $i++) {
            $col = 'q' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $data[$col] = $this->faker->randomElement($jawaban);
        }

        return $data;
    }
}
