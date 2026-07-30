<?php

namespace App\Repositories\Contracts\Asesmen;

use App\Models\Akpd;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface AkpdRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Akpd;
    public function create(array $data): Akpd;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function query(): Builder;
}
