<?php

namespace App\Services\Bimbingan;

use App\Models\KasusBk;
use App\Models\KategoriKasus;
use App\Models\TahunAjaran;
use App\Repositories\Contracts\k\Bimbingan\BimbinganIndividuRepositoryInterface;
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
        $pegawai = app(PegawaiService::class)->getCurrentPegawai();
        if (!$pegawai) {
            throw ValidationException::withMessages(['guru_bk' => 'Data pegawai tidak ditemukan.']);
        }

        $data['guru_bk_id'] = $pegawai->id;
        $data['tahun_ajaran_id'] ??= TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id');

        $siswaId = $data['siswa_id'] ?? null;
        unset($data['siswa_id']);

        if ($siswaId) {
            // Selalu buat kasus BK baru
            $kasus = KasusBk::create([
                'siswa_id' => $siswaId,
                'guru_bk_id' => $pegawai->id,
                'tahun_ajaran_id' => $data['tahun_ajaran_id'],
                'kategori_id' => KategoriKasus::inRandomOrder()->value('id'),
                'penanganan' => $data['penanganan'] ?? 'Konseling Individu',
                'uraian_masalah' => $data['uraian_masalah'] ?? 'Konseling Individu',
                'tindak_lanjut' => $data['tindak_lanjut'] ?? null,
                'status' => 'Open',
                'prioritas' => 'Sedang',
                'tanggal_mulai' => $data['tanggal_layanan'] ?? now()->format('Y-m-d'),
            ]);
            $data['kasus_id'] = $kasus->id;
        }

        // Hapus field yang tidak ada di tabel bimbingan_individus (sudah di kasus_bk)
        unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut'], $data['guru_bk_id'], $data['tahun_ajaran_id']);

        return $this->repo->create($data);
    }

    public function update(int $id, array $data): \App\Models\BimbinganIndividu
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

        // Hapus field yang tidak ada di tabel bimbingan_individus (sudah di kasus_bk)
        unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut']);

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

        if (!empty($filters['jenis_kelamin'])) {
            $query->whereHas('kasus.siswa.user', fn($q) => $q->where('jenis_kelamin', $filters['jenis_kelamin']));
        }

        return $query->latest('tanggal_layanan')->get();
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
}
