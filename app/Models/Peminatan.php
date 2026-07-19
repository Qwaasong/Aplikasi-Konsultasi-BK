<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminatan extends Model
{
    use HasFactory;
    protected $table = 'peminatans';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'pilihan1',
        'pilihan2',
        'pilihan3',
        'hasil',
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
