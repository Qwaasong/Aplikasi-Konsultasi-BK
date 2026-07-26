<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KonferensiKasus extends Model
{
    use HasFactory;
    protected $table = 'konferensi_kasus';

    protected $fillable = [
        'kasus_id',
        'guru_bk_id',
        'tanggal_konferensi',
        'tempat_pertemuan',
    ];

    protected $casts = [
        'tanggal_konferensi' => 'date',
    ];

    public function kasus(): BelongsTo
    {
        return $this->belongsTo(KasusBk::class, 'kasus_id');
    }

    public function guruBk(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'guru_bk_id');
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(KonferensiKasusPeserta::class, 'konferensi_kasus_id');
    }

    // ─────────────────────────────────────────
    // DELEGATION ACCESSORS (data dari kasus_bk)
    // ─────────────────────────────────────────

    public function getPenangananAttribute()
    {
        return $this->kasus?->penanganan;
    }

    public function getUraianMasalahAttribute()
    {
        return $this->kasus?->uraian_masalah;
    }

    public function getTindakLanjutAttribute()
    {
        return $this->kasus?->tindak_lanjut;
    }
}
