<?php

namespace App\Repositories\Contracts;

use App\Models\KonsultasiLampiran;
use Illuminate\Support\Collection;

interface KonsultasiLampiranRepositoryInterface
{
    /**
     * Ambil lampiran berdasarkan ID konsultasi.
     */
    public function getByKonsultasi(int $konsultasiId): Collection;

    /**
     * Cari lampiran berdasarkan ID.
     */
    public function findById(int $id): KonsultasiLampiran;

    /**
     * Tambah lampiran.
     */
    public function create(array $data): KonsultasiLampiran;

    /**
     * Hapus lampiran.
     */
    public function delete(int $id): bool;
}
