<?php

namespace App\Services\MasterData;

use App\Repositories\Contracts\MasterData\SekolahRepositoryInterface;
use App\Models\Sekolah;
use Illuminate\Support\Collection;

class SekolahService
{
    public function __construct(
        protected SekolahRepositoryInterface $repo
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
        $query = Sekolah::query();

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_sekolah', 'like', "%{$keyword}%")
                    ->orWhere('alamat', 'like', "%{$keyword}%")
                    ->orWhere('telepon', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        return $query->latest()->get();
    }

    public function getFilterOptions(): array
    {
        return [];
    }
}
