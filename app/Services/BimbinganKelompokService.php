<?php

namespace App\Services;

use App\Models\BimbinganKelompok;
use App\Models\BimbinganKelompokSiswa;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use Illuminate\Support\Collection;

class BimbinganKelompokService
{
    public function getAll(): Collection
    {
        return BimbinganKelompok::with([
            'guruBk.user',
            'tahunAjaran',
            'siswa.siswa.user',
        ])
            ->latest('tanggal_layanan')
            ->get();
    }

    public function findById(int $id): ?BimbinganKelompok
    {
        return BimbinganKelompok::with([
            'guruBk.user',
            'tahunAjaran',
            'siswa.siswa.user',
        ])
            ->findOrFail($id);
    }

    public function create(array $data, array $siswaIds = []): BimbinganKelompok
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();
        $data['guru_bk_id'] = $pegawai?->id;
        $data['tahun_ajaran_id'] ??= TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id');
        $data['kasus_id'] = $data['kasus_id'] ?? null;

        $record = BimbinganKelompok::create($data);

        // Simpan peserta bimbingan kelompok (many-to-many)
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

    public function update(int $id, array $data, array $siswaIds = []): BimbinganKelompok
    {
        $record = BimbinganKelompok::findOrFail($id);
        $record->update($data);

        // Update peserta: hapus semua yang lama, insert yang baru
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

        return $record->fresh(['guruBk.user', 'tahunAjaran', 'siswa.siswa.user']);
    }

    public function delete(int $id): void
    {
        BimbinganKelompok::findOrFail($id)->delete();
    }
}
