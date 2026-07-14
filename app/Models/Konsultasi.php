<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    protected $table = 'konsultasi';

    protected $fillable = [
        'siswa_id',
        'guru_bk_id',
        'tahun_ajaran_id',
        'kategori_id',
        'judul',
        'isi_konsultasi',
        'hasil_tindak_lanjut',
        'jenis_layanan',
        'fokus_masalah',
        'status',
        'prioritas',
        'tanggal_konsultasi',
    ];

    protected $casts = [
        'tanggal_konsultasi' => 'datetime',
    ];

    // ─────────────────────────────────────────
    // RELATIONS
    // ─────────────────────────────────────────

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }

    public function gurubk()
    {
        return $this->belongsTo(Pegawai::class, 'guru_bk_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriKonsultasi::class, 'kategori_id');
    }

    public function balasans()
    {
        return $this->hasMany(KonsultasiBalasan::class);
    }

    public function lampirans()
    {
        return $this->hasMany(KonsultasiLampiran::class);
    }
}
