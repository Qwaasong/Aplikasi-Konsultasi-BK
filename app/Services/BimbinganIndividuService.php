<?php

namespace App\Services;

use App\Models\BimbinganIndividu;
use App\Models\KasusBk;
use App\Models\KategoriKasus;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;

class BimbinganIndividuService
{
    public function getAll(): Collection
    {
        return BimbinganIndividu::with([
            'guruBk.user',
            'tahunAjaran',
            'kasus.siswa.user',
            'kasus.siswa.kelas.jurusan',
        ])
            ->latest('tanggal_layanan')
            ->get();
    }

    public function findById(int $id): ?BimbinganIndividu
    {
        return BimbinganIndividu::with([
            'guruBk.user',
            'tahunAjaran',
            'kasus.siswa.user',
            'kasus.siswa.kelas.jurusan',
        ])
            ->findOrFail($id);
    }

    public function create(array $data): BimbinganIndividu
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();
        if (!$pegawai) {
            throw ValidationException::withMessages(['guru_bk' => 'Data pegawai tidak ditemukan.']);
        }

        $data['guru_bk_id'] = $pegawai->id;
        $data['tahun_ajaran_id'] ??= TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id');

        // Buat atau dapatkan KasusBk untuk siswa ini
        $siswaId = $data['siswa_id'] ?? null;
        unset($data['siswa_id']);

        if ($siswaId) {
            // Cari kasus yang sudah ada untuk siswa ini, atau buat baru
            $kasus = KasusBk::firstOrCreate(
                ['siswa_id' => $siswaId],
                [
                    'guru_bk_id' => $pegawai->id,
                    'tahun_ajaran_id' => $data['tahun_ajaran_id'],
                    'kategori_id' => KategoriKasus::inRandomOrder()->value('id'),
                    'penanganan' => $data['uraian_masalah'] ?? 'Konseling Individu',
                    'uraian_masalah' => $data['uraian_masalah'] ?? 'Konseling Individu',
                    'status' => 'Open',
                    'prioritas' => 'Sedang',
                    'tanggal_mulai' => $data['tanggal_layanan'] ?? now()->format('Y-m-d'),
                ]
            );
            $data['kasus_id'] = $kasus->id;
        }

        return BimbinganIndividu::create($data);
    }

    public function update(int $id, array $data): BimbinganIndividu
    {
        $record = BimbinganIndividu::findOrFail($id);

        // Jangan update relasi
        unset($data['siswa_id']);

        $record->update($data);
        return $record->fresh(['guruBk.user', 'tahunAjaran', 'kasus.siswa.user', 'kasus.siswa.kelas.jurusan']);
    }

    public function delete(int $id): void
    {
        BimbinganIndividu::findOrFail($id)->delete();
    }

    public function search(string $keyword, int $limit = 5): \Illuminate\Support\Collection
    {
        return BimbinganIndividu::with('kasus.siswa.user')
            ->where('uraian_masalah', 'like', "%{$keyword}%")
            ->take($limit)
            ->get();
    }
}
