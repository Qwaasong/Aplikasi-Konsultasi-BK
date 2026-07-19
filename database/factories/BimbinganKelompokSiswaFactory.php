<?php

namespace Database\Factories;

use App\Models\BimbinganKelompok;
use App\Models\BimbinganKelompokSiswa;
use App\Models\DataSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class BimbinganKelompokSiswaFactory extends Factory
{
    protected $model = BimbinganKelompokSiswa::class;

    public function definition(): array
    {
        return [
            'bimbingan_kelompok_id' => BimbinganKelompok::factory(),
            'siswa_id' => DataSiswa::factory(),
        ];
    }
}
