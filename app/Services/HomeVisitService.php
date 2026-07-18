<?php

namespace App\Services;

use App\Models\HomeVisit;
use App\Models\KasusBk;
use App\Models\Pegawai;
use Illuminate\Support\Collection;

class HomeVisitService
{
    public function getAll(): Collection
    {
        return HomeVisit::with(['kasus.siswa.user', 'guruBk.user'])
            ->latest('tanggal_kunjungan')
            ->get();
    }

    public function getByGurubk(?int $pegawaiId = null): Collection
    {
        $id = $pegawaiId ?? $this->resolveGurubkId();
        return HomeVisit::with(['kasus.siswa.user', 'guruBk.user'])
            ->where('guru_bk_id', $id)
            ->latest('tanggal_kunjungan')
            ->get();
    }

    public function findById(int $id): ?HomeVisit
    {
        return HomeVisit::with(['kasus.siswa.user', 'kasus.siswa.kelas.jurusan', 'guruBk.user'])
            ->find($id);
    }

    public function create(array $data): HomeVisit
    {
        $mapped = $this->mapFields($data);
        $mapped['guru_bk_id'] = $mapped['guru_bk_id'] ?? $this->resolveGurubkId();
        return HomeVisit::create($mapped);
    }

    public function update(int $id, array $data): HomeVisit
    {
        $record = HomeVisit::findOrFail($id);
        $mapped = $this->mapFields($data);
        $record->update($mapped);
        return $record->fresh(['kasus.siswa.user', 'guruBk.user']);
    }

    public function delete(int $id): void
    {
        HomeVisit::findOrFail($id)->delete();
    }

    /**
     * Map legacy/modal field names to actual DB columns.
     * Also resolves kasus_id from siswa_id if needed.
     */
    private function mapFields(array $data): array
    {
        $mapped = [];

        // Resolve kasus_id from siswa_id
        if (isset($data['siswa_id']) && !isset($data['kasus_id'])) {
            $gurubkId = $data['guru_bk_id'] ?? $this->resolveGurubkId();
            $kasus = KasusBk::where('siswa_id', $data['siswa_id'])
                ->where('status', 'Open')
                ->latest()
                ->first();

            if (!$kasus) {
                $kasus = KasusBk::create([
                    'siswa_id'    => $data['siswa_id'],
                    'guru_bk_id'  => $gurubkId,
                    'penanganan'  => $data['judul'] ?? ($data['isi_konsultasi'] ?? 'Kunjungan Rumah'),
                    'uraian_masalah' => $data['isi_konsultasi'] ?? ($data['judul'] ?? '-'),
                    'tanggal_mulai' => $data['tanggal_konsultasi'] ?? now()->toDateString(),
                    'status'      => 'Open',
                    'prioritas'   => $data['prioritas'] ?? 'Sedang',
                ]);
            }

            $mapped['kasus_id'] = $kasus->id;
        } elseif (isset($data['kasus_id'])) {
            $mapped['kasus_id'] = $data['kasus_id'];
        }

        // Map field names
        $mapped['tanggal_kunjungan'] = $data['tanggal_kunjungan']
            ?? $data['tanggal_konsultasi']
            ?? now()->toDateString();

        $mapped['uraian_masalah'] = $data['uraian_masalah']
            ?? $data['isi_konsultasi']
            ?? '';

        $mapped['penanganan'] = $data['penanganan']
            ?? $data['judul']
            ?? '';

        $mapped['tindak_lanjut'] = $data['tindak_lanjut']
            ?? $data['hasil_tindak_lanjut']
            ?? null;

        // Map status: modal uses Open/Diproses/Selesai → DB uses diproses/ditunda/dibatalkan
        $statusMap = [
            'Open'     => 'diproses',
            'Diproses' => 'diproses',
            'Selesai'  => 'diproses',
        ];
        $rawStatus = $data['status'] ?? 'Open';
        $mapped['status'] = $statusMap[$rawStatus] ?? 'diproses';

        if (isset($data['guru_bk_id'])) {
            $mapped['guru_bk_id'] = $data['guru_bk_id'];
        }

        return $mapped;
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
