<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KonferensiKasus extends Model
{
    protected $table = 'konferensi_kasus';

    protected $fillable = [
        'kasus_id',
        'tanggal_konferensi',
        'uraian_masalah',
        'penanganan',
        'tindak_lanjut',
    ];

    protected $casts = [
        'tanggal_konferensi' => 'date',
    ];

    public function kasus(): BelongsTo
    {
        return $this->belongsTo(KasusBk::class, 'kasus_id');
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(KonferensiKasusPeserta::class, 'konferensi_kasus_id');
    }
}
