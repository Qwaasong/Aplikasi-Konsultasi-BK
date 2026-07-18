<?php

namespace Database\Factories;

use App\Models\KonferensiKasus;
use App\Models\KonferensiKasusPeserta;
use Illuminate\Database\Eloquent\Factories\Factory;

class KonferensiKasusPesertaFactory extends Factory
{
    protected $model = KonferensiKasusPeserta::class;

    public function definition(): array
    {
        return [
            'konferensi_kasus_id' => KonferensiKasus::factory(),
            'nama_peserta' => $this->faker->name(),
            'peran_peserta' => $this->faker->randomElement(['Guru BK', 'Wali Kelas', 'Kepala Sekolah', 'Orang Tua', 'Siswa']),
        ];
    }
}
