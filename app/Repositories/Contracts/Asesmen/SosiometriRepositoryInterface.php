<?php

namespace App\Repositories\Contracts\Asesmen;

use App\Models\Sosiometri;
use Illuminate\Support\Collection;

interface SosiometriRepositoryInterface
{
    public function getAll(): Collection;
    public function countSosiometri(): int;
    public function findById(int $id): Sosiometri;
    public function getBySiswa(int $siswaId): ?Sosiometri;
    public function create(array $data): Sosiometri;
    public function update(int $id, array $data): Sosiometri;
    public function delete(int $id): bool;
}
