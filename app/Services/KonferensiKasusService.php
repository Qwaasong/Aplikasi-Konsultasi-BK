<?php

namespace App\Services;

use App\Models\KonferensiKasus;

class KonferensiKasusService
{
    public function getAll()
    {
        return KonferensiKasus::with(['konsultasi.siswa.user'])->latest()->get();
    }

    public function findById(int $id): KonferensiKasus
    {
        return KonferensiKasus::with(['konsultasi.siswa.user'])->findOrFail($id);
    }

    public function create(array $data): KonferensiKasus
    {
        return KonferensiKasus::create($data);
    }

    public function update(int $id, array $data): KonferensiKasus
    {
        $record = KonferensiKasus::findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    public function delete(int $id): void
    {
        KonferensiKasus::findOrFail($id)->delete();
    }
}
