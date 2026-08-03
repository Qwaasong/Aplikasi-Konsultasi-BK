<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class DcmFactory extends Factory
{
    protected $model = \App\Models\Dcm::class;

    public function definition(): array
    {
        // Pilih beberapa kode acak dari setiap bagian A-L sebagai jawaban.
        $jawaban = [];

        foreach (\App\Models\Dcm::QUESTION_GROUPS as $section => $items) {
            $codes = array_keys($items);
            $jawaban[$section] = $this->faker->randomElements($codes, $this->faker->numberBetween(1, 5));
        }

        $dcm = new \App\Models\Dcm();
        $dcm->jawaban = $jawaban;

        return [
            'siswa_id' => DataSiswa::factory(),
            'tanggal' => $this->faker->date(),
            'jawaban' => $jawaban,
            'masalah_teridentifikasi' => $dcm->masalahSummary(),
            'kesimpulan' => $this->faker->optional()->sentence(),
            'catatan' => $this->faker->optional()->sentence(),
        ];
    }
}
