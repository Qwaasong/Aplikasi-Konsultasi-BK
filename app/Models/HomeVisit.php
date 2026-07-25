<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomeVisit extends Model
{
    use HasFactory;

    protected $table = 'home_visits';

    protected $fillable = [
        'kasus_id',
        'guru_bk_id',
        'tanggal_kunjungan',
        'status',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
    ];

    public function kasus()
    {
        return $this->belongsTo(KasusBk::class, 'kasus_id');
    }

    public function guruBk()
    {
        return $this->belongsTo(Pegawai::class, 'guru_bk_id');
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

    // ─────────────────────────────────────────
    // BACKWARD COMPATIBILITY ACCESSORS
    // ─────────────────────────────────────────

    public function getJudulAttribute()
    {
        return $this->kasus?->penanganan;
    }

    public function getIsiKonsultasiAttribute()
    {
        return $this->kasus?->uraian_masalah;
    }

    public function getHasilTindakLanjutAttribute()
    {
        return $this->kasus?->tindak_lanjut;
    }

    public function getTanggalKonsultasiAttribute()
    {
        return $this->tanggal_kunjungan;
    }

    public function setTanggalKonsultasiAttribute($value)
    {
        $this->attributes['tanggal_kunjungan'] = $value;
    }

    public function getSiswaAttribute()
    {
        return $this->kasus?->siswa;
    }

    public function getPrioritasAttribute()
    {
        return $this->kasus?->prioritas ?? 'Sedang';
    }
}
