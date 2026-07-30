<?php

namespace App\Repositories\Contracts\Asesmen;

use App\Models\Dcm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface DcmRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Dcm;
    public function create(array $data): Dcm;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function query(): Builder;
}
