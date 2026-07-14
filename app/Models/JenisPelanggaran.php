<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPelanggaran extends Model
{
    protected $table = 'jenis_pelanggaran';

    protected $fillable = [
        'nama',
        'deskripsi',
        'poin',
    ];

    public function pelanggarans()
    {
        return $this->hasMany(PelanggaranSiswa::class, 'jenis_pelanggaran_id');
    }
}
