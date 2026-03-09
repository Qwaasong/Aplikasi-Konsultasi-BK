<?php

namespace App\Repositories\Eloquent;

use App\Models\DataSiswa;
use App\Repositories\Contracts\SiswaRepositoryInterface;

class SiswaRepository implements SiswaRepositoryInterface
{
    public function getAll()
    {
        return DataSiswa::all();
    }

    public function findById(int $id)
    {
        return DataSiswa::findOrFail($id);
    }

    public function create(array $data)
    {
        return DataSiswa::create($data);
    }

    public function update(int $id, array $data)
    {
        $siswa = DataSiswa::findOrFail($id);
        $siswa->update($data);
        return $siswa;
    }

    public function delete(int $id)
    {
        $siswa = DataSiswa::findOrFail($id);
        return $siswa->delete();
    }
}
