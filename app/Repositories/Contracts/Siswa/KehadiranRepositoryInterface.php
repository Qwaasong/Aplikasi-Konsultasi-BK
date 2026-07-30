<?php

namespace App\Repositories\Contracts;

use App\Models\Kehadiran;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface KehadiranRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Kehadiran;
    public function create(array $data): Kehadiran;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function query(): Builder;
    public function bulkUpsert(array $rows): int;
}
