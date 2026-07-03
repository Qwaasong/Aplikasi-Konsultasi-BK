<?php

namespace App\Repositories\Contracts;

use App\Models\Sekolah;
use Illuminate\Support\Collection;

interface SekolahRepositoryInterface
{
    /**
     * Ambil semua data sekolah.
     */
    public function getAll(): Collection;

    /**
     * Hitung jumlah sekolah.
     */
    public function countSekolah(): int;

    /**
     * Cari sekolah berdasarkan ID.
     */
    public function findById(int $id): Sekolah;

    /**
     * Tambah data sekolah.
     */
    public function create(array $data): Sekolah;

    /**
     * Update data sekolah.
     */
    public function update(int $id, array $data): Sekolah;

    /**
     * Hapus data sekolah.
     */
    public function delete(int $id): bool;
}