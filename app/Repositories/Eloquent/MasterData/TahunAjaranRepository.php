<?php

namespace App\Repositories\Eloquent\MasterData;

use App\Models\TahunAjaran;
use App\Repositories\Contracts\l\A\TahunAjaranRepositoryInterface;
use Illuminate\Support\Collection;

class TahunAjaranRepository implements TahunAjaranRepositoryInterface
{
    /**
     * Ambil semua data tahun ajaran.
     */
    public function getAll(): Collection
    {
        return TahunAjaran::orderByDesc('tahun')->get();
    }

    /**
     * Hitung jumlah tahun ajaran.
     */
    public function countTahunAjaran(): int
    {
        return TahunAjaran::count();
    }

    /**
     * Cari tahun ajaran berdasarkan ID.
     */
    public function findById(int $id): TahunAjaran
    {
        return TahunAjaran::findOrFail($id);
    }

    /**
     * Ambil tahun ajaran yang aktif.
     */
    public function getActive(): ?TahunAjaran
    {
        return TahunAjaran::where('status_aktif', 1)->first();
    }

    /**
     * Tambah data tahun ajaran.
     */
    public function create(array $data): TahunAjaran
    {
        return TahunAjaran::create($data);
    }

    /**
     * Update data tahun ajaran.
     */
    public function update(int $id, array $data): TahunAjaran
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        $tahunAjaran->update($data);

        return $tahunAjaran->fresh();
    }

    /**
     * Hapus data tahun ajaran.
     */
    public function delete(int $id): bool
    {
        return TahunAjaran::findOrFail($id)->delete();
    }
}