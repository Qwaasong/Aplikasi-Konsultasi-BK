<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlihtanganKasus extends Model
{
    protected $table = 'alihtangan_kasus';

    protected $fillable = [
        'kasus_id',
        'nama_asal',
        'nama_penerima',
        'tanggal_alih',
        'alasan_alih',
        'tindak_lanjut',
    ];

    protected $casts = [
        'tanggal_alih' => 'date',
    ];

    public function kasus(): BelongsTo
    {
        return $this->belongsTo(KasusBk::class, 'kasus_id');
    }

    public function guruBkAsal(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nama_asal');
    }

    public function guruBkTujuan(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nama_penerima');
    }
}
