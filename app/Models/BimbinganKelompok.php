<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BimbinganKelompok extends Model
{
    protected $table = 'bimbingan_kelompok';

    protected $fillable = [
        'guru_bk_id',
        'tahun_ajaran_id',
        'tanggal_layanan',
        'topik',
        'tujuan',
        'hasil_tindak_lanjut',
    ];

    public function guruBk()
    {
        return $this->belongsTo(Pegawai::class, 'guru_bk_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}
