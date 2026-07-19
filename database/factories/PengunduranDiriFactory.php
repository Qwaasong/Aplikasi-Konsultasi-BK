<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use App\Models\PengunduranDiri;
use Illuminate\Database\Eloquent\Factories\Factory;

class PengunduranDiriFactory extends Factory
{
    protected $model = PengunduranDiri::class;

    public function definition(): array
    {
        return [
            'siswa_id' => DataSiswa::factory(),
            'nama_ortu_wali' => $this->faker->name(),
            'alamat_ortu_wali' => $this->faker->address(),
            'alasan_pengunduran' => $this->faker->paragraph(),
            'tanggal_pengunduran' => $this->faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
        ];
    }
}
