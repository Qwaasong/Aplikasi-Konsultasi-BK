<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BimbinganIndividu extends Model
{
    use HasFactory;
    protected $table = 'bimbingan_individus';

    protected $fillable = [
        'kasus_id',
        'guru_bk_id',
        'tahun_ajaran_id',
        'tanggal_layanan',
    ];

    protected $casts = [
        'tanggal_layanan' => 'date',
    ];

    public function kasus()
    {
        return $this->belongsTo(KasusBk::class, 'kasus_id');
    }

    public function guruBk()
    {
        return $this->belongsTo(Pegawai::class, 'guru_bk_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
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
