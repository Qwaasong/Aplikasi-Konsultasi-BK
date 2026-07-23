<?php

namespace App\Services;

use App\Models\KasusBk;
use App\Models\KategoriKasus;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use App\Repositories\Contracts\BimbinganIndividuRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class BimbinganIndividuService
{
    public function __construct(
        protected BimbinganIndividuRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?\App\Models\BimbinganIndividu
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): \App\Models\BimbinganIndividu
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();
        if (!$pegawai) {
            throw ValidationException::withMessages(['guru_bk' => 'Data pegawai tidak ditemukan.']);
        }

        $data['guru_bk_id'] = $pegawai->id;
        $data['tahun_ajaran_id'] ??= TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id');

        $siswaId = $data['siswa_id'] ?? null;
        unset($data['siswa_id']);

        if ($siswaId) {
            $kasus = KasusBk::firstOrCreate(
                ['siswa_id' => $siswaId],
                [
                    'guru_bk_id' => $pegawai->id,
                    'tahun_ajaran_id' => $data['tahun_ajaran_id'],
                    'kategori_id' => KategoriKasus::inRandomOrder()->value('id'),
                    'penanganan' => $data['uraian_masalah'] ?? 'Konseling Individu',
                    'uraian_masalah' => $data['uraian_masalah'] ?? 'Konseling Individu',
                    'status' => 'Open',
                    'prioritas' => 'Sedang',
                    'tanggal_mulai' => $data['tanggal_layanan'] ?? now()->format('Y-m-d'),
                ]
            );
            $data['kasus_id'] = $kasus->id;
        }

        return $this->repo->create($data);
    }

    public function update(int $id, array $data): \App\Models\BimbinganIndividu
    {
        $record = $this->repo->findById($id);

        unset($data['siswa_id']);

        $this->repo->update($id, $data);

        return $record->fresh(['guruBk.user', 'tahunAjaran', 'kasus.siswa.user', 'kasus.siswa.kelas.jurusan']);
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
