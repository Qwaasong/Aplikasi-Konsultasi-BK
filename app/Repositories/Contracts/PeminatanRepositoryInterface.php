<?php

namespace App\Repositories\Contracts;

use App\Models\Peminatan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface PeminatanRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Peminatan;
    public function create(array $data): Peminatan;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function query(): Builder;
}
