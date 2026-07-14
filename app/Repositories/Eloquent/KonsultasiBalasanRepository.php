<?php

namespace App\Repositories\Eloquent;

use App\Models\KonsultasiBalasan;
use App\Repositories\Contracts\KonsultasiBalasanRepositoryInterface;
use Illuminate\Support\Collection;

class KonsultasiBalasanRepository implements KonsultasiBalasanRepositoryInterface
{
    public function getByKonsultasi(int $konsultasiId): Collection
    {
        return KonsultasiBalasan::where('konsultasi_id', $konsultasiId)
            ->orderBy('created_at')
            ->get();
    }

    public function findById(int $id): KonsultasiBalasan
    {
        return KonsultasiBalasan::findOrFail($id);
    }

    public function create(array $data): KonsultasiBalasan
    {
        return KonsultasiBalasan::create($data);
    }

    public function delete(int $id): bool
    {
        return KonsultasiBalasan::findOrFail($id)->delete();
    }
}