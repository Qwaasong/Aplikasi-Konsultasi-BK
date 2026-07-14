<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sosiometri extends Model
{
    protected $table = 'sosiometri';

    protected $fillable = [
        'pemilih_siswa_id',
        'pilihan1_siswa_id',
        'alasan_1',
        'pilihan2_siswa_id',
        'alasan_2',
        'pilihan3_siswa_id',
        'alasan_3',
    ];

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'pemilih_siswa_id');
    }

    public function pilihan1()
    {
        return $this->belongsTo(DataSiswa::class, 'pilihan1_siswa_id');
    }

    public function pilihan2()
    {
        return $this->belongsTo(DataSiswa::class, 'pilihan2_siswa_id');
    }

    public function pilihan3()
    {
        return $this->belongsTo(DataSiswa::class, 'pilihan3_siswa_id');
    }
}
