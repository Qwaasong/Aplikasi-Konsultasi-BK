<?php

namespace App\Repositories\Contracts\MasterData;

use App\Models\Jurusan;
use Illuminate\Support\Collection;

interface JurusanRepositoryInterface
{
    /**
     * Ambil semua data jurusan.
     */
    public function getAll(): Collection;

    /**
     * Hitung jumlah jurusan.
     */
    public function countJurusan(): int;

    /**
     * Cari jurusan berdasarkan ID.
     */
    public function findById(int $id): Jurusan;

    /**
     * Ambil jurusan berdasarkan sekolah.
     */
    public function getBySekolah(int $sekolahId): Collection;

    /**
     * Tambah data jurusan.
     */
    public function create(array $data): Jurusan;

    /**
     * Update data jurusan.
     */
    public function update(int $id, array $data): Jurusan;

    /**
     * Hapus data jurusan.
     */
    public function delete(int $id): bool;
}