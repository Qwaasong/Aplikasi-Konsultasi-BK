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
        'uraian_masalah',
        'penanganan',
        'tindak_lanjut',
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
    // BACKWARD COMPATIBILITY ACCESSORS/MUTATORS
    // ─────────────────────────────────────────

    public function getJudulAttribute()
    {
        return $this->penanganan;
    }

    public function setJudulAttribute($value)
    {
        $this->attributes['penanganan'] = $value;
    }

    public function getIsiKonsultasiAttribute()
    {
        return $this->uraian_masalah;
    }

    public function setIsiKonsultasiAttribute($value)
    {
        $this->attributes['uraian_masalah'] = $value;
    }

    public function getHasilTindakLanjutAttribute()
    {
        return $this->tindak_lanjut;
    }

    public function setHasilTindakLanjutAttribute($value)
    {
        $this->attributes['tindak_lanjut'] = $value;
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
