<?php

namespace App\Repositories\Contracts\Bimbingan;

use App\Models\BimbinganKelompok;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface BimbinganKelompokRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?BimbinganKelompok;
    public function create(array $data): BimbinganKelompok;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function search(string $keyword, int $limit = 5): Collection;
    public function query(): Builder;
}