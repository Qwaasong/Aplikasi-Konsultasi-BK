<?php

namespace App\Repositories\Eloquent\Asesmen;

use App\Models\Sosiometri;
use App\Repositories\Contracts\Asesmen\SosiometriRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SosiometriRepository implements SosiometriRepositoryInterface
{
    public function getAll(): Collection
    {
        return Sosiometri::with(['siswa.user', 'siswa.kelas.jurusan'])->latest()->get();
    }

    public function countSosiometri(): int
    {
        return Sosiometri::count();
    }

    public function findById(int $id): Sosiometri
    {
        return Sosiometri::with(['siswa.user', 'siswa.kelas.jurusan', 'respons.siswaDipilih'])->findOrFail($id);
    }

    public function getBySiswa(int $siswaId): ?Sosiometri
    {
        return Sosiometri::where('siswa_id', $siswaId)->first();
    }

    public function create(array $data): Sosiometri
    {
        return Sosiometri::create($data);
    }

    public function update(int $id, array $data): Sosiometri
    {
        $sosiometri = Sosiometri::findOrFail($id);
        $sosiometri->update($data);

        return $sosiometri->fresh();
    }

    public function delete(int $id): bool
    {
        return Sosiometri::findOrFail($id)->delete();
    }

    public function query(): Builder
    {
        return Sosiometri::with(['siswa.user', 'siswa.kelas.jurusan'])->latest();
    }
}
