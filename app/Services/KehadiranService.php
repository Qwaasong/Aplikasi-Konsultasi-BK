<?php

namespace App\Services;

use App\Models\Kehadiran;
use App\Repositories\Contracts\KehadiranRepositoryInterface;
use Illuminate\Support\Collection;

class KehadiranService
{
    public function __construct(
        protected KehadiranRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?Kehadiran
    {
        return $this->repo->findById($id);
    }

    public function findByIdForCurrentUser(int $id): Kehadiran
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Kehadiran
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }

    public function getFiltered(array $filters = []): Collection
    {
        $query = $this->repo->query();

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->whereHas('siswa.user', fn($q) => $q->where('nama', 'like', "%{$keyword}%"));
        }

        if (!empty($filters['kelas'])) {
            $query->whereHas('siswa', fn($q) => $q->whereHas('kelas', fn($q2) => $q2->where('nama_kelas', $filters['kelas'])));
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['tanggal'])) {
            $query->whereDate('tanggal_kehadiran', $filters['tanggal']);
        }

        if (!empty($filters['tahun'])) {
            $query->whereHas('tahunAjaran', fn($q) => $q->where('tahun', $filters['tahun']));
        }

        return $query->latest('tanggal_kehadiran')->get();
    }

    public function getFilterOptions(): array
    {
        $all = $this->getAll();

        return [
            'kelasOptions' => $all->pluck('siswa.kelas_label')->filter()->unique()->sort()->values()->toArray(),
            'statusOptions' => $all->pluck('status')->filter()->unique()->values()->toArray(),
            'tahunOptions' => $all->pluck('tahunAjaran.tahun')->filter()->unique()->values()->toArray(),
        ];
    }
}
