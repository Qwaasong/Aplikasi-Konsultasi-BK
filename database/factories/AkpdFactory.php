<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class AkpdFactory extends Factory
{
    protected $model = \App\Models\Akpd::class;

    public function definition(): array
    {
        $aspek = ['Rendah', 'Sedang', 'Tinggi'];

        return [
            'siswa_id' => DataSiswa::factory(),
            'tanggal' => $this->faker->date(),
            'pribadi' => $this->faker->randomElement($aspek),
            'sosial' => $this->faker->randomElement($aspek),
            'belajar' => $this->faker->randomElement($aspek),
            'karir' => $this->faker->randomElement($aspek),
            'kesimpulan' => $this->faker->optional()->sentence(),
            'catatan' => $this->faker->optional()->sentence(),
        ];
    }
}
