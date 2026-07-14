<?php

namespace App\Repositories\Contracts;

use App\Models\AlihtanganKasus;
use Illuminate\Support\Collection;

interface AlihtanganKasusRepositoryInterface
{
    /**
     * Ambil semua data alih tangan kasus.
     */
    public function getAll(): Collection;

    /**
     * Cari data alih tangan kasus berdasarkan ID.
     */
    public function findById(int $id): ?AlihtanganKasus;

    /**
     * Tambah data alih tangan kasus.
     */
    public function create(array $data): AlihtanganKasus;

    /**
     * Update data alih tangan kasus.
     */
    public function update(int $id, array $data): bool;

    /**
     * Hapus data alih tangan kasus.
     */
    public function delete(int $id): bool;
}