<?php

namespace App\Services;

use App\Models\KonferensiKasusPeserta;
use App\Models\KasusBk;
use App\Models\KategoriKasus;
use App\Repositories\Contracts\KonferensiKasusRepositoryInterface;
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

    public function findById(int $id): ?\App\Models\KonferensiKasus
    {
        return $this->repo->findById($id);
    }

    public function create(array $data, array $pesertaData = []): \App\Models\KonferensiKasus
    {
        $pegawai = app(PegawaiService::class)->getCurrentPegawai();

        $siswaId = $data['siswa_id'] ?? null;
        unset($data['siswa_id']);

        if ($siswaId && !isset($data['kasus_id'])) {
            $kasus = KasusBk::where('siswa_id', $siswaId)
                ->where('status', 'Open')
                ->latest()
                ->first();

            if (!$kasus) {
                $kasus = KasusBk::create([
                    'siswa_id'      => $siswaId,
                    'guru_bk_id'    => $pegawai?->id ?? 1,
                    'kategori_id'   => KategoriKasus::inRandomOrder()->value('id'),
                    'penanganan'    => $data['penanganan'] ?? 'Konferensi Kasus',
                    'uraian_masalah'=> $data['uraian_masalah'] ?? '-',
                    'tindak_lanjut' => $data['tindak_lanjut'] ?? null,
                    'tanggal_mulai' => $data['tanggal_konferensi'] ?? now()->toDateString(),
                    'status'        => 'Open',
                    'prioritas'     => 'Sedang',
                ]);
            }
            $data['kasus_id'] = $kasus->id;
        }

        // Hapus field yang tidak ada di tabel konferensi_kasus (sudah di kasus_bk)
        unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut']);

        $record = $this->repo->create($data);

        if (!empty($pesertaData)) {
            $insert = collect($pesertaData)->map(fn($p) => [
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

    public function update(int $id, array $data, array $pesertaData = []): \App\Models\KonferensiKasus
    {
        $this->repo->update($id, $data);

        if (!empty($pesertaData)) {
            KonferensiKasusPeserta::where('konferensi_kasus_id', $id)->delete();
            $insert = collect($pesertaData)->map(fn($p) => [
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

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('kasus', fn($q2) => $q2->where('uraian_masalah', 'like', "%{$keyword}%"))
                    ->orWhereHas('kasus.siswa.user', fn($q2) => $q2->where('nama', 'like', "%{$keyword}%"));
            });
        }

        if (!empty($filters['kelas'])) {
            $query->whereHas('kasus.siswa', fn($q) => $q->whereHas('kelas', fn($q2) => $q2->where('nama_kelas', $filters['kelas'])));
        }

        if (!empty($filters['jurusan'])) {
            $query->whereHas('kasus.siswa.kelas.jurusan', fn($q) => $q->where('nama_jurusan', $filters['jurusan']));
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
}
