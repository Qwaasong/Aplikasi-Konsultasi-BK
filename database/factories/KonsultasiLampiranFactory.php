<?php

namespace Database\Factories;

use App\Models\KasusBk;
use App\Models\KonsultasiLampiran;
use Illuminate\Database\Eloquent\Factories\Factory;

class KonsultasiLampiranFactory extends Factory
{
    protected $model = KonsultasiLampiran::class;

    public function definition(): array
    {
        $ext = $this->faker->fileExtension();

        return [
            'kasus_id' => KasusBk::factory(),
            'nama_file' => $this->faker->word() . '.' . $ext,
            'path_file' => 'uploads/konsultasi/' . $this->faker->uuid() . '.' . $ext,
            'tipe_file' => $ext,
            'ukuran' => $this->faker->numberBetween(1024, 10485760),
        ];
    }
}
