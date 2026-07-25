<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BimbinganKelompok extends Model
{
    use HasFactory;

    protected $table = 'bimbingan_kelompok';

    protected $fillable = [
        'guru_bk_id',
        'tahun_ajaran_id',
        'kasus_id',
        'tanggal_layanan',
    ];

    protected $casts = [
        'tanggal_layanan' => 'date',
    ];

    public function guruBk(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'guru_bk_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function kasus(): BelongsTo
    {
        return $this->belongsTo(KasusBk::class, 'kasus_id');
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(BimbinganKelompokSiswa::class, 'bimbingan_kelompok_id');
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
