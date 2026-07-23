<?php

namespace App\Services;

use App\Models\Sekolah;
use Illuminate\Support\Collection;

class SekolahService
{
    public function getAll(): Collection
    {
        return Sekolah::latest()->get();
    }

    public function findById(int $id): ?Sekolah
    {
        return Sekolah::find($id);
    }

    public function create(array $data): Sekolah
    {
        return Sekolah::create($data);
    }

    public function update(int $id, array $data): Sekolah
    {
        $record = Sekolah::findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    public function delete(int $id): void
    {
        Sekolah::findOrFail($id)->delete();
    }
}
