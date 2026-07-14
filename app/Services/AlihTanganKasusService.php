<?php

namespace App\Services;

use App\Models\AlihtanganKasus;
use App\Models\Pegawai;
use Illuminate\Support\Collection;

class AlihTanganKasusService
{
    public function getAll(): Collection
    {
        return AlihtanganKasus::with(['konsultasi', 'guruBkAsal.user'])->latest()->get();
    }

    public function findById(int $id): AlihtanganKasus
    {
        return AlihtanganKasus::with(['konsultasi', 'guruBkAsal.user'])->findOrFail($id);
    }

    public function create(array $data, array $files = []): AlihtanganKasus
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();
        if ($pegawai) {
            $data['guru_bk_asal_id'] = $pegawai->id;
        }

        return AlihtanganKasus::create($data);
    }

    public function update(int $id, array $data, array $keptFiles = [], array $newFiles = []): AlihtanganKasus
    {
        $record = AlihtanganKasus::findOrFail($id);
        $record->update($data);

        return $record->fresh(['konsultasi', 'guruBkAsal.user']);
    }

    public function delete(int $id): void
    {
        AlihtanganKasus::findOrFail($id)->delete();
    }
}
