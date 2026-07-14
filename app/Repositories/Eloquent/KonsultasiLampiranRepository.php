<?php

namespace App\Repositories\Eloquent;

use App\Models\KonsultasiLampiran;
use App\Repositories\Contracts\KonsultasiLampiranRepositoryInterface;
use Illuminate\Support\Collection;

class KonsultasiLampiranRepository implements KonsultasiLampiranRepositoryInterface
{
    /**
     * Ambil lampiran berdasarkan ID konsultasi.
     */
    public function getByKonsultasi(int $konsultasiId): Collection
    {
        return KonsultasiLampiran::where('konsultasi_id', $konsultasiId)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Cari lampiran berdasarkan ID.
     */
    public function findById(int $id): KonsultasiLampiran
    {
        return KonsultasiLampiran::findOrFail($id);
    }

    /**
     * Tambah lampiran.
     */
    public function create(array $data): KonsultasiLampiran
    {
        return KonsultasiLampiran::create($data);
    }

    /**
     * Hapus lampiran.
     */
    public function delete(int $id): bool
    {
        return KonsultasiLampiran::findOrFail($id)->delete();
    }
}
