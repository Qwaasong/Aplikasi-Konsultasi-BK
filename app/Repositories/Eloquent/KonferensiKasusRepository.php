<?php

namespace App\Repositories\Eloquent;

use App\Models\KonferensiKasus;
use App\Repositories\Contracts\KonferensiKasusRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class KonferensiKasusRepository implements KonferensiKasusRepositoryInterface
{
    public function getAll(): Collection
    {
        return KonferensiKasus::with(['kasus.siswa.user', 'kasus.siswa.kelas.jurusan', 'kasus.lampirans', 'peserta'])
            ->latest('tanggal_konferensi')
            ->get();
    }

    public function findById(int $id): ?KonferensiKasus
    {
        return KonferensiKasus::with(['kasus.siswa.user', 'kasus.siswa.kelas.jurusan', 'kasus.lampirans', 'peserta'])
            ->findOrFail($id);
    }

    public function create(array $data): KonferensiKasus
    {
        return KonferensiKasus::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return KonferensiKasus::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return KonferensiKasus::findOrFail($id)->delete();
    }

    public function search(string $keyword, int $limit = 5): Collection
    {
        return KonferensiKasus::with('kasus.siswa.user')
            ->where('uraian_masalah', 'like', "%{$keyword}%")
            ->take($limit)
            ->get();
    }

    public function query(): Builder
    {
        return KonferensiKasus::with(['kasus.siswa.user', 'kasus.siswa.kelas.jurusan', 'kasus.lampirans', 'peserta']);
    }
}
