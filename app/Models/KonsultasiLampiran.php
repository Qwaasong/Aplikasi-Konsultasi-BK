<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonsultasiLampiran extends Model
{
    protected $table = 'konsultasi_lampiran';
    protected $fillable = [
        'konsultasi_id',
        'nama_file',
        'path_file',
        'tipe_file',
        'ukuran',
    ];
    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class, 'konsultasi_id');
    }
}
