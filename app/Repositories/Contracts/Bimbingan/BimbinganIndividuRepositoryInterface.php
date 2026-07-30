<?php

namespace App\Repositories\Contracts\Bimbingan;

use App\Models\BimbinganIndividu;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface BimbinganIndividuRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?BimbinganIndividu;
    public function create(array $data): BimbinganIndividu;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function search(string $keyword, int $limit = 5): Collection;
    public function query(): Builder;
}
