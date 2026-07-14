<?php

namespace App\Repositories\Eloquent;

use App\Models\BimbinganKelompok;
use App\Repositories\Contracts\BimbinganKelompokRepositoryInterface;
use Illuminate\Support\Collection;

class BimbinganKelompokRepository implements BimbinganKelompokRepositoryInterface
{
    public function getAll(): Collection
    {
        return BimbinganKelompok::latest()->get();
    }

    public function findById(int $id): ?BimbinganKelompok
    {
        return BimbinganKelompok::find($id);
    }

    public function create(array $data): BimbinganKelompok
    {
        return BimbinganKelompok::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return BimbinganKelompok::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return BimbinganKelompok::findOrFail($id)->delete();
    }
}
