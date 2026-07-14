<?php

namespace App\Repositories\Eloquent;

use App\Models\PelanggaranSiswa;
use App\Repositories\Contracts\PelanggaranSiswaRepositoryInterface;
use Illuminate\Support\Collection;

class PelanggaranSiswaRepository implements PelanggaranSiswaRepositoryInterface
{
    public function getAll(): Collection
    {
        return PelanggaranSiswa::latest()->get();
    }

    public function findById(int $id): ?PelanggaranSiswa
    {
        return PelanggaranSiswa::find($id);
    }

    public function create(array $data): PelanggaranSiswa
    {
        return PelanggaranSiswa::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return PelanggaranSiswa::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return PelanggaranSiswa::findOrFail($id)->delete();
    }
}
