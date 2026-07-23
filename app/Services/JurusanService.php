<?php

namespace App\Services;

use App\Models\Jurusan;
use Illuminate\Support\Collection;

class JurusanService
{
    public function getAll(): Collection
    {
        return Jurusan::with('sekolah')->latest()->get();
    }

    public function findById(int $id): ?Jurusan
    {
        return Jurusan::with('sekolah')->find($id);
    }

    public function create(array $data): Jurusan
    {
        return Jurusan::create($data);
    }

    public function update(int $id, array $data): Jurusan
    {
        $record = Jurusan::findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    public function delete(int $id): void
    {
        Jurusan::findOrFail($id)->delete();
    }
}
