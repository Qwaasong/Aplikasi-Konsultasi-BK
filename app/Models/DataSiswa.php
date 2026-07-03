<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DataSiswa extends Model
{
    protected $table = 'data_siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'kelas_id',
        'alamat',
    ];

    protected $casts = [
        'nis' => 'integer',
    ];

    // ─────────────────────────────────────────
    // RELATIONS
    // ─────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function konsultasis()
    {
        return $this->hasMany(Konsultasi::class, 'siswa_id');
    }

    // ─────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function (Builder $q) use ($keyword) {
            $q->whereHas('user', fn($q) => $q->where('nama', 'like', "%{$keyword}%"))
              ->orWhere('nis', 'like', "%{$keyword}%");
        });
    }

    public function scopeByKelas(Builder $query, int $kelasId): Builder
    {
        return $query->where('kelas_id', $kelasId);
    }

    // ─────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────

    public function getNamaAttribute()
    {
        return $this->user?->nama;
    }

    public function getInitialsAttribute(): string
    {
        $nama = $this->nama;
        $words = explode(' ', trim($nama));

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($nama, 0, 2));
    }

    public function getKelasLabelAttribute(): string
    {
        return $this->kelas?->nama_kelas ?? '-';
    }

    public function getTotalKonsultasiAttribute(): int
    {
        return $this->konsultasis()->count();
    }
}
