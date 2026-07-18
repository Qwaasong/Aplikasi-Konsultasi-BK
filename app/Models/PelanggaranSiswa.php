<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelanggaranSiswa extends Model
{
    use HasFactory;
    protected $table = 'pelanggaran_siswa';

    protected $fillable = [
        'siswa_id',
        'kasus_id',
        'tanggal_pernyataan',
        'deskripsi',
        'sanksi',
        'tindak_lanjut',
        'bukti_foto',
    ];

    protected $casts = [
        'tanggal_pernyataan' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }

    public function kasus(): BelongsTo
    {
        return $this->belongsTo(KasusBk::class, 'kasus_id');
    }
}
