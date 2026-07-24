<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\User;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PegawaiService
{
    public function __construct(
        protected PegawaiRepositoryInterface $pegawaiRepository
    ) {}

    // ===========================
    // READ
    // ===========================

    public function getAll(): Collection
    {
        return $this->pegawaiRepository->getAll();
    }

    public function getTotalPegawai(): int
    {
        return $this->pegawaiRepository->countPegawai();
    }

    public function findById(int $id): Pegawai
    {
        return $this->pegawaiRepository->findById($id);
    }

    // ===========================
    // WRITE
    // ===========================

    public function create(array $data): Pegawai
    {
        $this->ensureNipUnique($data['nip']);

        $user = User::create([
            'nama'            => $data['nama'],
            'username'        => $data['username'] ?? strtolower(str_replace(' ', '', $data['nama'])),
            'email'           => $data['email'],
            'jenis_kelamin'   => $data['jenis_kelamin'],
            'no_hp'           => $data['no_hp'] ?? '-',
            'foto'            => '',
            'password'        => bcrypt('password'),
            'role'            => $data['role'] ?? 'pegawai',
            'status'          => $data['status'] ?? 'aktif',
        ]);

        return $this->pegawaiRepository->create([
            'user_id' => $user->id,
            'nip' => $data['nip'],
            'jabatan' => $data['jabatan'],
        ]);
    }

    public function update(int $id, array $data): Pegawai
    {
        $pegawai = $this->pegawaiRepository->findById($id);

        if ($pegawai->nip != $data['nip']) {
            $this->ensureNipUnique($data['nip']);
        }

        $pegawai->user->update([
            'nama'          => $data['nama'],
            'email'         => $data['email'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'no_hp'         => $data['no_hp'],
            'role'          => $data['role'],
            'status'        => $data['status'],
        ]);

        return $this->pegawaiRepository->update($id, [
            'nip'      => $data['nip'],
            'jabatan'  => $data['jabatan'],
        ]);
    }

    public function delete(int $id): void
    {
        $pegawai = $this->pegawaiRepository->findById($id);

        $pegawai->user()->delete();

        $this->pegawaiRepository->delete($id);
    }

    public function getFiltered(array $filters = []): Collection
    {
        $query = Pegawai::with('user');

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('user', fn($q2) => $q2->where('nama', 'like', "%{$keyword}%"))
                    ->orWhere('nip', 'like', "%{$keyword}%")
                    ->orWhere('jabatan', 'like', "%{$keyword}%");
            });
        }

        if (!empty($filters['jabatan'])) {
            $query->where('jabatan', $filters['jabatan']);
        }

        return $query->latest()->get();
    }

    public function getFilterOptions(): array
    {
        $all = $this->getAll();

        return [
            'jabatanOptions' => $all->pluck('jabatan')->filter()->unique()->sort()->values()->toArray(),
        ];
    }

    /**
     * Ambil pegawai untuk user yang sedang login.
     */
    public function getCurrentPegawai(): ?\App\Models\Pegawai
    {
        return $this->pegawaiRepository->findByUserId(auth()->id());
    }

    // ===========================
    // PRIVATE
    // ===========================

    private function ensureNipUnique(string|int $nip): void
    {
        if (Pegawai::where('nip', $nip)->exists()) {
            throw ValidationException::withMessages([
                'nip' => "NIP {$nip} sudah digunakan.",
            ]);
        }
    }
}