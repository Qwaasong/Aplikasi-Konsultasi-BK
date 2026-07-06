<?php

namespace App\Repositories\Eloquent;

use App\Models\DataSiswa;
use App\Models\Kelas;
use App\Models\Jurusan;
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
        return DataSiswa::with(['user', 'kelas.jurusan'])
            ->orderBy(DataSiswa::select('nama')->from('users')->whereColumn('users.id', 'data_siswa.user_id'))
            ->get();
    }

    public function countSiswa(): int
    {
        return DataSiswa::count();
    }

    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        $query = DataSiswa::with(['user', 'kelas.jurusan']);

        if (! empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (! empty($filters['kelas'])) {
            $query->whereHas('kelas', fn($q) => $q->where('id', (int) $filters['kelas']));
        }

        if (! empty($filters['jurusan'])) {
            $query->byJurusan($filters['jurusan']);
        }

        if (! empty($filters['jenis_kelamin'])) {
            $query->byJenisKelamin($filters['jenis_kelamin']);
        }

        if (! empty($filters['periode_ajaran'])) {
            $query->byPeriode($filters['periode_ajaran']);
        }

        $perPage = (int) ($filters['per_page'] ?? 15);

        return $query->orderBy(
            DataSiswa::select('nama')->from('users')->whereColumn('users.id', 'data_siswa.user_id')
        )->paginate($perPage);
    }

    public function search(string $keyword = '', int $limit = 50): Collection
    {
        $query = DataSiswa::with(['user', 'kelas.jurusan']);

        if (! empty($keyword)) {
            $query->search($keyword);
        }

        return $query->take($limit)->get();
    }

    public function findById(int $id): DataSiswa
    {
        return DataSiswa::with(['user', 'kelas.jurusan'])->findOrFail($id);
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

        $rows = array_map(function (array $row) use ($now) {
            return array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $rows);

        DataSiswa::upsert(
            $rows,
            ['nis'],
            ['kelas_id', 'alamat', 'periode_ajaran', 'updated_at']
        );

        return count($rows);
    }

    // ─────────────────────────────────────────
    // HELPER / DISTINCT
    // ─────────────────────────────────────────

    public function getJurusan(): Collection
    {
        return Jurusan::orderBy('nama_jurusan')->pluck('nama_jurusan');
    }

    public function getKelas(): Collection
    {
        return Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id');
    }

    public function getPeriode(): Collection
    {
        return DataSiswa::select('periode_ajaran')
            ->whereNotNull('periode_ajaran')
            ->distinct()
            ->orderByDesc('periode_ajaran')
            ->pluck('periode_ajaran');
    }

    // ─────────────────────────────────────────
    // STATS
    // ─────────────────────────────────────────

    public function getStats(): array
    {
        $total = DataSiswa::count();

        $laki = DataSiswa::whereHas('user', fn($q) => $q->where('jenis_kelamin', 'Laki-laki'))->count();
        $perempuan = DataSiswa::whereHas('user', fn($q) => $q->where('jenis_kelamin', 'Perempuan'))->count();

        $perKelas = DataSiswa::select('kelas_id', DB::raw('count(*) as total'))
            ->groupBy('kelas_id')
            ->get()
            ->mapWithKeys(function ($row) {
                $kelas = Kelas::find($row->kelas_id);
                return [$kelas?->nama_kelas ?? $row->kelas_id => $row->total];
            })
            ->toArray();

        $perJurusan = DataSiswa::select('kelas_id', DB::raw('count(*) as total'))
            ->groupBy('kelas_id')
            ->get()
            ->groupBy(fn($row) => Kelas::with('jurusan')->find($row->kelas_id)?->jurusan?->nama_jurusan ?? 'Unknown')
            ->map(fn($group) => $group->sum('total'))
            ->toArray();

        return compact('total', 'laki', 'perempuan', 'perKelas', 'perJurusan');
    }
}
