<?php

namespace App\Repositories\Eloquent\User;

use App\Models\Pegawai;
use App\Repositories\Contracts\User\PegawaiRepositoryInterface;
use Illuminate\Support\Collection;

class PegawaiRepository implements PegawaiRepositoryInterface
{
    /**
     * Ambil semua data pegawai beserta user dan kelas wali.
     */
    public function getAll(): Collection
    {
        return Pegawai::with(['user', 'kelasWali'])
            ->orderBy('nip')
            ->get();
    }

    /**
     * Hitung jumlah pegawai.
     */
    public function countPegawai(): int
    {
        return Pegawai::count();
    }

    /**
     * Cari pegawai berdasarkan ID.
     */
    public function findById(int $id): Pegawai
    {
        return Pegawai::with(['user', 'kelasWali'])
            ->findOrFail($id);
    }

    /**
     * Tambah data pegawai.
     */
    public function create(array $data): Pegawai
    {
        return Pegawai::create($data);
    }

    /**
     * Update data pegawai.
     */
    public function update(int $id, array $data): Pegawai
    {
        $pegawai = Pegawai::findOrFail($id);

        $pegawai->update($data);

        return $pegawai->fresh();
    }

    /**
     * Hapus data pegawai.
     */
    public function delete(int $id): bool
    {
        return Pegawai::findOrFail($id)->delete();
    }

    public function findByUserId(int $userId): ?Pegawai
    {
        return Pegawai::where('user_id', $userId)->first();
    }
}