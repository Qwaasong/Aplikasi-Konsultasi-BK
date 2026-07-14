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
        'tempat_lahir',
        'tgl_lahir',
        'anak_ke',
        'jml_saudara',
        'asal_smp',
        'agama',
        'hobi',
        'bakat',
        'rencana_lulus',
        'detail_rencana_lulus',
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

    public function keluarga()
    {
        return $this->hasOne(KeluargaSiswa::class, 'siswa_id');
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

    public function scopeByJurusan(Builder $query, string $jurusan): Builder
    {
        return $query->whereHas('kelas.jurusan', fn($q) => $q->where('nama_jurusan', $jurusan));
    }

    public function scopeByJenisKelamin(Builder $query, string $jenisKelamin): Builder
    {
        return $query->whereHas('user', fn($q) => $q->where('jenis_kelamin', $jenisKelamin));
    }

    public function scopeByPeriode(Builder $query, string $periode): Builder
    {
        // Filter siswa yang punya konsultasi di periode tertentu
        // Format periode: "2025/2026" → tahun = 2025
        $tahun = (int) explode('/', $periode)[0];

        return $query->whereHas('konsultasis', function ($q) use ($tahun) {
            $q->whereHas('tahunAjaran', function ($q2) use ($tahun) {
                $q2->where('tahun', $tahun);
            });
        });
    }

    // ─────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────

    public function getNamaAttribute()
    {
        return $this->user?->nama;
    }

    public function getJenisKelaminAttribute(): string
    {
        return $this->user?->jenis_kelamin ?? '-';
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

    public function getJurusanLabelAttribute(): string
    {
        return $this->kelas?->jurusan?->nama_jurusan ?? '-';
    }

    public function getNamaLengkapAttribute(): string
    {
        return $this->user?->nama ?? '-';
    }

    public function getTotalKonsultasiAttribute(): int
    {
        return $this->konsultasis()->count();
    }
}
