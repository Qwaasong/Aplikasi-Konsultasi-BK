<?php

namespace App\Repositories\Eloquent;

use App\Models\KonferensiKasusPeserta;
use App\Repositories\Contracts\KonferensiKasusPesertaRepositoryInterface;
use Illuminate\Support\Collection;

class KonferensiKasusPesertaRepository implements KonferensiKasusPesertaRepositoryInterface
{
    public function getAll(): Collection
    {
        return KonferensiKasusPeserta::latest()->get();
    }

    public function findById(int $id): ?KonferensiKasusPeserta
    {
        return KonferensiKasusPeserta::find($id);
    }

    public function create(array $data): KonferensiKasusPeserta
    {
        return KonferensiKasusPeserta::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return KonferensiKasusPeserta::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return KonferensiKasusPeserta::findOrFail($id)->delete();
    }
}
