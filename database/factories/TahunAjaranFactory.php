<?php

namespace Database\Factories;

use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class TahunAjaranFactory extends Factory
{
    protected $model = TahunAjaran::class;

    public function definition(): array
    {
        return [
            'tahun' => (string) $this->faker->numberBetween(2024, 2030),
            'semester' => $this->faker->randomElement(['Ganjil', 'Genap']),
            'status_aktif' => false,
        ];
    }

    public function aktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_aktif' => true,
        ]);
    }
}
