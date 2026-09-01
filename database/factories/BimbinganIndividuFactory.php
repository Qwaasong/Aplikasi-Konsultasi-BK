<?php

namespace Database\Factories;

use App\Models\BimbinganIndividu;
use App\Models\KasusBk;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class BimbinganIndividuFactory extends Factory
{
    protected $model = BimbinganIndividu::class;

    public function definition(): array
    {
        return [
            'kasus_id' => KasusBk::factory(),
            'tanggal_layanan' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
        ];
    }
}
