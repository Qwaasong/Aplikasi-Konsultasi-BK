<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BimbinganKelompokSiswa extends Model
{
    use HasFactory;
    protected $table = 'bimbingan_kelompok_siswa';

    protected $fillable = [
        'bimbingan_kelompok_id',
        'siswa_id',
    ];

    public function bimbinganKelompok()
    {
        return $this->belongsTo(BimbinganKelompok::class, 'bimbingan_kelompok_id');
    }

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }
}
