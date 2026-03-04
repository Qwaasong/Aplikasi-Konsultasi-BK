<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSiswa extends Model
{
    protected $table = 'data_siswa';

    protected $fillable = [
        'nis',
        'nama',
        'kelas',
        'jenis_kelamin',
        'jurusan',
        'periode_ajaran',
    ];
}