<?php

namespace App\Services;

use App\Models\HomeVisit;
use App\Models\Pegawai;
use Illuminate\Support\Collection;

class HomeVisitService
{
    public function getAll(): Collection
    {
        return HomeVisit::with(['kasus.siswa.user', 'guruBk.user'])
            ->latest('tanggal_kunjungan')
            ->get();
    }

    public function getByGurubk(?int $pegawaiId = null): Collection
    {
        $id = $pegawaiId ?? $this->resolveGurubkId();
        return HomeVisit::with(['kasus.siswa.user', 'guruBk.user'])
            ->where('guru_bk_id', $id)
            ->latest('tanggal_kunjungan')
            ->get();
    }

    public function findById(int $id): ?HomeVisit
    {
        return HomeVisit::with(['kasus.siswa.user', 'kasus.siswa.kelas.jurusan', 'guruBk.user'])
            ->find($id);
    }

    public function create(array $data): HomeVisit
    {
        $data['guru_bk_id'] = $data['guru_bk_id'] ?? $this->resolveGurubkId();
        return HomeVisit::create($data);
    }

    public function update(int $id, array $data): HomeVisit
    {
        $record = HomeVisit::findOrFail($id);
        $record->update($data);
        return $record->fresh(['kasus.siswa.user', 'guruBk.user']);
    }

    public function delete(int $id): void
    {
        HomeVisit::findOrFail($id)->delete();
    }

    private function resolveGurubkId(): int
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();
        if (!$pegawai) {
            abort(403, 'Akun ini tidak terdaftar sebagai pegawai/guru BK.');
        }
        return $pegawai->id;
    }
}
