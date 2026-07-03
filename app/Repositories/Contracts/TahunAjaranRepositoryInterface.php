<?php

namespace App\Repositories\Contracts;

use App\Models\TahunAjaran;
use Illuminate\Support\Collection;

interface TahunAjaranRepositoryInterface
{
    /**
     * Ambil semua data tahun ajaran.
     */
    public function getAll(): Collection;

    /**
     * Hitung jumlah tahun ajaran.
     */
    public function countTahunAjaran(): int;

    /**
     * Cari tahun ajaran berdasarkan ID.
     */
    public function findById(int $id): TahunAjaran;

    /**
     * Ambil tahun ajaran yang aktif.
     */
    public function getActive(): ?TahunAjaran;

    /**
     * Tambah data tahun ajaran.
     */
    public function create(array $data): TahunAjaran;

    /**
     * Update data tahun ajaran.
     */
    public function update(int $id, array $data): TahunAjaran;

    /**
     * Hapus data tahun ajaran.
     */
    public function delete(int $id): bool;
}