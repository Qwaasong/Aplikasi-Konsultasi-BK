<?php

namespace App\Repositories\Contracts;

use App\Models\BimbinganKelompok;
use Illuminate\Support\Collection;

interface BimbinganKelompokRepositoryInterface
{
    /**
     * Ambil semua data bimbingan kelompok.
     */
    public function getAll(): Collection;

    /**
     * Cari data bimbingan kelompok berdasarkan ID.
     */
    public function findById(int $id): ?BimbinganKelompok;

    /**
     * Tambah data bimbingan kelompok.
     */
    public function create(array $data): BimbinganKelompok;

    /**
     * Update data bimbingan kelompok.
     */
    public function update(int $id, array $data): bool;

    /**
     * Hapus data bimbingan kelompok.
     */
    public function delete(int $id): bool;
}