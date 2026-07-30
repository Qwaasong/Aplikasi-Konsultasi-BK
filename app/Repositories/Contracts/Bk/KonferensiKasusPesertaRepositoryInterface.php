<?php

namespace App\Repositories\Contracts\Bk;

use App\Models\KonferensiKasusPeserta;
use Illuminate\Support\Collection;

interface KonferensiKasusPesertaRepositoryInterface
{
    /**
     * Ambil semua data peserta konferensi kasus.
     */
    public function getAll(): Collection;

    /**
     * Cari data peserta konferensi kasus berdasarkan ID.
     */
    public function findById(int $id): ?KonferensiKasusPeserta;

    /**
     * Tambah data peserta konferensi kasus.
     */
    public function create(array $data): KonferensiKasusPeserta;

    /**
     * Update data peserta konferensi kasus.
     */
    public function update(int $id, array $data): bool;

    /**
     * Hapus data peserta konferensi kasus.
     */
    public function delete(int $id): bool;
}