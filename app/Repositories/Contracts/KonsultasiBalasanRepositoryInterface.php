<?php

namespace App\Repositories\Contracts;

use App\Models\KonsultasiBalasan;
use Illuminate\Support\Collection;

interface KonsultasiBalasanRepositoryInterface
{
    /**
     * Ambil balasan berdasarkan ID konsultasi.
     */
    public function getByKonsultasi(int $konsultasiId): Collection;

    /**
     * Cari balasan berdasarkan ID.
     */
    public function findById(int $id): KonsultasiBalasan;

    /**
     * Tambah balasan.
     */
    public function create(array $data): KonsultasiBalasan;

    /**
     * Hapus balasan.
     */
    public function delete(int $id): bool;
}
