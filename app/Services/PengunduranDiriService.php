<?php

namespace App\Services;

use App\Models\PengunduranDiri;
use App\Repositories\Contracts\PengunduranDiriRepositoryInterface;
use Illuminate\Support\Collection;

class PengunduranDiriService
{
    public function __construct(
        protected PengunduranDiriRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?PengunduranDiri
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): PengunduranDiri
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): PengunduranDiri
    {
        $this->repo->update($id, $data);

        return $this->repo->findById($id);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }

    public function search(string $keyword, int $limit = 5): Collection
    {
        return $this->repo->search($keyword, $limit);
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

        if (!empty($filters['jurusan'])) {
            $query->whereHas('siswa.kelas.jurusan', fn($q) => $q->where('nama_jurusan', $filters['jurusan']));
        }

        return $query->latest()->get();
    }

    public function getFilterOptions(): array
    {
        $all = $this->getAll();

        return [
            'kelasOptions' => $all->pluck('siswa.kelas_label')->filter()->unique()->sort()->values()->toArray(),
            'jurusanOptions' => $all->pluck('siswa.jurusan_label')->filter()->unique()->sort()->values()->toArray(),
        ];
    }
}
