<?php

namespace App\Services;

use App\Models\KonferensiKasus;
use App\Models\KonferensiKasusPeserta;
use App\Models\KasusBk;
use App\Models\Pegawai;
use App\Models\KategoriKasus;
use Illuminate\Support\Collection;

class KonferensiKasusService
{
    public function getAll(): Collection
    {
        return KonferensiKasus::with([
            'kasus.siswa.user',
            'kasus.siswa.kelas.jurusan',
            'kasus.lampirans',
            'peserta',
        ])->latest('tanggal_konferensi')->get();
    }

    public function findById(int $id): ?KonferensiKasus
    {
        return KonferensiKasus::with([
            'kasus.siswa.user',
            'kasus.siswa.kelas.jurusan',
            'kasus.lampirans',
            'peserta',
        ])->findOrFail($id);
    }

    public function create(array $data, array $pesertaData = []): KonferensiKasus
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();

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
                    'penanganan'    => $data['uraian_masalah'] ?? 'Konferensi Kasus',
                    'uraian_masalah'=> $data['uraian_masalah'] ?? '-',
                    'tanggal_mulai' => $data['tanggal_konferensi'] ?? now()->toDateString(),
                    'status'        => 'Open',
                    'prioritas'     => 'Sedang',
                ]);
            }
            $data['kasus_id'] = $kasus->id;
        }

        $record = KonferensiKasus::create($data);

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

    public function update(int $id, array $data, array $pesertaData = []): KonferensiKasus
    {
        $record = KonferensiKasus::findOrFail($id);
        unset($data['siswa_id']);
        $record->update($data);

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

        return $record->fresh(['kasus.siswa.user', 'kasus.lampirans', 'peserta']);
    }

    public function delete(int $id): void
    {
        KonferensiKasus::findOrFail($id)->delete();
    }
}
