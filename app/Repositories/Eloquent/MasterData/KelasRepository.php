<?php

namespace App\Repositories\Eloquent\MasterData;

use App\Models\Kelas;
use App\Repositories\Contracts\e\KelasRepositoryInterface;
use Illuminate\Support\Collection;

class KelasRepository implements KelasRepositoryInterface
{
    /**
     * Ambil semua data kelas beserta jurusan dan wali kelas.
     */
    public function getAll(): Collection
    {
        return Kelas::with(['jurusan', 'waliKelas'])
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();
    }

    /**
     * Hitung jumlah kelas.
     */
    public function countKelas(): int
    {
        return Kelas::count();
    }

    /**
     * Cari kelas berdasarkan ID.
     */
    public function findById(int $id): Kelas
    {
        return Kelas::with(['jurusan', 'waliKelas'])
            ->findOrFail($id);
    }

    /**
     * Ambil kelas berdasarkan jurusan.
     */
    public function getByJurusan(int $jurusanId): Collection
    {
        return Kelas::with('waliKelas')
            ->where('jurusan_id', $jurusanId)
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();
    }

    /**
     * Tambah data kelas.
     */
    public function create(array $data): Kelas
    {
        return Kelas::create($data);
    }

    /**
     * Update data kelas.
     */
    public function update(int $id, array $data): Kelas
    {
        $kelas = Kelas::findOrFail($id);

        $kelas->update($data);

        return $kelas->fresh();
    }

    /**
     * Hapus data kelas.
     */
    public function delete(int $id): bool
    {
        return Kelas::findOrFail($id)->delete();
    }
}