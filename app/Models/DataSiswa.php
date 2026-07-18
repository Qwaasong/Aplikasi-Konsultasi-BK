<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataSiswa extends Model
{
    use HasFactory;

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
        'tgl_lahir' => 'date',
        'anak_ke' => 'integer',
        'jml_saudara' => 'integer',
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

    public function kasus()
    {
        return $this->hasMany(KasusBk::class, 'siswa_id');
    }

    public function keluarga()
    {
        return $this->hasOne(KeluargaSiswa::class, 'siswa_id');
    }

    public function pelanggarans()
    {
        return $this->hasMany(PelanggaranSiswa::class, 'siswa_id');
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'siswa_id');
    }

    public function peminatan()
    {
        return $this->hasMany(Peminatan::class, 'siswa_id');
    }

    public function pengunduranDiri()
    {
        return $this->hasMany(PengunduranDiri::class, 'siswa_id');
    }

    public function sosiometri()
    {
        return $this->hasMany(Sosiometri::class, 'siswa_id');
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

    public function getTotalKasusAttribute(): int
    {
        return $this->kasus()->count();
    }
}
