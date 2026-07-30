<?php

namespace App\Services\Bk;

use App\Models\KasusBk;
use App\Models\KategoriKasus;
use App\Repositories\Contracts\Bk\HomeVisitRepositoryInterface;
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

        // Selalu buat kasus BK baru
        if ($siswaId) {
            $kasus = KasusBk::create([
                'siswa_id'      => $siswaId,
                'guru_bk_id'    => $gurubkId,
                'kategori_id'   => KategoriKasus::inRandomOrder()->value('id'),
                'penanganan'    => $data['penanganan'] ?? 'Kunjungan Rumah',
                'uraian_masalah'=> $data['uraian_masalah'] ?? '-',
                'tindak_lanjut' => $data['tindak_lanjut'] ?? null,
                'tanggal_mulai' => $data['tanggal_kunjungan'] ?? now()->toDateString(),
                'status'        => 'Open',
                'prioritas'     => 'Sedang',
            ]);

            $data['kasus_id'] = $kasus->id;
        }

        $data['guru_bk_id'] = $gurubkId;

        // Hapus field yang tidak ada di tabel home_visits (sudah di kasus_bk)
        unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut']);

        return $this->repo->create($data);
    }

    public function update(int $id, array $data): \App\Models\HomeVisit
    {
        $record = $this->repo->findById($id);

        unset($data['siswa_id']);

        // Simpan penanganan/uraian_masalah/tindak_lanjut ke kasus_bk
        if ($record->kasus_id) {
            KasusBk::where('id', $record->kasus_id)->update([
                'penanganan'    => $data['penanganan'] ?? null,
                'uraian_masalah' => $data['uraian_masalah'] ?? null,
                'tindak_lanjut'  => $data['tindak_lanjut'] ?? null,
            ]);
        }

        // Hapus field yang tidak ada di tabel home_visits (sudah di kasus_bk)
        unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut']);

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

    public function getFiltered(array $filters = []): Collection
    {
        $query = $this->repo->query();

        if (!empty($filters['guru_bk_id'])) {
            $query->where('guru_bk_id', $filters['guru_bk_id']);
        }

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->whereHas('kasus.siswa.user', fn($q) => $q->where('nama', 'like', "%{$keyword}%"));
        }

        if (!empty($filters['kelas'])) {
            $query->whereHas('kasus.siswa', fn($q) => $q->whereHas('kelas', fn($q2) => $q2->where('nama_kelas', $filters['kelas'])));
        }

        if (!empty($filters['jurusan'])) {
            $query->whereHas('kasus.siswa.kelas.jurusan', fn($q) => $q->where('nama_jurusan', $filters['jurusan']));
        }

        if (!empty($filters['jenis_kelamin'])) {
            $query->whereHas('kasus.siswa.user', fn($q) => $q->where('jenis_kelamin', $filters['jenis_kelamin']));
        }

        return $query->latest('tanggal_kunjungan')->get();
    }

    public function getFilterOptions(): array
    {
        $all = $this->getAll();

        return [
            'kelasOptions' => $all->pluck('kasus.siswa.kelas_label')->filter()->unique()->sort()->values()->toArray(),
            'jurusanOptions' => $all->pluck('kasus.siswa.jurusan_label')->filter()->unique()->sort()->values()->toArray(),
            'jenisKelaminOptions' => $all->pluck('kasus.siswa.jenis_kelamin')->filter()->unique()->values()->toArray(),
        ];
    }

    private function resolveGurubkId(): int
    {
        $pegawai = app(PegawaiService::class)->getCurrentPegawai();
        if (!$pegawai) {
            throw new \App\Exceptions\AuthorizationException('mengakses data pegawai/guru BK');
        }
        return $pegawai->id;
    }
}
