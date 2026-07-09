<?php

namespace App\Repositories\Eloquent;

use App\Models\KategoriKonsultasi;
use App\Repositories\Contracts\KategoriKonsultasiRepositoryInterface;
use Illuminate\Support\Collection;

class KategoriKonsultasiRepository implements KategoriKonsultasiRepositoryInterface
{
    /**
     * Ambil semua kategori.
     */
    public function getAll(): Collection
    {
        return KategoriKonsultasi::orderBy('nama_kategori')->get();
    }

    /**
     * Hitung jumlah kategori.
     */
    public function countKategori(): int
    {
        return KategoriKonsultasi::count();
    }

    /**
     * Cari kategori berdasarkan ID.
     */
    public function findById(int $id): KategoriKonsultasi
    {
        return KategoriKonsultasi::findOrFail($id);
    }

    /**
     * Tambah kategori.
     */
    public function create(array $data): KategoriKonsultasi
    {
        return KategoriKonsultasi::create($data);
    }

    /**
     * Update kategori.
     */
    public function update(int $id, array $data): KategoriKonsultasi
    {
        $kategori = KategoriKonsultasi::findOrFail($id);

        $kategori->update($data);

        return $kategori->fresh();
    }

    /**
     * Hapus kategori.
     */
    public function delete(int $id): bool
    {
        return KategoriKonsultasi::findOrFail($id)->delete();
    }
}