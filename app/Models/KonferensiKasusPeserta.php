<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonferensiKasusPeserta extends Model
{
    use HasFactory;
    protected $table = 'konferensi_kasus_peserta';

    protected $fillable = [
        'konferensi_kasus_id',
        'nama_peserta',
        'peran_peserta',
    ];

    public function konferensiKasus()
    {
        return $this->belongsTo(KonferensiKasus::class, 'konferensi_kasus_id');
    }
}
