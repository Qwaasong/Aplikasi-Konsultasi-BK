<?php

namespace Database\Factories;

use App\Models\AlihtanganKasus;
use App\Models\KasusBk;
use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlihtanganKasusFactory extends Factory
{
    protected $model = AlihtanganKasus::class;

    public function definition(): array
    {
        return [
            'kasus_id' => KasusBk::factory(),
            'nama_asal' => Pegawai::factory()->guruBk(),
            'nama_penerima' => Pegawai::factory()->guruBk(),
            'tanggal_alih' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'alasan_alih' => $this->faker->optional()->paragraph(),
            'tindak_lanjut' => $this->faker->optional()->paragraph(),
        ];
    }
}
