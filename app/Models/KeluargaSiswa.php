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
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'alamat',
        'nomor_telepon',
    ];

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }
}