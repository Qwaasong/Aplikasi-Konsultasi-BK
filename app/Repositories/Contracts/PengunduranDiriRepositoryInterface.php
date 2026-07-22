<?php

namespace App\Repositories\Contracts;

use App\Models\PengunduranDiri;
use Illuminate\Support\Collection;

interface PengunduranDiriRepositoryInterface
{
    /**
     * Ambil semua data pengunduran diri.
     */
    public function getAll(): Collection;

    /**
     * Cari data pengunduran diri berdasarkan ID.
     */
    public function findById(int $id): ?PengunduranDiri;

    /**
     * Tambah data pengunduran diri.
     */
    public function create(array $data): PengunduranDiri;

    /**
     * Update data pengunduran diri.
     */
    public function update(int $id, array $data): bool;

    /**
     * Hapus data pengunduran diri.
     */
    public function delete(int $id): bool;
}
