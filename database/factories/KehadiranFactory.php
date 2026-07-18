<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use App\Models\Kehadiran;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class KehadiranFactory extends Factory
{
    protected $model = Kehadiran::class;

    public function definition(): array
    {
        return [
            'siswa_id' => DataSiswa::factory(),
            'tahun_ajaran_id' => TahunAjaran::factory()->aktif(),
            'tanggal_kehadiran' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['Hadir', 'Sakit', 'Izin', 'Alpha']),
        ];
    }
}
