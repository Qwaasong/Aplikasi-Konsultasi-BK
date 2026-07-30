<?php

namespace App\Repositories\Eloquent\Siswa;

use App\Models\KeluargaSiswa;
use App\Repositories\Contracts\Siswa\KeluargaSiswaRepositoryInterface;
use Illuminate\Support\Collection;

class KeluargaSiswaRepository implements KeluargaSiswaRepositoryInterface
{
    public function getAll(): Collection
    {
        return KeluargaSiswa::latest()->get();
    }

    public function findById(int $id): ?KeluargaSiswa
    {
        return KeluargaSiswa::find($id);
    }

    public function create(array $data): KeluargaSiswa
    {
        return KeluargaSiswa::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return KeluargaSiswa::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return KeluargaSiswa::findOrFail($id)->delete();
    }
}
