<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonsultasiBalasan extends Model
{
    protected $table = 'konsultasi_balasan';
    protected $fillable = [
        'konsultasi_id',
        'jawaban',
        'tanggal_balasan',
        'id_konselor',
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class, 'konsultasi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_konselor');
    }
}
