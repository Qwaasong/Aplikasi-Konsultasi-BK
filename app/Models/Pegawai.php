<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $fillable = [
        'user_id',
        'nip',
        'jabatan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function kasusBk(): HasMany
    {
        return $this->hasMany(KasusBk::class, 'guru_bk_id');
    }

    public function homeVisits(): HasMany
    {
        return $this->hasMany(HomeVisit::class, 'guru_bk_id');
    }

    public function bimbinganKelompok(): HasMany
    {
        return $this->hasMany(BimbinganKelompok::class, 'guru_bk_id');
    }

    public function bimbinganIndividu(): HasMany
    {
        return $this->hasMany(BimbinganIndividu::class, 'guru_bk_id');
    }

    public function alihtanganAsal(): HasMany
    {
        return $this->hasMany(AlihtanganKasus::class, 'nama_asal');
    }

    public function alihtanganTujuan(): HasMany
    {
        return $this->hasMany(AlihtanganKasus::class, 'nama_penerima');
    }
}
