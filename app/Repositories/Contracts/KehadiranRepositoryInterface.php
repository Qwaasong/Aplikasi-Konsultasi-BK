<?php

namespace App\Repositories\Contracts;

use App\Models\Kehadiran;
use Illuminate\Support\Collection;

interface KehadiranRepositoryInterface
{
    /**
     * Ambil semua data kehadiran.
     */
    public function getAll(): Collection;

    /**
     * Cari data kehadiran berdasarkan ID.
     */
    public function findById(int $id): ?Kehadiran;

    /**
     * Tambah data kehadiran.
     */
    public function create(array $data): Kehadiran;

    /**
     * Update data kehadiran.
     */
    public function update(int $id, array $data): bool;

    /**
     * Hapus data kehadiran.
     */
    public function delete(int $id): bool;
}