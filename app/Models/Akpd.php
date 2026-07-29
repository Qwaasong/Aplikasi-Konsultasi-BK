<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akpd extends Model
{
    use HasFactory;
    protected $table = 'akpds';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'pribadi',
        'sosial',
        'belajar',
        'karir',
        'kesimpulan',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }
}
