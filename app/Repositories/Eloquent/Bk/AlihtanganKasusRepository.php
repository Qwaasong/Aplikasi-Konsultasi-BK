<?php

namespace App\Repositories\Eloquent\Bk;

use App\Models\AlihtanganKasus;
use App\Repositories\Contracts\l\AlihtanganKasusRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AlihtanganKasusRepository implements AlihtanganKasusRepositoryInterface
{
    public function getAll(): Collection
    {
        return AlihtanganKasus::with([
            'kasus.siswa.user',
            'kasus.siswa.kelas.jurusan',
            'guruBkAsal.user',
            'guruBkTujuan.user',
        ])->latest('tanggal_alih')->get();
    }

    public function findById(int $id): ?AlihtanganKasus
    {
        return AlihtanganKasus::with([
            'kasus.siswa.user',
            'kasus.siswa.kelas.jurusan',
            'guruBkAsal.user',
            'guruBkTujuan.user',
        ])->findOrFail($id);
    }

    public function create(array $data): AlihtanganKasus
    {
        return AlihtanganKasus::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return AlihtanganKasus::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return AlihtanganKasus::findOrFail($id)->delete();
    }

    public function search(string $keyword, int $limit = 5): Collection
    {
        return AlihtanganKasus::with('kasus.siswa.user')
            ->whereHas('kasus.siswa.user', fn($q) => $q->where('nama', 'like', "%{$keyword}%"))
            ->take($limit)
            ->get();
    }

    public function query(): Builder
    {
        return AlihtanganKasus::with([
            'kasus.siswa.user',
            'kasus.siswa.kelas.jurusan',
            'guruBkAsal.user',
            'guruBkTujuan.user',
        ]);
    }
}
