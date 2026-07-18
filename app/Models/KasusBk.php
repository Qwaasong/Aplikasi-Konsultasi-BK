<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KasusBk extends Model
{
    use HasFactory;
    protected $table = 'kasus_bk';

    protected $fillable = [
        'siswa_id',
        'guru_bk_id',
        'tahun_ajaran_id',
        'kategori_id',
        'penanganan',
        'uraian_masalah',
        'status',
        'prioritas',
        'tanggal_mulai',
        'tanggal_selesai',
        'hasil_akhir',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public const STATUS_OPEN = 'Open';
    public const STATUS_PENDING = 'Pending';
    public const STATUS_CLOSED = 'Closed';

    public const PRIORITAS_RENDAH = 'Rendah';
    public const PRIORITAS_SEDANG = 'Sedang';
    public const PRIORITAS_TINGGI = 'Tinggi';

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }

    public function guruBk()
    {
        return $this->belongsTo(Pegawai::class, 'guru_bk_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriKasus::class, 'kategori_id');
    }

    public function lampirans()
    {
        return $this->hasMany(KonsultasiLampiran::class, 'kasus_id');
    }

    public function homeVisits()
    {
        return $this->hasMany(HomeVisit::class, 'kasus_id');
    }

    public function alihTanganKasus()
    {
        return $this->hasMany(AlihtanganKasus::class, 'kasus_id');
    }

    public function bimbinganIndividu()
    {
        return $this->hasMany(BimbinganIndividu::class, 'kasus_id');
    }

    public function konferensiKasus()
    {
        return $this->hasMany(KonferensiKasus::class, 'kasus_id');
    }

    public function pelanggaranSiswa()
    {
        return $this->hasMany(PelanggaranSiswa::class, 'kasus_id');
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

    public function getDeksripsiAttribute()
    {
        return $this->uraian_masalah;
    }

    public function setDeksripsiAttribute($value)
    {
        $this->attributes['uraian_masalah'] = $value;
    }

    public function getDeskripsiAttribute()
    {
        return $this->uraian_masalah;
    }

    public function setDeskripsiAttribute($value)
    {
        $this->attributes['uraian_masalah'] = $value;
    }

    public function getIsiKonsultasiAttribute()
    {
        return $this->uraian_masalah;
    }

    public function setIsiKonsultasiAttribute($value)
    {
        $this->attributes['uraian_masalah'] = $value;
    }

    public function getTanggalKonsultasiAttribute()
    {
        return $this->tanggal_mulai;
    }

    public function setTanggalKonsultasiAttribute($value)
    {
        $this->attributes['tanggal_mulai'] = $value;
    }

    public function getHasilTindakLanjutAttribute()
    {
        return $this->hasil_akhir;
    }

    public function setHasilTindakLanjutAttribute($value)
    {
        $this->attributes['hasil_akhir'] = $value;
    }
}
