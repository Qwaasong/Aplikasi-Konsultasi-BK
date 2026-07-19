<?php

namespace Database\Factories;

use App\Models\Jurusan;
use App\Models\Sekolah;
use Illuminate\Database\Eloquent\Factories\Factory;

class JurusanFactory extends Factory
{
    protected $model = Jurusan::class;

    public function definition(): array
    {
        return [
            'sekolah_id' => Sekolah::factory(),
            'kode_jurusan' => $this->faker->unique()->bothify('JR-###'),
            'nama_jurusan' => $this->faker->randomElement([
                'Rekayasa Perangkat Lunak',
                'Teknik Komputer dan Jaringan',
                'Multimedia',
                'Akuntansi',
                'Manajemen Perkantoran',
            ]),
        ];
    }
}
