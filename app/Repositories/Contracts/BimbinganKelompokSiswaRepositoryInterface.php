<?php

namespace App\Repositories\Contracts;

use App\Models\BimbinganKelompokSiswa;
use Illuminate\Support\Collection;

interface BimbinganKelompokSiswaRepositoryInterface
{
    /**
     * Ambil semua data bimbingan kelompok siswa.
     */
    public function getAll(): Collection;

    /**
     * Cari data bimbingan kelompok siswa berdasarkan ID.
     */
    public function findById(int $id): ?BimbinganKelompokSiswa;

    /**
     * Tambah data bimbingan kelompok siswa.
     */
    public function create(array $data): BimbinganKelompokSiswa;

    /**
     * Update data bimbingan kelompok siswa.
     */
    public function update(int $id, array $data): bool;

    /**
     * Hapus data bimbingan kelompok siswa.
     */
    public function delete(int $id): bool;
}