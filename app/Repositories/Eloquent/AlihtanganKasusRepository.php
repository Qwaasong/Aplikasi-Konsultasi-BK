<?php

namespace App\Repositories\Eloquent;

use App\Models\AlihtanganKasus;
use App\Repositories\Contracts\AlihtanganKasusRepositoryInterface;
use Illuminate\Support\Collection;

class AlihtanganKasusRepository implements AlihtanganKasusRepositoryInterface
{
    public function getAll(): Collection
    {
        return AlihtanganKasus::latest()->get();
    }

    public function findById(int $id): ?AlihtanganKasus
    {
        return AlihtanganKasus::find($id);
    }

    public function create(array $data): AlihtanganKasus
    {
        return AlihtanganKasus::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return AlihtanganKasus::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return AlihtanganKasus::findOrFail($id)->delete();
    }
}
