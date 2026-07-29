<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelanggaranSiswa extends Model
{
    use HasFactory;
    protected $table = 'pelanggaran_siswa';

    protected $fillable = [
        'kasus_id',
        'tanggal_pernyataan',
        'sanksi',
        'bukti_foto',
    ];

    protected $casts = [
        'tanggal_pernyataan' => 'date',
    ];

    public function kasus(): BelongsTo
    {
        return $this->belongsTo(KasusBk::class, 'kasus_id');
    }

    // ─────────────────────────────────────────
    // DELEGATION ACCESSORS (data dari kasus_bk)
    // ─────────────────────────────────────────

    public function getSiswaAttribute()
    {
        return $this->kasus?->siswa;
    }

    public function getDeskripsiAttribute()
    {
        return $this->kasus?->uraian_masalah;
    }

    public function getTindakLanjutAttribute()
    {
        return $this->kasus?->tindak_lanjut;
    }
}
