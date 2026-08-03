<?php

namespace App\Services\MasterData;

use App\Repositories\Contracts\MasterData\KelasRepositoryInterface;
use App\Models\Kelas;
use Illuminate\Support\Collection;

class KelasService
{
    public function __construct(
        protected KelasRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id)
    {
        return $this->repo->findById($id);
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }

    /** Complex filtered queries still use Model directly — ponytail: push to repo when query() method is added */
    public function getFiltered(array $filters = []): Collection
    {
        $query = Kelas::with(['jurusan.sekolah', 'waliKelas.user']);

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_kelas', 'like', "%{$keyword}%")
                    ->orWhere('tingkat', 'like', "%{$keyword}%")
                    ->orWhereHas('jurusan', fn($q2) => $q2->where('nama_jurusan', 'like', "%{$keyword}%"))
                    ->orWhereHas('jurusan.sekolah', fn($q2) => $q2->where('nama_sekolah', 'like', "%{$keyword}%"))
                    ->orWhereHas('waliKelas.user', fn($q2) => $q2->where('nama', 'like', "%{$keyword}%"));
            });
        }

        if (!empty($filters['sekolah'])) {
            $query->whereHas('jurusan.sekolah', fn($q) => $q->where('nama_sekolah', $filters['sekolah']));
        }

        if (!empty($filters['jurusan'])) {
            $query->whereHas('jurusan', fn($q) => $q->where('nama_jurusan', $filters['jurusan']));
        }

        if (!empty($filters['tingkat'])) {
            $query->where('tingkat', $filters['tingkat']);
        }

        return $query->latest()->get();
    }

    public function getFilterOptions(): array
    {
        $all = $this->getAll();

        return [
            'sekolahOptions' => $all->pluck('sekolah.nama_sekolah')->filter()->unique()->sort()->values()->toArray(),
            'jurusanOptions' => $all->pluck('jurusan.nama_jurusan')->filter()->unique()->sort()->values()->toArray(),
            'tingkatOptions' => $all->pluck('tingkat')->filter()->unique()->sort()->values()->toArray(),
        ];
    }
}
