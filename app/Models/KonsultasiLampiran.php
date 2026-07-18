<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonsultasiLampiran extends Model
{
    use HasFactory;
    protected $table = 'konsultasi_lampiran';

    protected $fillable = [
        'kasus_id',
        'nama_file',
        'path_file',
        'tipe_file',
        'ukuran',
    ];

    protected $casts = [
        'ukuran' => 'integer',
    ];

    public function kasus(): BelongsTo
    {
        return $this->belongsTo(KasusBk::class, 'kasus_id');
    }
}
