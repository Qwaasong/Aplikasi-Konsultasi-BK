<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosiometriRespon extends Model
{
    protected $table = 'sosiometri_respons';

    protected $fillable = [
        'sosiometri_id',
        'siswa_dipilih_id',
        'siswa_pemilih_id',
        'urutan',
        'alasan',
    ];

    public function sosiometri()
    {
        return $this->belongsTo(Sosiometri::class, 'sosiometri_id');
    }

    public function siswaDipilih()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_dipilih_id');
    }

    public function siswaPemilih()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_pemilih_id');
    }
}
