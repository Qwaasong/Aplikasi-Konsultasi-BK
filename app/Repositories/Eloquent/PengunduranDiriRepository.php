<?php

namespace App\Repositories\Eloquent;

use App\Models\PengunduranDiri;
use App\Repositories\Contracts\PengunduranDiriRepositoryInterface;
use Illuminate\Support\Collection;

class PengunduranDiriRepository implements PengunduranDiriRepositoryInterface
{
    public function getAll(): Collection
    {
        return PengunduranDiri::latest()->get();
    }

    public function findById(int $id): ?PengunduranDiri
    {
        return PengunduranDiri::find($id);
    }

    public function create(array $data): PengunduranDiri
    {
        return PengunduranDiri::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return PengunduranDiri::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return PengunduranDiri::findOrFail($id)->delete();
    }
}
