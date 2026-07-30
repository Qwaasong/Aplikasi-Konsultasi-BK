<?php

namespace App\Repositories\Contracts;

use App\Models\KeluargaSiswa;
use Illuminate\Support\Collection;

interface KeluargaSiswaRepositoryInterface
{
    /**
     * Ambil semua data keluarga siswa.
     */
    public function getAll(): Collection;

    /**
     * Cari data keluarga siswa berdasarkan ID.
     */
    public function findById(int $id): ?KeluargaSiswa;

    /**
     * Tambah data keluarga siswa.
     */
    public function create(array $data): KeluargaSiswa;

    /**
     * Update data keluarga siswa.
     */
    public function update(int $id, array $data): bool;

    /**
     * Hapus data keluarga siswa.
     */
    public function delete(int $id): bool;
}