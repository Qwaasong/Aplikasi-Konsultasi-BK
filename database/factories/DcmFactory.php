<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use App\Models\Dcm;
use Illuminate\Database\Eloquent\Factories\Factory;

class DcmFactory extends Factory
{
    protected $model = Dcm::class;

    public function definition(): array
    {
        return [
            'siswa_id' => DataSiswa::factory(),
            'tanggal' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'masalah_teridentifikasi' => $this->faker->randomElements(
                ['Akademik', 'Pribadi', 'Sosial', 'Keluarga', 'Karir', 'Disiplin', 'Motivasi'],
                $this->faker->numberBetween(1, 4)
            ),
            'kesimpulan' => $this->faker->optional()->sentence(),
            'catatan' => $this->faker->optional()->sentence(),
        ];
    }
}
