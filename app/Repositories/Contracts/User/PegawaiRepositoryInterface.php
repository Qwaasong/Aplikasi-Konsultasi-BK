<?php

namespace App\Repositories\Contracts;

use App\Models\Pegawai;
use Illuminate\Support\Collection;

interface PegawaiRepositoryInterface
{
    /**
     * Ambil semua data pegawai.
     */
    public function getAll(): Collection;

    /**
     * Hitung jumlah pegawai.
     */
    public function countPegawai(): int;

    /**
     * Cari pegawai berdasarkan ID.
     */
    public function findById(int $id): Pegawai;

    /**
     * Tambah data pegawai.
     */
    public function create(array $data): Pegawai;

    /**
     * Update data pegawai.
     */
    public function update(int $id, array $data): Pegawai;

    /**
     * Hapus data pegawai.
     */
    public function delete(int $id): bool;

    public function findByUserId(int $userId): ?Pegawai;
}