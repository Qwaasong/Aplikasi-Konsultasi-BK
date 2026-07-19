<?php

namespace Database\Factories;

use App\Models\DataSiswa;
use App\Models\KeluargaSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class KeluargaSiswaFactory extends Factory
{
    protected $model = KeluargaSiswa::class;

    public function definition(): array
    {
        return [
            'siswa_id' => DataSiswa::factory(),
            'nama_ayah' => $this->faker->name('male'),
            'nama_ibu' => $this->faker->name('female'),
            'pendidikan_ayah' => $this->faker->randomElement(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3']),
            'pendidikan_ibu' => $this->faker->randomElement(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3']),
            'pekerjaan_ayah' => $this->faker->randomElement(['PNS', 'Wiraswasta', 'Petani', 'Buruh', 'Guru', 'Dokter']),
            'pekerjaan_ibu' => $this->faker->randomElement(['IRT', 'PNS', 'Guru', 'Dokter', 'Perawat', 'Wiraswasta']),
            'telp_ortu' => $this->faker->phoneNumber(),
            'status_rumah' => $this->faker->randomElement(['Milik Sendiri', 'Kontrak', 'Sewa', 'Numpang']),
            'dinding_rumah' => $this->faker->randomElement(['Tembok', 'Kayu', 'Bambu']),
            'lantai_rumah' => $this->faker->randomElement(['Keramik', 'Semen', 'Kayu', 'Tanah']),
            'jml_kamar' => $this->faker->numberBetween(1, 6),
            'punya_kamar_sendiri' => $this->faker->boolean(),
            'jml_tv' => $this->faker->numberBetween(0, 4),
            'kendaraan_mobil' => $this->faker->numberBetween(0, 3),
            'kendaraan_motor' => $this->faker->numberBetween(0, 4),
            'biaya_sekolah_dari' => $this->faker->randomElement(['Orang Tua', 'Beasiswa', 'Paman', 'Bantuan']),
            'kendaraan_ke_sekolah' => $this->faker->randomElement(['Jalan Kaki', 'Sepeda', 'Motor', 'Mobil', 'Angkutan Umum']),
        ];
    }
}
