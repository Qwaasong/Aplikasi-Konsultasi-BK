<?php

namespace App\Repositories\Eloquent\Bk;

use App\Models\KategoriKasus;
use App\Repositories\Contracts\Bk\KategoriKasusRepositoryInterface;
use Illuminate\Support\Collection;

class KategoriKasusRepository implements KategoriKasusRepositoryInterface
{
    /**
     * Ambil semua kategori.
     */
    public function getAll(): Collection
    {
        return KategoriKasus::orderBy('nama_kategori')->get();
    }

    /**
     * Hitung jumlah kategori.
     */
    public function countKategori(): int
    {
        return KategoriKasus::count();
    }

    /**
     * Cari kategori berdasarkan ID.
     */
    public function findById(int $id): KategoriKasus
    {
        return KategoriKasus::findOrFail($id);
    }

    /**
     * Tambah kategori.
     */
    public function create(array $data): KategoriKasus
    {
        return KategoriKasus::create($data);
    }

    /**
     * Update kategori.
     */
    public function update(int $id, array $data): KategoriKasus
    {
        $kategori = KategoriKasus::findOrFail($id);

        $kategori->update($data);

        return $kategori->fresh();
    }

    /**
     * Hapus kategori.
     */
    public function delete(int $id): bool
    {
        return KategoriKasus::findOrFail($id)->delete();
    }
}