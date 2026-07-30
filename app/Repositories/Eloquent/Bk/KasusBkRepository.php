<?php

namespace App\Repositories\Eloquent\Bk;

use App\Models\KasusBk;
use App\Repositories\Contracts\Bk\KasusBkRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class KasusBkRepository implements KasusBkRepositoryInterface
{
    public function all(): Collection
    {
        return KasusBk::with(['siswa.user', 'guruBk.user', 'kategori', 'lampirans'])
            ->latest('tanggal_mulai')
            ->get();
    }

    public function getAll(): Collection
    {
        return $this->all();
    }

    public function findById(int $id): ?KasusBk
    {
        return KasusBk::with(['siswa.user', 'siswa.kelas.jurusan', 'guruBk.user', 'kategori', 'lampirans'])
            ->find($id);
    }

    public function create(array $data): KasusBk
    {
        return KasusBk::create($data);
    }

    public function update(int $id, array $data): KasusBk
    {
        $kasus = KasusBk::findOrFail($id);
        $kasus->update($data);
        return $kasus->fresh(['siswa', 'guruBk', 'kategori', 'lampirans']);
    }

    public function delete(int $id): bool
    {
        return KasusBk::destroy($id) > 0;
    }

    public function countKasus(): int
    {
        return KasusBk::count();
    }

    public function getByGuruBk(int $guruBkId): Collection
    {
        return KasusBk::with(['siswa.user', 'siswa.kelas.jurusan', 'kategori', 'lampirans'])
            ->where('guru_bk_id', $guruBkId)
            ->latest('tanggal_mulai')
            ->get();
    }

    public function getByTahunAjaran(int $tahunAjaranId): Collection
    {
        return KasusBk::with(['siswa.user', 'guruBk.user', 'kategori', 'lampirans'])
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->latest('tanggal_mulai')
            ->get();
    }

    public function getBySiswa(int $siswaId): Collection
    {
        return KasusBk::with(['guruBk.user', 'kategori', 'lampirans'])
            ->where('siswa_id', $siswaId)
            ->latest('tanggal_mulai')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return KasusBk::with(['siswa.user', 'guruBk.user', 'kategori', 'lampirans'])
            ->where('status', $status)
            ->latest('tanggal_mulai')
            ->get();
    }
}