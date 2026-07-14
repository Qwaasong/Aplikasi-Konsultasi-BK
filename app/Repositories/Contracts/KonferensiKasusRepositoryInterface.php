<?php

namespace App\Repositories\Contracts;

use App\Models\KonferensiKasus;
use Illuminate\Support\Collection;

interface KonferensiKasusRepositoryInterface
{
    /**
     * Ambil semua data konferensi kasus.
     */
    public function getAll(): Collection;

    /**
     * Cari data konferensi kasus berdasarkan ID.
     */
    public function findById(int $id): ?KonferensiKasus;

    /**
     * Tambah data konferensi kasus.
     */
    public function create(array $data): KonferensiKasus;

    /**
     * Update data konferensi kasus.
     */
    public function update(int $id, array $data): bool;

    /**
     * Hapus data konferensi kasus.
     */
    public function delete(int $id): bool;
}