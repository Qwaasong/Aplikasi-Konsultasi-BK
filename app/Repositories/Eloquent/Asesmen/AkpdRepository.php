<?php

namespace App\Repositories\Eloquent\Asesmen;

use App\Models\Akpd;
use App\Repositories\Contracts\l\AkpdRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AkpdRepository implements AkpdRepositoryInterface
{
    public function getAll(): Collection
    {
        return Akpd::with(['siswa.user', 'siswa.kelas.jurusan'])
            ->latest()
            ->get();
    }

    public function findById(int $id): ?Akpd
    {
        return Akpd::with(['siswa.user', 'siswa.kelas.jurusan'])
            ->find($id);
    }

    public function create(array $data): Akpd
    {
        return Akpd::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Akpd::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Akpd::findOrFail($id)->delete();
    }

    public function query(): Builder
    {
        return Akpd::with(['siswa.user', 'siswa.kelas.jurusan']);
    }
}
