<?php

namespace App\Repositories\Contracts;

use App\Models\GayaBelajar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface GayaBelajarRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?GayaBelajar;
    public function create(array $data): GayaBelajar;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function query(): Builder;
}
