<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function getAll(): Collection;
    
    public function countUsers(String $role): int;
    
    public function getPaginated(array $filters = []): LengthAwarePaginator;

    public function findById(int $id): User;

    public function findByUsername(string $username): ?User;

    public function create(array $data): User;

    public function update(int $id, array $data): User;

    public function delete(int $id): bool;

    public function getRoles(): Collection;

}