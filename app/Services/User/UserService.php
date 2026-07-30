<?php

namespace App\Services\User;

use App\Models\Pegawai;
use App\Models\User;
use App\Repositories\Contracts\i\S\User\UserRepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->userRepository->getAll();
    }

    public function getTotalUser()
    {
        return $this->userRepository->getAll()->count();
    }

    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        return $this->userRepository->getPaginated($filters);
    }

    public function findById(int $id): User
    {
        return $this->userRepository->findById($id);
    }

    public function getRoles(): Collection
    {
        return $this->userRepository->getRoles();
    }

    public function getStats(): array
    {
        $all   = $this->userRepository->getAll();
        $total = $all->count();
        $admin = $all->where('role', 'admin')->count();
        $konselor = $all->where('role', 'guru_bk')->count();

        return compact('total', 'admin', 'konselor');
    }

    public function create(array $data): User
    {
        $this->ensureUsernameUnique($data['username']);

        return $this->userRepository->create($data);
    }

    public function update(int $id, array $data): User
    {
        $existing = $this->userRepository->findById($id);

        if ($data['username'] !== $existing->username) {
            $this->ensureUsernameUnique($data['username']);
        }

        return $this->userRepository->update($id, $data);
    }

    public function delete(int $id): void
    {
        // Jangan hapus diri sendiri
        if ($id === auth()->id()) {
            throw ValidationException::withMessages([
                'user' => 'Anda tidak dapat menghapus akun yang sedang digunakan.',
            ]);
        }

        $this->userRepository->delete($id);
    }

    private function ensureUsernameUnique(string $username): void
    {
        if ($this->userRepository->findByUsername($username)) {
            throw ValidationException::withMessages([
                'username' => "Username '{$username}' sudah digunakan oleh pengguna lain.",
            ]);
        }
    }

    /**
     * Register user baru + buat entri pegawai otomatis.
     */
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['foto'] = '';

        $user = $this->userRepository->create($data);

        if (in_array($user->role, ['guru_bk', 'admin'])) {
            Pegawai::create([
                'user_id' => $user->id,
                'nip' => $this->generateNip($user),
                'jabatan' => $user->role === 'admin' ? 'Admin' : 'Guru BK',
            ]);
        }

        event(new Registered($user));

        return $user;
    }

    private function generateNip(User $user): string
    {
        $prefix = $user->role === 'admin' ? 'ADM' : 'GBK';
        $count = Pegawai::count() + 1;
        $date = date('Ymd');

        return "{$prefix}{$date}{$count}";
    }
}