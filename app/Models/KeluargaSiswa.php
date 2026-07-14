<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeluargaSiswa extends Model
{
    protected $table = 'keluarga_siswa';

    protected $fillable = [
        'siswa_id',
        'nama_ayah',
        'nama_ibu',
        'pendidikan_ayah',
        'pendidikan_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'telp_ortu',
        'status_rumah',
        'dinding_rumah',
        'lantai_rumah',
        'jml_kamar',
        'punya_kamar_sendiri',
        'jml_tv',
        'kendaraan_mobil',
        'kendaraan_motor',
        'biaya_sekolah_dari',
        'kendaraan_ke_sekolah',
    ];

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }
}