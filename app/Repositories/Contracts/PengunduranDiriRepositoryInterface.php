<?php

namespace App\Repositories\Contracts;

use App\Models\Sosiometri;
use Illuminate\Support\Collection;

interface SosiometriRepositoryInterface
{
    /**
     * Ambil semua data sosiometri.
     */
    public function getAll(): Collection;

    /**
     * Cari data sosiometri berdasarkan ID.
     */
    public function findById(int $id): ?Sosiometri;

    /**
     * Tambah data sosiometri.
     */
    public function create(array $data): Sosiometri;

    /**
     * Update data sosiometri.
     */
    public function update(int $id, array $data): bool;

    /**
     * Hapus data sosiometri.
     */
    public function delete(int $id): bool;
}