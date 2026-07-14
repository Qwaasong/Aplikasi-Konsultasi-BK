<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengunduranDiri extends Model
{
    protected $table = 'pengunduran_diri';

    protected $fillable = [
        'siswa_id',
        'nama_ortu_wali',
        'alamat_ortu_wali',
        'alasan_pengunduran',
        'tanggal_pengunduran',
    ];

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }
}
