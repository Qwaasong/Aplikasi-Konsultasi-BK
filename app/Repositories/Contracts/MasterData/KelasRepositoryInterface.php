<?php

namespace App\Repositories\Contracts\MasterData;

use App\Models\Kelas;
use Illuminate\Support\Collection;

interface KelasRepositoryInterface
{
    /**
     * Ambil semua data kelas.
     */
    public function getAll(): Collection;

    /**
     * Hitung jumlah kelas.
     */
    public function countKelas(): int;

    /**
     * Cari kelas berdasarkan ID.
     */
    public function findById(int $id): Kelas;

    /**
     * Ambil kelas berdasarkan jurusan.
     */
    public function getByJurusan(int $jurusanId): Collection;

    /**
     * Tambah data kelas.
     */
    public function create(array $data): Kelas;

    /**
     * Update data kelas.
     */
    public function update(int $id, array $data): Kelas;

    /**
     * Hapus data kelas.
     */
    public function delete(int $id): bool;
}