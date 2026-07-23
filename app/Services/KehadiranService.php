<?php

namespace App\Services;

use App\Models\Kehadiran;
use App\Repositories\Contracts\KehadiranRepositoryInterface;
use Illuminate\Support\Collection;

class KehadiranService
{
    public function __construct(
        protected KehadiranRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?Kehadiran
    {
        return $this->repo->findById($id);
    }

    public function findByIdForCurrentUser(int $id): Kehadiran
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Kehadiran
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }
}
