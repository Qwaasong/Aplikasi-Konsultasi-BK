<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelanggaranSiswa extends Model
{
    protected $table = 'pelanggaran_siswa';

    protected $fillable = [
        'siswa_id',
        'jenis_pelanggaran_id',
        'jumlah_poin',
        'tanggal_pelanggaran',
        'bukti_foto',
        'deskripsi_tindakan',
    ];

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }

    public function jenisPelanggaran()
    {
        return $this->belongsTo(JenisPelanggaran::class, 'jenis_pelanggaran_id');
    }
}