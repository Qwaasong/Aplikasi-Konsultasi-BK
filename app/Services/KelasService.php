<?php

namespace App\Services;

use App\Models\Kelas;
use Illuminate\Support\Collection;

class KelasService
{
    public function getAll(): Collection
    {
        return Kelas::with(['jurusan.sekolah', 'waliKelas.user'])->latest()->get();
    }

    public function findById(int $id): ?Kelas
    {
        return Kelas::with(['jurusan.sekolah', 'waliKelas.user'])->find($id);
    }

    public function create(array $data): Kelas
    {
        return Kelas::create($data);
    }

    public function update(int $id, array $data): Kelas
    {
        $record = Kelas::findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    public function delete(int $id): void
    {
        Kelas::findOrFail($id)->delete();
    }
}
