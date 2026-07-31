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

    public function getFiltered(array $filters = []): \Illuminate\Support\Collection
    {
        $query = \App\Models\Sekolah::query();

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_sekolah', 'like', "%{$keyword}%")
                    ->orWhere('alamat', 'like', "%{$keyword}%")
                    ->orWhere('telepon', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if (!empty($filters['nama_sekolah'])) {
            $query->where('nama_sekolah', $filters['nama_sekolah']);
        }

        return $query->latest()->get();
    }

    public function getSekolahOptions(): array
    {
        return Sekolah::orderBy('nama_sekolah')->pluck('nama_sekolah')->toArray();
    }

    public function getFilterOptions(): array
    {
        return [];
    }
}
