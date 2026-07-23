<?php

namespace App\Repositories\Contracts;

use App\Models\AlihtanganKasus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface AlihtanganKasusRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?AlihtanganKasus;
    public function create(array $data): AlihtanganKasus;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function search(string $keyword, int $limit = 5): Collection;
    public function query(): Builder;
}
