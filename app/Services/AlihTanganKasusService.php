<?php

namespace App\Services;

use App\Models\BimbinganIndividu;
use App\Models\BimbinganKelompok;
use App\Models\HomeVisit;
use App\Models\KasusBk;
use App\Models\KonferensiKasus;
use App\Models\Pegawai;
use App\Repositories\Contracts\AlihtanganKasusRepositoryInterface;
use Illuminate\Support\Collection;

class AlihTanganKasusService
{
    public function __construct(
        protected AlihtanganKasusRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?\App\Models\AlihtanganKasus
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): \App\Models\AlihtanganKasus
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();

        $data['nama_asal'] = $pegawai?->id;

        $record = $this->repo->create($data);

        $this->reassignGuruBk($record->kasus_id, $record->nama_penerima);

        return $record->fresh(['kasus.siswa.user', 'guruBkAsal.user', 'guruBkTujuan.user']);
    }

    public function update(int $id, array $data): \App\Models\AlihtanganKasus
    {
        $record = $this->repo->findById($id);

        $penerimaBerubah = isset($data['nama_penerima']) && $data['nama_penerima'] !== $record->nama_penerima;
        $this->repo->update($id, $data);

        if ($penerimaBerubah && $record->kasus_id) {
            $this->reassignGuruBk($record->kasus_id, $data['nama_penerima']);
        }

        return $record->fresh(['kasus.siswa.user', 'guruBkAsal.user', 'guruBkTujuan.user']);
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

        if (!empty($filters['tanggal'])) {
            $query->whereDate('tanggal_alih', $filters['tanggal']);
        }

        return $query->latest('tanggal_alih')->get();
    }

    public function getFilterOptions(): array
    {
        $all = $this->getAll();

        return [
            'kelasOptions' => $all->pluck('kasus.siswa.kelas_label')->filter()->unique()->sort()->values()->toArray(),
            'jurusanOptions' => $all->pluck('kasus.siswa.jurusan_label')->filter()->unique()->sort()->values()->toArray(),
        ];
    }

    /**
     * Reassign all records terkait ke guru BK baru.
     */
    private function reassignGuruBk(int $kasusId, int $newGuruBkId): void
    {
        KasusBk::where('id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        BimbinganIndividu::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        BimbinganKelompok::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        HomeVisit::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        KonferensiKasus::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
    }

    /**
     * Get available kasus for current guru BK.
     */
    public function getKasusOptions(): Collection
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();

        return KasusBk::with(['siswa.user', 'kategori'])
            ->where('guru_bk_id', $pegawai?->id)
            ->where('status', 'Open')
            ->latest('tanggal_mulai')
            ->get()
            ->map(fn($k) => [
                'id' => $k->id,
                'nama_siswa' => $k->siswa->nama ?? '-',
                'nis' => $k->siswa->nis ?? '-',
                'kelas_label' => $k->siswa->kelas_label ?? '-',
                'penanganan' => $k->penanganan,
                'kategori' => $k->kategori->nama_kategori ?? '-',
                'tanggal_mulai' => $k->tanggal_mulai?->format('d M Y'),
                'prioritas' => $k->prioritas,
            ]);
    }

    /**
     * Get all pegawai with role guru_bk for selection.
     */
    public function getGuruBkOptions(): Collection
    {
        return Pegawai::with('user')
            ->whereHas('user', fn($q) => $q->where('role', 'guru_bk'))
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'nama' => $p->user->nama,
                'nip' => $p->nip,
            ]);
    }
}
