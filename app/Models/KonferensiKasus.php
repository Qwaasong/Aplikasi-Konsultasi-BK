<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonferensiKasus extends Model
{
    protected $table = 'konferensi_kasus';

    protected $fillable = [
        'konsultasi_id',
        'tanggal_konferensi',
        'topik',
        'tindak_lanjut',
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class, 'konsultasi_id');
    }
}
