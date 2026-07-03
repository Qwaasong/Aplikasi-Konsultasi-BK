<?php

namespace App\Repositories\Contracts;

use App\Models\KategoriKonsultasi;
use Illuminate\Support\Collection;

interface KategoriKonsultasiRepositoryInterface
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
    public function findById(int $id): KategoriKonsultasi;

    /**
     * Tambah kategori.
     */
    public function create(array $data): KategoriKonsultasi;

    /**
     * Update kategori.
     */
    public function update(int $id, array $data): KategoriKonsultasi;

    /**
     * Hapus kategori.
     */
    public function delete(int $id): bool;
}