<?php

namespace App\Services;

use App\Models\KasusBk;
use App\Models\KategoriKasus;
use App\Models\Pegawai;
use App\Repositories\Contracts\HomeVisitRepositoryInterface;
use Illuminate\Support\Collection;

class HomeVisitService
{
    public function __construct(
        protected HomeVisitRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function getByGurubk(?int $pegawaiId = null): Collection
    {
        $id = $pegawaiId ?? $this->resolveGurubkId();

        return $this->repo->query()
            ->where('guru_bk_id', $id)
            ->latest('tanggal_kunjungan')
            ->get();
    }

    public function findById(int $id): ?\App\Models\HomeVisit
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): \App\Models\HomeVisit
    {
        $gurubkId = $this->resolveGurubkId();

        $siswaId = $data['siswa_id'] ?? null;
        unset($data['siswa_id']);

        if ($siswaId) {
            $kasus = KasusBk::where('siswa_id', $siswaId)
                ->where('status', 'Open')
                ->latest()
                ->first();

            if (!$kasus) {
                $kasus = KasusBk::create([
                    'siswa_id'      => $siswaId,
                    'guru_bk_id'    => $gurubkId,
                    'kategori_id'   => KategoriKasus::inRandomOrder()->value('id'),
                    'penanganan'    => $data['penanganan'] ?? 'Kunjungan Rumah',
                    'uraian_masalah'=> $data['uraian_masalah'] ?? '-',
                    'tanggal_mulai' => $data['tanggal_kunjungan'] ?? now()->toDateString(),
                    'status'        => 'Open',
                    'prioritas'     => 'Sedang',
                ]);
            }

            $data['kasus_id'] = $kasus->id;
        }

        $data['guru_bk_id'] = $gurubkId;

        return $this->repo->create($data);
    }

    public function update(int $id, array $data): \App\Models\HomeVisit
    {
        $record = $this->repo->findById($id);

        unset($data['siswa_id']);

        $this->repo->update($id, $data);

        return $record->fresh(['kasus.siswa.user', 'kasus.lampirans', 'guruBk.user']);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }

    public function search(string $keyword, int $limit = 5): Collection
    {
        return $this->repo->search($keyword, $limit);
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
