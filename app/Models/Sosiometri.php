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

    public const PERTANYAAN = [
        'Q1' => 'Siapa teman di kelas ini yang paling kamu inginkan untuk menjadi teman satu kelompok belajar?',
        'Q2' => 'Siapa teman yang paling tidak kamu harapkan berada dalam satu kelompok belajar denganmu?',
        'Q3' => 'Jika kamu sedang memiliki masalah pribadi dan ingin bercerita, siapa teman di kelompok ini yang paling kamu percayai?',
        'Q4' => 'Siapa teman yang paling jarang kamu ajak mengobrol atau berinteraksi saat waktu istirahat?',
        'Q5' => 'Jika kelompok ini harus memilih seorang ketua untuk memimpin proyek baru, siapa yang akan kamu pilih?',
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

    public function questionGroups(): array
    {
        $groups = [];

        $respons = $this->respons ?? collect();

        foreach (self::PERTANYAAN as $key => $pertanyaan) {
            $dipilih = $respons
                ->where('pertanyaan', $key)
                ->map(fn ($r) => $r->siswaDipilih?->nama)
                ->filter()
                ->values()
                ->all();

            $groups[] = [
                'key' => $key,
                'pertanyaan' => $pertanyaan,
                'dipilih' => $dipilih,
            ];
        }

        return $groups;
    }
}
