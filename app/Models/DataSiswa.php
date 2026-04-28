<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DataSiswa extends Model
{
    protected $table = 'data_siswa';

    protected $fillable = [
        'nis',
        'nama',
        'kelas',
        'jenis_kelamin',
        'jurusan',
        'periode_ajaran',
    ];

    protected $casts = [
        'nis'   => 'integer',
        'kelas' => 'integer',
    ];

    // ─────────────────────────────────────────
    // RELATIONS
    // ─────────────────────────────────────────

    /**
     * Seorang siswa bisa punya banyak konsultasi.
     */
    public function konsultasis()
    {
        return $this->hasMany(Konsultasi::class, 'id_siswa');
    }

    // ─────────────────────────────────────────
    // SCOPES  (untuk filter di repository)
    // ─────────────────────────────────────────

    /**
     * Filter berdasarkan kata kunci (nama atau NIS).
     */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('nama', 'like', "%{$keyword}%")
              ->orWhere('nis', 'like', "%{$keyword}%");
        });
    }

    /**
     * Filter berdasarkan kelas.
     */
    public function scopeByKelas(Builder $query, int $kelas): Builder
    {
        return $query->where('kelas', $kelas);
    }

    /**
     * Filter berdasarkan jurusan.
     */
    public function scopeByJurusan(Builder $query, string $jurusan): Builder
    {
        return $query->where('jurusan', $jurusan);
    }

    /**
     * Filter berdasarkan jenis kelamin.
     */
    public function scopeByJenisKelamin(Builder $query, string $jenisKelamin): Builder
    {
        return $query->where('jenis_kelamin', $jenisKelamin);
    }

    /**
     * Filter berdasarkan periode ajaran.
     */
    public function scopeByPeriode(Builder $query, string $periode): Builder
    {
        return $query->where('periode_ajaran', $periode);
    }

    // ─────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────

    /**
     * Inisial nama siswa (untuk avatar).
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->nama));

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->nama, 0, 2));
    }

    /**
     * Label kelas + jurusan, contoh: "12 RPL".
     */
    public function getKelasLabelAttribute(): string
    {
        return $this->kelas . ' ' . $this->jurusan;
    }

    /**
     * Jumlah konsultasi yang pernah diikuti.
     */
    public function getTotalKonsultasiAttribute(): int
    {
        return $this->konsultasis()->count();
    }
}