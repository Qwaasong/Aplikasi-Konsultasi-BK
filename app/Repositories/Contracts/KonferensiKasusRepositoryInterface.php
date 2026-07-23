<?php

namespace App\Repositories\Contracts;

use App\Models\KonferensiKasus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface KonferensiKasusRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?KonferensiKasus;
    public function create(array $data): KonferensiKasus;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function search(string $keyword, int $limit = 5): Collection;
    public function query(): Builder;
}
