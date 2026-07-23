<?php

namespace App\Services;

use App\Models\PengunduranDiri;
use Illuminate\Support\Collection;

class PengunduranDiriService
{
    public function getAll(): Collection
    {
        return PengunduranDiri::with(['siswa.user', 'siswa.kelas.jurusan'])
            ->latest()
            ->get();
    }

    public function findById(int $id): ?PengunduranDiri
    {
        return PengunduranDiri::with(['siswa.user', 'siswa.kelas.jurusan'])
            ->find($id);
    }

    public function create(array $data): PengunduranDiri
    {
        return PengunduranDiri::create($data);
    }

    public function update(int $id, array $data): PengunduranDiri
    {
        $record = PengunduranDiri::findOrFail($id);
        $record->update($data);

        return $record->fresh(['siswa.user', 'siswa.kelas.jurusan']);
    }

    public function delete(int $id): void
    {
        PengunduranDiri::findOrFail($id)->delete();
    }

    public function search(string $keyword, int $limit = 5): \Illuminate\Support\Collection
    {
        return PengunduranDiri::with('siswa.user')
            ->whereHas('siswa.user', fn($q) => $q->where('nama', 'like', "%{$keyword}%"))
            ->take($limit)
            ->get();
    }
}
