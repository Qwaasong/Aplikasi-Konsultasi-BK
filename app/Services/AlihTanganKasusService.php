<?php

namespace App\Services;

use App\Models\AlihtanganKasus;
use App\Models\BimbinganIndividu;
use App\Models\HomeVisit;
use App\Models\KasusBk;
use App\Models\Pegawai;
use Illuminate\Support\Collection;

class AlihTanganKasusService
{
    public function getAll(): Collection
    {
        return AlihtanganKasus::with([
            'kasus.siswa.user',
            'kasus.siswa.kelas.jurusan',
            'guruBkAsal.user',
            'guruBkTujuan.user',
        ])->latest('tanggal_alih')->get();
    }

    public function findById(int $id): ?AlihtanganKasus
    {
        return AlihtanganKasus::with([
            'kasus.siswa.user',
            'kasus.siswa.kelas.jurusan',
            'guruBkAsal.user',
            'guruBkTujuan.user',
        ])->findOrFail($id);
    }

    public function create(array $data): AlihtanganKasus
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();

        // Auto-set guru asal = current logged in pegawai
        $data['nama_asal'] = $pegawai?->id;

        $record = AlihtanganKasus::create($data);

        // Reassign guru_bk ke kasus terkait dan semua layanan terkait
        $this->reassignGuruBk($record->kasus_id, $record->nama_penerima);

        return $record->fresh(['kasus.siswa.user', 'guruBkAsal.user', 'guruBkTujuan.user']);
    }

    public function update(int $id, array $data): AlihtanganKasus
    {
        $record = AlihtanganKasus::findOrFail($id);

        $penerimaBerubah = isset($data['nama_penerima']) && $data['nama_penerima'] !== $record->nama_penerima;
        $record->update($data);

        if ($penerimaBerubah && $record->kasus_id) {
            $this->reassignGuruBk($record->kasus_id, $data['nama_penerima']);
        }

        return $record->fresh(['kasus.siswa.user', 'guruBkAsal.user', 'guruBkTujuan.user']);
    }

    public function delete(int $id): void
    {
        AlihtanganKasus::findOrFail($id)->delete();
    }

    /**
     * Reassign all records terkait ke guru BK baru.
     */
    private function reassignGuruBk(int $kasusId, int $newGuruBkId): void
    {
        KasusBk::where('id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        BimbinganIndividu::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        HomeVisit::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
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
