<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sosiometri extends Model
{
    use HasFactory;
    protected $table = 'sosiometri';

    protected $fillable = [
        'siswa_id',
        'judul',
        'instruksi',
        'jumlah_pilihan',
    ];

    protected $casts = [
        'jumlah_pilihan' => 'integer',
    ];

    /**
     * Siswa yang mengisi sosiometri ini.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }

    /**
     * Respons (pilihan) untuk sosiometri ini.
     */
    public function respons(): HasMany
    {
        return $this->hasMany(SosiometriRespon::class, 'sosiometri_id');
    }
}
