<?php

namespace Database\Factories;

use App\Models\KategoriKasus;
use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriKasusFactory extends Factory
{
    protected $model = KategoriKasus::class;

    public function definition(): array
    {
        return [
            'nama_kategori' => $this->faker->randomElement(['Pribadi', 'Belajar', 'Karir', 'Sosial']),
        ];
    }
}
