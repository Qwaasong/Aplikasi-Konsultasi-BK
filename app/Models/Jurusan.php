<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;
    protected $table = 'jurusan';

    protected $fillable = [
        'sekolah_id',
        'kode_jurusan',
        'nama_jurusan',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}
