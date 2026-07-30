<?php

namespace App\Repositories\Eloquent\Asesmen;

use App\Models\GayaBelajar;
use App\Repositories\Contracts\Asesmen\GayaBelajarRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GayaBelajarRepository implements GayaBelajarRepositoryInterface
{
    public function getAll(): Collection
    {
        return GayaBelajar::with(['siswa.user', 'siswa.kelas.jurusan'])
            ->latest()
            ->get();
    }

    public function findById(int $id): ?GayaBelajar
    {
        return GayaBelajar::with(['siswa.user', 'siswa.kelas.jurusan'])
            ->find($id);
    }

    public function create(array $data): GayaBelajar
    {
        return GayaBelajar::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return GayaBelajar::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return GayaBelajar::findOrFail($id)->delete();
    }

    public function query(): Builder
    {
        return GayaBelajar::with(['siswa.user', 'siswa.kelas.jurusan']);
    }
}
