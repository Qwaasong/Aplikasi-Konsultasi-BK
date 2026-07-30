<?php

namespace App\Repositories\Eloquent\Asesmen;

use App\Models\Dcm;
use App\Repositories\Contracts\Asesmen\DcmRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DcmRepository implements DcmRepositoryInterface
{
    public function getAll(): Collection
    {
        return Dcm::with(['siswa.user', 'siswa.kelas.jurusan'])
            ->latest()
            ->get();
    }

    public function findById(int $id): ?Dcm
    {
        return Dcm::with(['siswa.user', 'siswa.kelas.jurusan'])
            ->find($id);
    }

    public function create(array $data): Dcm
    {
        return Dcm::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Dcm::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Dcm::findOrFail($id)->delete();
    }

    public function query(): Builder
    {
        return Dcm::with(['siswa.user', 'siswa.kelas.jurusan']);
    }
}
