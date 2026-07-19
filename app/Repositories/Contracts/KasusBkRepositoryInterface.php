<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface KasusBkRepositoryInterface
{
    public function all(): Collection;
    public function getByGuruBk(int $guruBkId): Collection;
    public function getByTahunAjaran(int $tahunAjaranId): Collection;
    public function getBySiswa(int $siswaId): Collection;
    public function getByStatus(string $status): Collection;
    public function findById(int $id): ?\App\Models\KasusBk;
    public function create(array $data): \App\Models\KasusBk;
    public function update(int $id, array $data): \App\Models\KasusBk;
    public function delete(int $id): bool;
    public function countKasus(): int;
}