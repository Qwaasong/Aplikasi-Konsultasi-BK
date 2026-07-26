<?php

namespace App\Repositories\Eloquent;

use App\Models\BimbinganKelompok;
use App\Repositories\Contracts\BimbinganKelompokRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BimbinganKelompokRepository implements BimbinganKelompokRepositoryInterface
{
    public function getAll(): Collection
    {
        return BimbinganKelompok::with(['guruBk.user', 'tahunAjaran', 'siswa.siswa.user', 'kasus.siswa.user'])
            ->latest('tanggal_layanan')
            ->get();
    }

    public function findById(int $id): ?BimbinganKelompok
    {
        return BimbinganKelompok::with(['guruBk.user', 'tahunAjaran', 'siswa.siswa.user', 'kasus.siswa.user'])
            ->find($id);
    }

    public function create(array $data): BimbinganKelompok
    {
        return BimbinganKelompok::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return BimbinganKelompok::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return BimbinganKelompok::findOrFail($id)->delete();
    }

    public function search(string $keyword, int $limit = 5): Collection
    {
        return BimbinganKelompok::with(['siswa.siswa.user', 'kasus.siswa.user'])
            ->whereHas('kasus', fn($q) => $q->where('uraian_masalah', 'like', "%{$keyword}%"))
            ->take($limit)
            ->get();
    }

    public function query(): Builder
    {
        return BimbinganKelompok::with(['guruBk.user', 'tahunAjaran', 'siswa.siswa.user', 'kasus.siswa.user']);
    }
}
