<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dcm extends Model
{
    use HasFactory;

    protected $table = 'dcms';

    protected $fillable = [
        'siswa_id', 'tanggal', 'masalah_teridentifikasi', 'kesimpulan', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'masalah_teridentifikasi' => 'array',
    ];

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }
}
