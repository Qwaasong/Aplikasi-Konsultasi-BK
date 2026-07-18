<?php

namespace App\Services;

use App\Models\BimbinganIndividu;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;

class BimbinganIndividuService
{
    public function getAll(): Collection
    {
        return BimbinganIndividu::with(['guruBk.user', 'tahunAjaran'])
            ->latest('tanggal_layanan')
            ->get();
    }

    public function findById(int $id): ?BimbinganIndividu
    {
        return BimbinganIndividu::with(['guruBk.user', 'tahunAjaran'])
            ->findOrFail($id);
    }

    public function create(array $data): BimbinganIndividu
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();
        if (!$pegawai) {
            throw ValidationException::withMessages(['guru_bk' => 'Data pegawai tidak ditemukan.']);
        }

        $data['guru_bk_id'] = $pegawai->id;
        $data['tahun_ajaran_id'] ??= TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id');

        return BimbinganIndividu::create($data);
    }

    public function update(int $id, array $data): BimbinganIndividu
    {
        $record = BimbinganIndividu::findOrFail($id);
        $record->update($data);
        return $record->fresh(['guruBk.user', 'tahunAjaran']);
    }

    public function delete(int $id): void
    {
        BimbinganIndividu::findOrFail($id)->delete();
    }
}
