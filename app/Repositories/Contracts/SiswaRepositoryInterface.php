<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SiswaRepositoryInterface
{
    /**
     * Ambil semua siswa tanpa pagination.
    */
    public function getAll(): Collection;
    
    /**
     * Hitung total siswa.
     */
    public function countSiswa(): int;

    /**
     * Ambil siswa dengan filter + pagination.
     *
     * @param  array{
     *   search?: string,
     *   kelas?: int,
     *   jurusan?: string,
     *   jenis_kelamin?: string,
     *   periode_ajaran?: string,
     *   per_page?: int,
     * }  $filters
     */
    public function getPaginated(array $filters = []): LengthAwarePaginator;

    /**
     * Cari siswa berdasarkan kata kunci (untuk dropdown konsultasi).
     */
    public function search(string $keyword = '', int $limit = 50): Collection;

    /**
     * Temukan siswa berdasarkan ID.
     */
    public function findById(int $id): \App\Models\DataSiswa;

    /**
     * Temukan siswa berdasarkan NIS.
     */
    public function findByNis(int $nis): ?\App\Models\DataSiswa;

    /**
     * Buat siswa baru.
     */
    public function create(array $data): \App\Models\DataSiswa;

    /**
     * Perbarui data siswa.
     */
    public function update(int $id, array $data): \App\Models\DataSiswa;

    /**
     * Hapus siswa.
     */
    public function delete(int $id): bool;

    /**
     * Import banyak siswa sekaligus (bulk insert / upsert by NIS).
     *
     * @param  array<int, array>  $rows
     */
    public function bulkUpsert(array $rows): int;

    /**
     * Daftar jurusan yang tersedia (distinct).
     */
    public function getJurusan(): Collection;

    /**
     * Daftar kelas yang tersedia (distinct).
     */
    public function getKelas(): Collection;

    /**
     * Daftar periode ajaran yang tersedia (distinct).
     */
    public function getPeriode(): Collection;

    /**
     * Statistik ringkasan untuk dashboard.
     *
     * @return array{total: int, laki: int, perempuan: int, per_kelas: array, per_jurusan: array}
     */
    public function getStats(): array;

}