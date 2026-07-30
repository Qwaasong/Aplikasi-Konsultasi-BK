<?php

namespace App\Repositories\Eloquent\User;

use App\Models\PengunduranDiri;
use App\Repositories\Contracts\e\PengunduranDiriRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PengunduranDiriRepository implements PengunduranDiriRepositoryInterface
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

    public function update(int $id, array $data): bool
    {
        return PengunduranDiri::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return PengunduranDiri::findOrFail($id)->delete();
    }

    public function search(string $keyword, int $limit = 5): Collection
    {
        return PengunduranDiri::with('siswa.user')
            ->whereHas('siswa.user', fn($q) => $q->where('nama', 'like', "%{$keyword}%"))
            ->take($limit)
            ->get();
    }

    public function query(): Builder
    {
        return PengunduranDiri::with(['siswa.user', 'siswa.kelas.jurusan']);
    }
}
