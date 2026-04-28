<?php

namespace App\Repositories\Eloquent;

use App\Models\DataSiswa;
use App\Repositories\Contracts\SiswaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SiswaRepository implements SiswaRepositoryInterface
{
    // ─────────────────────────────────────────
    // READ
    // ─────────────────────────────────────────

    public function getAll(): Collection
    {
        return DataSiswa::orderBy('nama')->get();
    }

    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        $query = DataSiswa::query();

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['kelas'])) {
            $query->byKelas((int) $filters['kelas']);
        }

        if (!empty($filters['jurusan'])) {
            $query->byJurusan($filters['jurusan']);
        }

        if (!empty($filters['jenis_kelamin'])) {
            $query->byJenisKelamin($filters['jenis_kelamin']);
        }

        if (!empty($filters['periode_ajaran'])) {
            $query->byPeriode($filters['periode_ajaran']);
        }

        $perPage = (int) ($filters['per_page'] ?? 15);

        return $query->orderBy('nama')->paginate($perPage);
    }

    public function search(string $keyword = '', int $limit = 50): Collection
    {
        $query = DataSiswa::query();

        if (!empty($keyword)) {
            $query->search($keyword);
        }

        return $query->orderBy('nama')->take($limit)->get();
    }

    public function findById(int $id): DataSiswa
    {
        return DataSiswa::findOrFail($id);
    }

    public function findByNis(int $nis): ?DataSiswa
    {
        return DataSiswa::where('nis', $nis)->first();
    }

    // ─────────────────────────────────────────
    // WRITE
    // ─────────────────────────────────────────

    public function create(array $data): DataSiswa
    {
        return DataSiswa::create($data);
    }

    public function update(int $id, array $data): DataSiswa
    {
        $siswa = DataSiswa::findOrFail($id);
        $siswa->update($data);
        return $siswa->fresh();
    }

    public function delete(int $id): bool
    {
        $siswa = DataSiswa::findOrFail($id);
        return $siswa->delete();
    }

    /**
     * Bulk upsert berdasarkan kolom NIS.
     * Jika NIS sudah ada → update; belum ada → insert.
     * Mengembalikan jumlah baris yang diproses.
     */
    public function bulkUpsert(array $rows): int
    {
        if (empty($rows)) {
            return 0;
        }

        $now = now();

        // Tambahkan timestamp ke setiap baris
        $rows = array_map(function (array $row) use ($now) {
            return array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $rows);

        // Upsert by NIS – kolom yang di-update jika NIS sudah ada
        DataSiswa::upsert(
            $rows,
            ['nis'],                                  // unique key
            ['nama', 'kelas', 'jenis_kelamin', 'jurusan', 'periode_ajaran', 'updated_at']
        );

        return count($rows);
    }

    // ─────────────────────────────────────────
    // HELPER / DISTINCT
    // ─────────────────────────────────────────

    public function getJurusan(): Collection
    {
        return DataSiswa::select('jurusan')
            ->distinct()
            ->orderBy('jurusan')
            ->pluck('jurusan');
    }

    public function getKelas(): Collection
    {
        return DataSiswa::select('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');
    }

    public function getPeriode(): Collection
    {
        return DataSiswa::select('periode_ajaran')
            ->distinct()
            ->orderByDesc('periode_ajaran')
            ->pluck('periode_ajaran');
    }

    // ─────────────────────────────────────────
    // STATS
    // ─────────────────────────────────────────

    public function getStats(): array
    {
        $total     = DataSiswa::count();
        $laki      = DataSiswa::where('jenis_kelamin', 'Laki-laki')->count();
        $perempuan = DataSiswa::where('jenis_kelamin', 'Perempuan')->count();

        $perKelas = DataSiswa::select('kelas', DB::raw('count(*) as total'))
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->kelas => $row->total])
            ->toArray();

        $perJurusan = DataSiswa::select('jurusan', DB::raw('count(*) as total'))
            ->groupBy('jurusan')
            ->orderBy('jurusan')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->jurusan => $row->total])
            ->toArray();

        return compact('total', 'laki', 'perempuan', 'perKelas', 'perJurusan');
    }
}