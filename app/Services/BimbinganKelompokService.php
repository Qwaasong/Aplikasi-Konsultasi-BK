<?php

namespace App\Services;

use App\Models\BimbinganKelompokSiswa;
use App\Models\KasusBk;
use App\Models\KategoriKasus;
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
        $pegawai = app(PegawaiService::class)->getCurrentPegawai();
        $data['guru_bk_id'] = $pegawai?->id;
        $data['tahun_ajaran_id'] ??= TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id');

        // Selalu buat kasus BK baru (gunakan siswa pertama sebagai referensi)
        $siswaId = !empty($siswaIds) ? $siswaIds[0] : null;
        $kasus = KasusBk::create([
            'siswa_id'       => $siswaId,
            'guru_bk_id'     => $pegawai?->id,
            'tahun_ajaran_id'=> $data['tahun_ajaran_id'],
            'kategori_id'    => KategoriKasus::inRandomOrder()->value('id'),
            'penanganan'     => $data['penanganan'] ?? 'Bimbingan Kelompok',
            'uraian_masalah' => $data['uraian_masalah'] ?? '-',
            'tindak_lanjut'  => $data['tindak_lanjut'] ?? null,
            'tanggal_mulai'  => $data['tanggal_layanan'] ?? now()->toDateString(),
            'status'         => 'Open',
            'prioritas'      => 'Sedang',
        ]);
        $data['kasus_id'] = $kasus->id;

        // Hapus field yang tidak ada di tabel bimbingan_kelompok (sudah di kasus_bk)
        unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut']);

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
        $record = $this->repo->findById($id);

        // Simpan penanganan/uraian_masalah/tindak_lanjut ke kasus_bk
        if ($record->kasus_id) {
            \App\Models\KasusBk::where('id', $record->kasus_id)->update([
                'penanganan'    => $data['penanganan'] ?? null,
                'uraian_masalah' => $data['uraian_masalah'] ?? null,
                'tindak_lanjut'  => $data['tindak_lanjut'] ?? null,
            ]);
        }

        // Hapus field yang tidak ada di tabel bimbingan_kelompok (sudah di kasus_bk)
        unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut']);

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

    public function getFiltered(array $filters = []): Collection
    {
        $query = $this->repo->query();

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->whereHas('kasus', fn($q) => $q->where('uraian_masalah', 'like', "%{$keyword}%"));
        }

        if (!empty($filters['kelas'])) {
            $query->whereHas('siswa.siswa', fn($q) => $q->whereHas('kelas', fn($q2) => $q2->where('nama_kelas', $filters['kelas'])));
        }

        if (!empty($filters['jurusan'])) {
            $query->whereHas('siswa.siswa.kelas.jurusan', fn($q) => $q->where('nama_jurusan', $filters['jurusan']));
        }

        if (!empty($filters['jenis_kelamin'])) {
            $query->whereHas('siswa.siswa', fn($q) => $q->where('jenis_kelamin', $filters['jenis_kelamin']));
        }

        return $query->latest('tanggal_layanan')->get();
    }

    public function getFilterOptions(): array
    {
        $all = $this->getAll();

        return [
            'kelasOptions' => $all->pluck('siswa')->flatten()->pluck('siswa.kelas_label')->filter()->unique()->sort()->values()->toArray(),
            'jurusanOptions' => $all->pluck('siswa')->flatten()->pluck('siswa.jurusan_label')->filter()->unique()->sort()->values()->toArray(),
            'jenisKelaminOptions' => $all->pluck('siswa')->flatten()->pluck('siswa.jenis_kelamin')->filter()->unique()->values()->toArray(),
        ];
    }
}
