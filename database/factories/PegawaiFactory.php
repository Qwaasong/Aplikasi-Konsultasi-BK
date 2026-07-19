<?php

namespace Database\Factories;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PegawaiFactory extends Factory
{
    protected $model = Pegawai::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->guruBk(),
            'nip' => $this->faker->unique()->numerify('##################'),
            'jabatan' => 'Guru BK',
        ];
    }

    public function guruBk(): static
    {
        return $this->state(fn (array $attributes) => [
            'jabatan' => 'Guru BK',
        ]);
    }
}
