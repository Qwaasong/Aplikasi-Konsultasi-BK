<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeVisit extends Model
{
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
}
