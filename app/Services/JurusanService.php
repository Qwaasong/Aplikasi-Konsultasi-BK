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

    public function getFiltered(array $filters = []): \Illuminate\Support\Collection
    {
        $query = Jurusan::with('sekolah');

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('kode_jurusan', 'like', "%{$keyword}%")
                    ->orWhere('nama_jurusan', 'like', "%{$keyword}%")
                    ->orWhereHas('sekolah', fn($q2) => $q2->where('nama_sekolah', 'like', "%{$keyword}%"));
            });
        }

        if (!empty($filters['sekolah'])) {
            $query->whereHas('sekolah', fn($q) => $q->where('nama_sekolah', $filters['sekolah']));
        }

        return $query->latest()->get();
    }

    public function getFilterOptions(): array
    {
        $all = $this->getAll();

        return [
            'sekolahOptions' => $all->pluck('sekolah.nama_sekolah')->filter()->unique()->sort()->values()->toArray(),
        ];
    }
}
