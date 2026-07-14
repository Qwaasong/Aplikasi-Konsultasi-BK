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
     * Hitung jumlah data sosiometri.
     */
    public function countSosiometri(): int;

    /**
     * Cari data sosiometri berdasarkan ID.
     */
    public function findById(int $id): Sosiometri;

    /**
     * Ambil data sosiometri berdasarkan siswa pemilih.
     */
    public function getBySiswa(int $siswaId): ?Sosiometri;

    /**
     * Tambah data sosiometri.
     */
    public function create(array $data): Sosiometri;

    /**
     * Update data sosiometri.
     */
    public function update(int $id, array $data): Sosiometri;

    /**
     * Hapus data sosiometri.
     */
    public function delete(int $id): bool;
}