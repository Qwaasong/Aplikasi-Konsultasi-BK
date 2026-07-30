<?php

namespace App\Repositories\Eloquent\User;

use App\Models\User;
use App\Repositories\Contracts\User\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function getAll(): Collection
    {
        return User::orderBy('nama')->get();
    }

    public function countUsers(string $role): int
    {
        return User::where('role', $role)->count();
    }

    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        $query = User::query();

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                  ->orWhere('username', 'like', "%{$keyword}%");
            });
        }

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        $perPage = (int) ($filters['per_page'] ?? 15);

        return $query->orderBy('nama')->paginate($perPage);
    }

    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function findByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        // Default values untuk kolom yang tidak dikirim form
        $data['email'] ??= $data['username'] . '@sekolah.sch.id';
        $data['jenis_kelamin'] ??= 'L';
        $data['no_hp'] ??= '-';
        $data['foto'] ??= '';
        $data['status'] ??= 'aktif';
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return $user->fresh();
    }

    public function delete(int $id): bool
    {
        return User::findOrFail($id)->delete();
    }

    public function getRoles(): Collection
    {
        return collect(User::select('role')->distinct()->orderBy('role')->pluck('role'));
    }
}