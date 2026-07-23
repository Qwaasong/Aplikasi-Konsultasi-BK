<?php

namespace App\Repositories\Eloquent;

use App\Models\HomeVisit;
use App\Repositories\Contracts\HomeVisitRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class HomeVisitRepository implements HomeVisitRepositoryInterface
{
    public function getAll(): Collection
    {
        return HomeVisit::with(['kasus.siswa.user', 'kasus.siswa.kelas.jurusan', 'kasus.lampirans', 'guruBk.user'])
            ->latest('tanggal_kunjungan')
            ->get();
    }

    public function findById(int $id): ?HomeVisit
    {
        return HomeVisit::with(['kasus.siswa.user', 'kasus.siswa.kelas.jurusan', 'kasus.lampirans', 'guruBk.user'])
            ->find($id);
    }

    public function create(array $data): HomeVisit
    {
        return HomeVisit::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return HomeVisit::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return HomeVisit::findOrFail($id)->delete();
    }

    public function search(string $keyword, int $limit = 5): Collection
    {
        return HomeVisit::with(['kasus.siswa.user', 'guruBk.user'])
            ->whereHas('kasus.siswa.user', fn($q) => $q->where('nama', 'like', "%{$keyword}%"))
            ->take($limit)
            ->get();
    }

    public function query(): Builder
    {
        return HomeVisit::with(['kasus.siswa.user', 'kasus.siswa.kelas.jurusan', 'kasus.lampirans', 'guruBk.user']);
    }
}
