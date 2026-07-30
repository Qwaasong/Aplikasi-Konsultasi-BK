<?php

namespace App\Services\MasterData;

use App\Repositories\Contracts\MasterData\TahunAjaranRepositoryInterface;
use App\Models\TahunAjaran;
use Illuminate\Support\Collection;

class TahunAjaranService
{
    public function __construct(
        protected TahunAjaranRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id)
    {
        return $this->repo->findById($id);
    }

    public function getActive()
    {
        return $this->repo->getActive();
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
        $query = TahunAjaran::query();

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('tahun', 'like', "%{$keyword}%")
                    ->orWhere('semester', 'like', "%{$keyword}%");
            });
        }

        return $query->latest()->get();
    }

    public function getFilterOptions(): array
    {
        return [];
    }
}
