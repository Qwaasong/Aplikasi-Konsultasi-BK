<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use App\Models\Sosiometri;
use Illuminate\Database\Eloquent\Factories\Factory;

class SosiometriFactory extends Factory
{
    protected $model = Sosiometri::class;

    public function definition(): array
    {
        return [
            'siswa_id' => DataSiswa::factory(),
            'judul' => $this->faker->sentence(3),
            'instruksi' => $this->faker->sentence(6),
            'jumlah_pilihan' => $this->faker->numberBetween(1, 5),
        ];
    }
}
