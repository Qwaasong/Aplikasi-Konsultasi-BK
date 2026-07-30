<?php

namespace App\Repositories\Eloquent\Bimbingan;

use App\Models\BimbinganIndividu;
use App\Repositories\Contracts\k\BimbinganIndividuRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BimbinganIndividuRepository implements BimbinganIndividuRepositoryInterface
{
    public function getAll(): Collection
    {
        return BimbinganIndividu::with([
            'kasus.guruBk.user',
            'kasus.tahunAjaran',
            'kasus.siswa.user',
            'kasus.siswa.kelas.jurusan',
        ])->latest('tanggal_layanan')->get();
    }

    public function findById(int $id): ?BimbinganIndividu
    {
        return BimbinganIndividu::with([
            'kasus.guruBk.user',
            'kasus.tahunAjaran',
            'kasus.siswa.user',
            'kasus.siswa.kelas.jurusan',
        ])->findOrFail($id);
    }

    public function create(array $data): BimbinganIndividu
    {
        return BimbinganIndividu::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return BimbinganIndividu::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return BimbinganIndividu::findOrFail($id)->delete();
    }

    public function search(string $keyword, int $limit = 5): Collection
    {
        return BimbinganIndividu::with('kasus.siswa.user')
            ->whereHas('kasus', fn($q) => $q->where('uraian_masalah', 'like', "%{$keyword}%"))
            ->take($limit)
            ->get();
    }

    public function query(): Builder
    {
        return BimbinganIndividu::with([
            'kasus.guruBk.user',
            'kasus.tahunAjaran',
            'kasus.siswa.user',
            'kasus.siswa.kelas.jurusan',
        ]);
    }
}
