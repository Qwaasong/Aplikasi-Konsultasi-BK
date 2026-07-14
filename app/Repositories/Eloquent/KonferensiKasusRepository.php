<?php

namespace App\Repositories\Eloquent;

use App\Models\KonferensiKasus;
use App\Repositories\Contracts\KonferensiKasusRepositoryInterface;
use Illuminate\Support\Collection;

class KonferensiKasusRepository implements KonferensiKasusRepositoryInterface
{
    public function getAll(): Collection
    {
        return KonferensiKasus::latest()->get();
    }

    public function findById(int $id): ?KonferensiKasus
    {
        return KonferensiKasus::find($id);
    }

    public function create(array $data): KonferensiKasus
    {
        return KonferensiKasus::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return KonferensiKasus::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return KonferensiKasus::findOrFail($id)->delete();
    }
}
