<?php

namespace App\Repositories\Eloquent\Bimbingan;

use App\Models\BimbinganKelompokSiswa;
use App\Repositories\Contracts\Bimbingan\BimbinganKelompokSiswaRepositoryInterface;
use Illuminate\Support\Collection;

class BimbinganKelompokSiswaRepository implements BimbinganKelompokSiswaRepositoryInterface
{
    public function getAll(): Collection
    {
        return BimbinganKelompokSiswa::latest()->get();
    }

    public function findById(int $id): ?BimbinganKelompokSiswa
    {
        return BimbinganKelompokSiswa::find($id);
    }

    public function create(array $data): BimbinganKelompokSiswa
    {
        return BimbinganKelompokSiswa::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return BimbinganKelompokSiswa::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return BimbinganKelompokSiswa::findOrFail($id)->delete();
    }
}
