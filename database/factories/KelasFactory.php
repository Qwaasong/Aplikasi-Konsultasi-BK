<?php

namespace Database\Factories;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class KelasFactory extends Factory
{
    protected $model = Kelas::class;

    public function definition(): array
    {
        $tingkat = $this->faker->randomElement(['X', 'XI', 'XII']);

        return [
            'jurusan_id' => Jurusan::factory(),
            'nama_kelas' => $tingkat . ' ' . $this->faker->randomElement(['RPL 1', 'TKJ 1', 'MM 1']),
            'tingkat' => $tingkat,
            'wali_kelas_id' => null,
        ];
    }
}
