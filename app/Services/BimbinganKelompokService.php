<?php

namespace App\Services;

use App\Models\BimbinganKelompok;
use App\Models\TahunAjaran;
use App\Models\Pegawai;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class BimbinganKelompokService
{
    public function getAll(): Collection
    {
        return BimbinganKelompok::with(['guruBk.user', 'tahunAjaran'])->latest()->get();
    }

    public function findById(int $id): BimbinganKelompok
    {
        return BimbinganKelompok::with(['guruBk.user', 'tahunAjaran'])->findOrFail($id);
    }

    public function create(array $data, array $files = []): BimbinganKelompok
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();
        if (!$pegawai) {
            throw ValidationException::withMessages(['guru_bk' => 'Data pegawai tidak ditemukan.']);
        }

        $data['guru_bk_id'] = $pegawai->id;
        $data['tahun_ajaran_id'] ??= TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id');

        $record = BimbinganKelompok::create($data);

        // Handle file uploads if any
        $this->saveFiles($record->id, $files);

        return $record->fresh(['guruBk.user', 'tahunAjaran']);
    }

    public function update(int $id, array $data, array $keptFiles = [], array $newFiles = []): BimbinganKelompok
    {
        $record = BimbinganKelompok::findOrFail($id);
        $record->update($data);

        return $record->fresh(['guruBk.user', 'tahunAjaran']);
    }

    public function delete(int $id): void
    {
        BimbinganKelompok::findOrFail($id)->delete();
    }

    protected function saveFiles(int $id, array $files): void
    {
        // Stub for file handling
    }
}
