<?php

namespace App\Services;

use App\Models\TahunAjaran;
use Illuminate\Support\Collection;

class TahunAjaranService
{
    public function getAll(): Collection
    {
        return TahunAjaran::latest()->get();
    }

    public function findById(int $id): ?TahunAjaran
    {
        return TahunAjaran::find($id);
    }

    public function getActive(): ?TahunAjaran
    {
        return TahunAjaran::where('status_aktif', true)->first();
    }

    public function create(array $data): TahunAjaran
    {
        return TahunAjaran::create($data);
    }

    public function update(int $id, array $data): TahunAjaran
    {
        $record = TahunAjaran::findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    public function delete(int $id): void
    {
        TahunAjaran::findOrFail($id)->delete();
    }
}
