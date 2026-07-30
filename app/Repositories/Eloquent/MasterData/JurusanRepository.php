<?php

namespace App\Repositories\Eloquent\MasterData;

use App\Models\Jurusan;
use App\Repositories\Contracts\MasterData\JurusanRepositoryInterface;
use Illuminate\Support\Collection;

class JurusanRepository implements JurusanRepositoryInterface
{
    /**
     * Ambil semua data jurusan beserta sekolah.
     */
    public function getAll(): Collection
    {
        return Jurusan::with('sekolah')
            ->orderBy('nama_jurusan')
            ->get();
    }

    /**
     * Hitung jumlah jurusan.
     */
    public function countJurusan(): int
    {
        return Jurusan::count();
    }

    /**
     * Cari jurusan berdasarkan ID.
     */
    public function findById(int $id): Jurusan
    {
        return Jurusan::with('sekolah')->findOrFail($id);
    }

    /**
     * Ambil jurusan berdasarkan sekolah.
     */
    public function getBySekolah(int $sekolahId): Collection
    {
        return Jurusan::where('sekolah_id', $sekolahId)
            ->orderBy('nama_jurusan')
            ->get();
    }

    /**
     * Tambah data jurusan.
     */
    public function create(array $data): Jurusan
    {
        return Jurusan::create($data);
    }

    /**
     * Update data jurusan.
     */
    public function update(int $id, array $data): Jurusan
    {
        $jurusan = Jurusan::findOrFail($id);

        $jurusan->update($data);

        return $jurusan->fresh();
    }

    /**
     * Hapus data jurusan.
     */
    public function delete(int $id): bool
    {
        return Jurusan::findOrFail($id)->delete();
    }
}