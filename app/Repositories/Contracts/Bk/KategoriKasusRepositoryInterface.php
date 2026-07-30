<?php

namespace App\Repositories\Contracts\Bk;

use App\Models\KategoriKasus;
use Illuminate\Support\Collection;

interface KategoriKasusRepositoryInterface
{
    /**
     * Ambil semua kategori.
     */
    public function getAll(): Collection;

    /**
     * Hitung jumlah kategori.
     */
    public function countKategori(): int;

    /**
     * Cari kategori berdasarkan ID.
     */
    public function findById(int $id): KategoriKasus;

    /**
     * Tambah kategori.
     */
    public function create(array $data): KategoriKasus;

    /**
     * Update kategori.
     */
    public function update(int $id, array $data): KategoriKasus;

    /**
     * Hapus kategori.
     */
    public function delete(int $id): bool;
}