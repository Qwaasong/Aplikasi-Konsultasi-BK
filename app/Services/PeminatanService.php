<?php

namespace App\Services;

use App\Repositories\Contracts\PeminatanRepositoryInterface;
use Illuminate\Support\Collection;

class PeminatanService
{
    public function __construct(
        protected PeminatanRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?\App\Models\Peminatan
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): \App\Models\Peminatan
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): \App\Models\Peminatan
    {
        $this->repo->update($id, $data);

        return $this->repo->findById($id);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }
}
