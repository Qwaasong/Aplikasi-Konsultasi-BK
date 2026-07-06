<?php

namespace App\Repositories\Eloquent;

use App\Models\Konsultasi;
use App\Repositories\Contracts\KonsultasiRepositoryInterface;
use Illuminate\Support\Facades\DB;

class KonsultasiRepository implements KonsultasiRepositoryInterface
{
    public function countKonsultasi(): int
    {
        return Konsultasi::count();
    }

    public function getAll()
    {
        return Konsultasi::with(['siswa.user', 'gurubk.user', 'kategori', 'tahunAjaran'])
            ->latest()
            ->get();
    }

    public function getByGurubk(int $pegawaiId)
    {
        return Konsultasi::with(['siswa.user', 'gurubk.user', 'kategori'])
            ->where('guru_bk_id', $pegawaiId)
            ->latest()
            ->get();
    }

    public function findById(int $id): Konsultasi
    {
        return Konsultasi::with(['siswa.user', 'gurubk.user', 'kategori', 'tahunAjaran', 'lampirans', 'balasans.user'])
            ->findOrFail($id);
    }

    public function create(array $data): Konsultasi
    {
        return DB::transaction(function () use ($data) {
            $konsultasi = Konsultasi::create($data);

            return $konsultasi->fresh(['siswa.user', 'gurubk.user', 'kategori', 'tahunAjaran']);
        });
    }

    public function update(int $id, array $data): Konsultasi
    {
        return DB::transaction(function () use ($id, $data) {
            $konsultasi = Konsultasi::findOrFail($id);
            $konsultasi->update($data);

            return $konsultasi->fresh();
        });
    }

    public function delete(int $id): bool
    {
        return Konsultasi::findOrFail($id)->delete();
    }
}
