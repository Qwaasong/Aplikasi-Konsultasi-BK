<?php

namespace App\Repositories\Eloquent\MasterData;

use App\Models\Sekolah;
use App\Repositories\Contracts\MasterData\SekolahRepositoryInterface;
use Illuminate\Support\Collection;

class SekolahRepository implements SekolahRepositoryInterface
{
    /**
     * Ambil semua data sekolah.
     */
    public function getAll(): Collection
    {
        return Sekolah::orderBy('nama_sekolah')->get();
    }

    /**
     * Hitung jumlah sekolah.
     */
    public function countSekolah(): int
    {
        return Sekolah::count();
    }

    /**
     * Cari sekolah berdasarkan ID.
     */
    public function findById(int $id): Sekolah
    {
        return Sekolah::findOrFail($id);
    }

    /**
     * Tambah data sekolah.
     */
    public function create(array $data): Sekolah
    {
        return Sekolah::create($data);
    }

    /**
     * Update data sekolah.
     */
    public function update(int $id, array $data): Sekolah
    {
        $sekolah = Sekolah::findOrFail($id);

        $sekolah->update($data);

        return $sekolah->fresh();
    }

    /**
     * Hapus data sekolah.
     */
    public function delete(int $id): bool
    {
        return Sekolah::findOrFail($id)->delete();
    }
}