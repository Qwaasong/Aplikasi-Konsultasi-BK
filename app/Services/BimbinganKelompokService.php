<?php

namespace App\Services;

use App\Models\BimbinganKelompokSiswa;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use App\Repositories\Contracts\BimbinganKelompokRepositoryInterface;
use Illuminate\Support\Collection;

class BimbinganKelompokService
{
    public function __construct(
        protected BimbinganKelompokRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?\App\Models\BimbinganKelompok
    {
        return $this->repo->findById($id);
    }

    public function create(array $data, array $siswaIds = []): \App\Models\BimbinganKelompok
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();
        $data['guru_bk_id'] = $pegawai?->id;
        $data['tahun_ajaran_id'] ??= TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id');
        $data['kasus_id'] = $data['kasus_id'] ?? null;

        $record = $this->repo->create($data);

        if (!empty($siswaIds)) {
            $peserta = collect($siswaIds)->map(fn($id) => [
                'bimbingan_kelompok_id' => $record->id,
                'siswa_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            BimbinganKelompokSiswa::insert($peserta->toArray());
        }

        return $record->fresh(['guruBk.user', 'tahunAjaran', 'siswa.siswa.user']);
    }

    public function update(int $id, array $data, array $siswaIds = []): \App\Models\BimbinganKelompok
    {
        $this->repo->update($id, $data);

        if (!empty($siswaIds)) {
            BimbinganKelompokSiswa::where('bimbingan_kelompok_id', $id)->delete();
            $peserta = collect($siswaIds)->map(fn($sid) => [
                'bimbingan_kelompok_id' => $id,
                'siswa_id' => $sid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            BimbinganKelompokSiswa::insert($peserta->toArray());
        }

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
}
