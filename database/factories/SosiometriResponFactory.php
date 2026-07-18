<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use App\Models\Sosiometri;
use App\Models\SosiometriRespon;
use Illuminate\Database\Eloquent\Factories\Factory;

class SosiometriResponFactory extends Factory
{
    protected $model = SosiometriRespon::class;

    public function definition(): array
    {
        return [
            'sosiometri_id' => Sosiometri::factory(),
            'siswa_dipilih_id' => DataSiswa::factory(),
            'siswa_pemilih_id' => DataSiswa::factory(),
            'urutan' => $this->faker->numberBetween(1, 5),
            'alasan' => $this->faker->sentence(),
        ];
    }
}
