<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlihtanganKasus extends Model
{
    protected $table = 'alihtangan_kasus';

    protected $fillable = [
        'konsultasi_id',
        'guru_bk_asal_id',
        'pihak_penerima',
        'tanggal_alih',
        'alasan_alih',
        'tindak_lanjut',
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class, 'konsultasi_id');
    }

    public function guruBkAsal()
    {
        return $this->belongsTo(Pegawai::class, 'guru_bk_asal_id');
    }
}
