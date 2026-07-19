<?php

namespace App\Services;

use App\Models\HomeVisit;
use App\Models\KasusBk;
use App\Models\Pegawai;
use App\Models\KategoriKasus;
use Illuminate\Support\Collection;

class HomeVisitService
{
    public function getAll(): Collection
    {
        return HomeVisit::with(['kasus.siswa.user', 'kasus.siswa.kelas.jurusan', 'kasus.lampirans', 'guruBk.user'])
            ->latest('tanggal_kunjungan')
            ->get();
    }

    public function getByGurubk(?int $pegawaiId = null): Collection
    {
        $id = $pegawaiId ?? $this->resolveGurubkId();
        return HomeVisit::with(['kasus.siswa.user', 'kasus.lampirans', 'guruBk.user'])
            ->where('guru_bk_id', $id)
            ->latest('tanggal_kunjungan')
            ->get();
    }

    public function findById(int $id): ?HomeVisit
    {
        return HomeVisit::with(['kasus.siswa.user', 'kasus.siswa.kelas.jurusan', 'kasus.lampirans', 'guruBk.user'])
            ->find($id);
    }

    public function create(array $data): HomeVisit
    {
        $gurubkId = $this->resolveGurubkId();

        // Resolve kasus_id dari siswa_id
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

        return HomeVisit::create($data);
    }

    public function update(int $id, array $data): HomeVisit
    {
        $record = HomeVisit::findOrFail($id);

        unset($data['siswa_id']);

        $record->update($data);
        return $record->fresh(['kasus.siswa.user', 'kasus.lampirans', 'guruBk.user']);
    }

    public function delete(int $id): void
    {
        HomeVisit::findOrFail($id)->delete();
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
