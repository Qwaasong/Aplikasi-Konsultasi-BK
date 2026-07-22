<?php

namespace App\Services;

use App\Models\Peminatan;
use App\Models\Pegawai;
use Illuminate\Support\Collection;

class PeminatanService
{
    public function getAll(): Collection
    {
        return Peminatan::with(['siswa.user', 'siswa.kelas.jurusan'])
            ->latest('tanggal')
            ->get();
    }

    public function findById(int $id): ?Peminatan
    {
        return Peminatan::with(['siswa.user', 'siswa.kelas.jurusan'])
            ->find($id);
    }

    public function create(array $data): Peminatan
    {
        return Peminatan::create($data);
    }

    public function update(int $id, array $data): Peminatan
    {
        $record = Peminatan::findOrFail($id);
        $record->update($data);
        return $record->fresh(['siswa.user', 'siswa.kelas.jurusan']);
    }

    public function delete(int $id): void
    {
        Peminatan::findOrFail($id)->delete();
    }
}
