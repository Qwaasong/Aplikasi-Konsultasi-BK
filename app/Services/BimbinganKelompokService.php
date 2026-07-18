<?php

namespace App\Services;

use App\Models\BimbinganKelompok;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use Illuminate\Support\Collection;

class BimbinganKelompokService
{
    public function getAll(): Collection
    {
        return BimbinganKelompok::with(['guruBk.user', 'tahunAjaran'])
            ->latest('tanggal_layanan')
            ->get();
    }

    public function findById(int $id): ?BimbinganKelompok
    {
        return BimbinganKelompok::with(['guruBk.user', 'tahunAjaran'])
            ->findOrFail($id);
    }

    public function create(array $data): BimbinganKelompok
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();
        $data['guru_bk_id'] = $pegawai?->id;
        $data['tahun_ajaran_id'] ??= TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id');

        return BimbinganKelompok::create($data);
    }

    public function update(int $id, array $data): BimbinganKelompok
    {
        $record = BimbinganKelompok::findOrFail($id);
        $record->update($data);
        return $record->fresh(['guruBk.user', 'tahunAjaran']);
    }

    public function delete(int $id): void
    {
        BimbinganKelompok::findOrFail($id)->delete();
    }
}
