<?php

namespace App\Services;

use App\Models\Kehadiran;
use Illuminate\Support\Collection;

class KehadiranService
{
    public function getAll(): Collection
    {
        return Kehadiran::with(['siswa.user', 'tahunAjaran'])
            ->latest('tanggal_kehadiran')
            ->get();
    }

    public function findById(int $id): ?Kehadiran
    {
        return Kehadiran::find($id);
    }

    public function findByIdForCurrentUser(int $id): Kehadiran
    {
        return Kehadiran::findOrFail($id);
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
        return (bool) Kehadiran::findOrFail($id)->delete();
    }
}
