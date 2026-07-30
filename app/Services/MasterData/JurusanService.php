<?php

namespace App\Services\MasterData;

use App\Repositories\Contracts\MasterData\JurusanRepositoryInterface;
use App\Models\Jurusan;
use Illuminate\Support\Collection;

class JurusanService
{
    public function __construct(
        protected JurusanRepositoryInterface $repo
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
        $query = Jurusan::with('sekolah');

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('kode_jurusan', 'like', "%{$keyword}%")
                    ->orWhere('nama_jurusan', 'like', "%{$keyword}%")
                    ->orWhereHas('sekolah', fn($q2) => $q2->where('nama_sekolah', 'like', "%{$keyword}%"));
            });
        }

        if (!empty($filters['sekolah'])) {
            $query->whereHas('sekolah', fn($q) => $q->where('nama_sekolah', $filters['sekolah']));
        }

        return $query->latest()->get();
    }

    public function getFilterOptions(): array
    {
        $all = $this->getAll();

        return [
            'sekolahOptions' => $all->pluck('sekolah.nama_sekolah')->filter()->unique()->sort()->values()->toArray(),
        ];
    }
}
