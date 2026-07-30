<?php

namespace App\Repositories\Eloquent\Asesmen;

use App\Models\Peminatan;
use App\Repositories\Contracts\e\PeminatanRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PeminatanRepository implements PeminatanRepositoryInterface
{
    public function getAll(): Collection
    {
        return Peminatan::with(['siswa.user', 'siswa.kelas.jurusan'])
            ->latest()
            ->get();
    }

    public function findById(int $id): ?Peminatan
    {
        return Peminatan::with(['siswa.user', 'siswa.kelas.jurusan'])
            ->find($id);
    }

    public function create(array $data): Peminatan
    {
        return Peminatan::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Peminatan::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Peminatan::findOrFail($id)->delete();
    }

    public function query(): Builder
    {
        return Peminatan::with(['siswa.user', 'siswa.kelas.jurusan']);
    }
}
