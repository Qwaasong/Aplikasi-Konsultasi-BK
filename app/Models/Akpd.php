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
        'tahun_pelajaran',
        'q01', 'q02', 'q03', 'q04', 'q05',
        'q06', 'q07', 'q08', 'q09', 'q10',
        'q11', 'q12', 'q13', 'q14', 'q15',
        'q16', 'q17', 'q18', 'q19', 'q20',
        'q21', 'q22', 'q23', 'q24', 'q25',
        'q26', 'q27', 'q28', 'q29', 'q30',
        'q31', 'q32', 'q33', 'q34', 'q35',
        'q36', 'q37', 'q38', 'q39', 'q40',
        'q41', 'q42', 'q43', 'q44', 'q45',
        'q46', 'q47', 'q48', 'q49', 'q50',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }
}
