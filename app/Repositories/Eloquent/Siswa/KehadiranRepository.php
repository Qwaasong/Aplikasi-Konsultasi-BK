<?php

namespace App\Repositories\Eloquent\Siswa;

use App\Models\Kehadiran;
use App\Repositories\Contracts\Siswa\KehadiranRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class KehadiranRepository implements KehadiranRepositoryInterface
{
    public function getAll(): Collection
    {
        return Kehadiran::with(['siswa.user', 'siswa.kelas.jurusan', 'tahunAjaran'])
            ->latest('tanggal_kehadiran')
            ->get();
    }

    public function findById(int $id): ?Kehadiran
    {
        return Kehadiran::with(['siswa.user', 'siswa.kelas.jurusan', 'tahunAjaran'])
            ->find($id);
    }

    public function create(array $data): Kehadiran
    {
        return Kehadiran::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Kehadiran::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Kehadiran::findOrFail($id)->delete();
    }

    public function query(): Builder
    {
        return Kehadiran::with(['siswa.user', 'siswa.kelas.jurusan', 'tahunAjaran']);
    }

    public function bulkUpsert(array $rows): int
    {
        if (empty($rows)) {
            return 0;
        }

        foreach ($rows as $row) {
            Kehadiran::updateOrCreate(
                [
                    'siswa_id' => $row['siswa_id'],
                    'tanggal_kehadiran' => $row['tanggal_kehadiran'],
                    'tahun_ajaran_id' => $row['tahun_ajaran_id'],
                ],
                [
                    'status' => $row['status'],
                ]
            );
        }

        return count($rows);
    }
}
