<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use App\Models\KasusBk;
use App\Models\PelanggaranSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class PelanggaranSiswaFactory extends Factory
{
    protected $model = PelanggaranSiswa::class;

    public function definition(): array
    {
        return [
            'siswa_id' => DataSiswa::factory(),
            'kasus_id' => KasusBk::factory(),
            'tanggal_pernyataan' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'deskripsi' => $this->faker->paragraph(),
            'sanksi' => $this->faker->sentence(),
            'tindak_lanjut' => $this->faker->paragraph(),
            'bukti_foto' => $this->faker->optional()->imageUrl(),
        ];
    }
}
