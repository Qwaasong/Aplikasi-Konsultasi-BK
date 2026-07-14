<?php

namespace App\Services;

use App\Models\Konsultasi;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use Illuminate\Support\Collection;

class HomeVisitService
{
    public function getAll(): Collection
    {
        return Konsultasi::where('jenis_layanan', 'Kunjungan Rumah')
            ->with(['siswa.user', 'gurubk.user'])
            ->latest()
            ->get();
    }

    public function findById(int $id): Konsultasi
    {
        return Konsultasi::with(['siswa.user', 'gurubk.user'])->findOrFail($id);
    }

    public function create(array $data, array $files = []): Konsultasi
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();

        $data['guru_bk_id'] = $pegawai?->id;
        $data['jenis_layanan'] = 'Kunjungan Rumah';
        $data['tahun_ajaran_id'] ??= TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id');
        $data['status'] = 'Open';
        $data['prioritas'] = 'Sedang';

        return Konsultasi::create($data);
    }

    public function update(int $id, array $data, array $keptFiles = [], array $newFiles = []): Konsultasi
    {
        $record = Konsultasi::findOrFail($id);
        $record->update($data);

        return $record->fresh(['siswa.user', 'gurubk.user']);
    }

    public function delete(int $id): void
    {
        Konsultasi::findOrFail($id)->delete();
    }
}
