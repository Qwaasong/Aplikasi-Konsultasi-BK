<?php

namespace App\Repositories\Eloquent;

use App\Models\Konsultasi;
use App\Repositories\Contracts\KonsultasiRepositoryInterface;

class KonsultasiRepository implements KonsultasiRepositoryInterface
{
    public function getAll()
    {
        return Konsultasi::with('siswa')->latest()->get();
    }

    public function findById(int $id)
    {
        return Konsultasi::with('siswa')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Konsultasi::create($data);
    }

    public function update(int $id, array $data)
    {
        $konsultasi = Konsultasi::findOrFail($id);
        $konsultasi->update($data);
        return $konsultasi;
    }

    public function delete(int $id)
    {
        return Konsultasi::findOrFail($id)->delete();
    }
}
