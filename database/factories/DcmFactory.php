<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class DcmFactory extends Factory
{
    protected $model = \App\Models\Dcm::class;

    public function definition(): array
    {
        $masalahPool = [
            'Pribadi: Percaya diri',
            'Pribadi: Motivasi belajar',
            'Pribadi: Manajemen waktu',
            'Sosial: Pergaulan',
            'Sosial: Konflik teman',
            'Belajar: Kesulitan memahami',
            'Belajar: Konsentrasi',
            'Karir: Perencanaan karir',
            'Karir: Pilihan jurusan',
            'Keluarga: Komunikasi orang tua',
        ];

        return [
            'siswa_id' => DataSiswa::factory(),
            'tanggal' => $this->faker->date(),
            'masalah_teridentifikasi' => $this->faker->randomElements($masalahPool, $this->faker->numberBetween(1, 5)),
            'kesimpulan' => $this->faker->optional()->sentence(),
            'catatan' => $this->faker->optional()->sentence(),
        ];
    }
}
