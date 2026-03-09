<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    protected $table = 'konsultasi';

    protected $fillable = [
        'tanggal',
        'id_siswa',
        'jenis_layanan',
        'deskripsi_masalah',
        'hasil_layanan',
        'tindak_lanjut',
        'id_user',
        'file',
    ];

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'id_siswa');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}