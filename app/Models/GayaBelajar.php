<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GayaBelajar extends Model
{
    use HasFactory;

    protected $table = 'gaya_belajars';

    protected $fillable = [
        'siswa_id', 'tanggal', 'visual', 'auditori', 'kinestetik', 'hasil', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'visual' => 'integer',
        'auditori' => 'integer',
        'kinestetik' => 'integer',
    ];

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }
}
