<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonsultasiBalasan extends Model
{
    protected $table = 'konsultasi_balasan';
    protected $fillable = [
        'konsultasi_id',
        'user_id',
        'pesan',
        'lampiran',
        'dibaca',
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class, 'konsultasi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
