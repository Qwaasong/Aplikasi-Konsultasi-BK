<?php

namespace App\Services\Bk;

use App\Models\KasusBk;
use App\Models\KonferensiKasus;
use App\Models\KonferensiKasusPeserta;
use App\Repositories\Contracts\Bk\KonferensiKasusRepositoryInterface;
use App\Services\User\PegawaiService;
use Illuminate\Support\Collection;

class KonferensiKasusService
{
    public function __construct(
        protected KonferensiKasusRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?KonferensiKasus
    {
        return $this->repo->findById($id);
    }

    public function create(array $data, array $pesertaData = []): KonferensiKasus
    {
        $pegawai = app(PegawaiService::class)->getCurrentPegawai();

        // Konferensi kasus = follow-up dari kasus yang sudah ada
        // kasus_id langsung dari form (user pilih kasus)
        $data['guru_bk_id'] = $data['guru_bk_id'] ?? $pegawai?->id;

        // Hapus field yang tidak ada di tabel konferensi_kasus (sudah di kasus_bk)
        unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut'], $data['siswa_id']);

        $record = $this->repo->create($data);

        if (! empty($pesertaData)) {
            $insert = collect($pesertaData)->map(fn ($p) => [
                'konferensi_kasus_id' => $record->id,
                'nama_peserta' => $p['nama_peserta'],
                'peran_peserta' => $p['peran_peserta'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            KonferensiKasusPeserta::insert($insert->toArray());
        }

        return $record->fresh(['kasus.siswa.user', 'kasus.lampirans', 'peserta']);
    }

    public function update(int $id, array $data, array $pesertaData = []): KonferensiKasus
    {
        $record = $this->repo->findById($id);

        $this->repo->update($id, $data);

        if (! empty($pesertaData)) {
            KonferensiKasusPeserta::where('konferensi_kasus_id', $id)->delete();
            $insert = collect($pesertaData)->map(fn ($p) => [
                'konferensi_kasus_id' => $id,
                'nama_peserta' => $p['nama_peserta'],
                'peran_peserta' => $p['peran_peserta'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            KonferensiKasusPeserta::insert($insert->toArray());
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

    public function getFiltered(array $filters = []): Collection
    {
        $query = $this->repo->query();

        if (! empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('kasus', fn ($q2) => $q2->where('uraian_masalah', 'like', "%{$keyword}%"))
                    ->orWhereHas('kasus.siswa.user', fn ($q2) => $q2->where('nama', 'like', "%{$keyword}%"));
            });
        }

        if (! empty($filters['kelas'])) {
            $query->whereHas('kasus.siswa', fn ($q) => $q->whereHas('kelas', fn ($q2) => $q2->where('nama_kelas', $filters['kelas'])));
        }

        if (! empty($filters['jurusan'])) {
            $query->whereHas('kasus.siswa.kelas.jurusan', fn ($q) => $q->where('nama_jurusan', $filters['jurusan']));
        }

        return $query->latest('tanggal_konferensi')->get();
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
     * Ambil daftar kasus BK yang tersedia untuk dipilih (status Open, milik guru BK saat ini).
     */
    public function getKasusOptions(): Collection
    {
        $pegawai = app(PegawaiService::class)->getCurrentPegawai();

        return KasusBk::with(['siswa.user', 'siswa.kelas.jurusan', 'kategori'])
            ->where('guru_bk_id', $pegawai?->id)
            ->where('status', 'Open')
            ->latest('tanggal_mulai')
            ->get()
            ->map(fn ($k) => [
                'id' => $k->id,
                'nama_siswa' => $k->siswa->user->nama ?? '-',
                'nis' => $k->siswa->nis ?? '-',
                'kelas_label' => $k->siswa->kelas_label ?? '-',
                'penanganan' => $k->penanganan ?? '-',
                'kategori' => $k->kategori->nama_kategori ?? '-',
                'tanggal_mulai' => optional($k->tanggal_mulai)->format('d M Y'),
                'prioritas' => $k->prioritas ?? '-',
            ]);
    }
}
