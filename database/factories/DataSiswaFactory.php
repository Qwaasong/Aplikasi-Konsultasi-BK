<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataSiswaFactory extends Factory
{
    protected $model = DataSiswa::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'siswa']),
            'nis' => $this->faker->unique()->numerify('#####'),
            'kelas_id' => Kelas::inRandomOrder()->value('id') ?? 1,
            'alamat' => $this->faker->address(),
            'tempat_lahir' => $this->faker->city(),
            'tgl_lahir' => $this->faker->date(),
            'anak_ke' => $this->faker->numberBetween(1, 5),
            'jml_saudara' => $this->faker->numberBetween(0, 5),
            'asal_smp' => 'SMP Negeri ' . $this->faker->numberBetween(1, 10),
            'agama' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu']),
            'hobi' => $this->faker->randomElement(['Membaca', 'Olahraga', 'Musik', 'Gaming']),
            'bakat' => $this->faker->randomElement(['Menggambar', 'Koding', 'Menyanyi', 'Menulis']),
            'rencana_lulus' => $this->faker->randomElement(['Bekerja', 'Kuliah', 'Menikah']),
            'detail_rencana_lulus' => $this->faker->sentence(),
        ];
    }
}
