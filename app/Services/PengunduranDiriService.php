<?php

namespace App\Services;

use App\Models\PengunduranDiri;
use App\Repositories\Contracts\PengunduranDiriRepositoryInterface;
use Illuminate\Support\Collection;

class PengunduranDiriService
{
    public function __construct(
        protected PengunduranDiriRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?PengunduranDiri
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): PengunduranDiri
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): PengunduranDiri
    {
        $this->repo->update($id, $data);

        return $this->repo->findById($id);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }

    public function search(string $keyword, int $limit = 5): Collection
    {
        return $this->repo->search($keyword, $limit);
    }
}
