<?php

namespace App\Repositories\Contracts;

use App\Models\PelanggaranSiswa;
use Illuminate\Support\Collection;

interface PelanggaranSiswaRepositoryInterface
{
    /**
     * Ambil semua data pelanggaran siswa.
     */
    public function getAll(): Collection;

    /**
     * Cari data pelanggaran siswa berdasarkan ID.
     */
    public function findById(int $id): ?PelanggaranSiswa;

    /**
     * Tambah data pelanggaran siswa.
     */
    public function create(array $data): PelanggaranSiswa;

    /**
     * Update data pelanggaran siswa.
     */
    public function update(int $id, array $data): bool;

    /**
     * Hapus data pelanggaran siswa.
     */
    public function delete(int $id): bool;
}