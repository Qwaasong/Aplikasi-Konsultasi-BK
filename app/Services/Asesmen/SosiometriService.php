<?php

namespace App\Services\Asesmen;

use App\Models\DataSiswa;
use App\Models\Sosiometri;
use App\Repositories\Contracts\Asesmen\SosiometriRepositoryInterface;
use App\Services\ImportExportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SosiometriService
{
    public function __construct(
        protected SosiometriRepositoryInterface $repo,
        protected ImportExportService $importExportService
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?Sosiometri
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Sosiometri
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): Sosiometri
    {
        $this->repo->update($id, $data);

        return $this->repo->findById($id);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }

    public function search(string $keyword): Collection
    {
        return Sosiometri::with('siswa')
            ->whereHas('siswa', function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                ->orWhere('nis', 'like', "%{$keyword}%");
            })
            ->limit(10)
            ->get();
    }

    public function getFiltered(array $filters = []): Collection
    {
        $query = $this->repo->query();

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('siswa.user', fn($q2) => $q2->where('nama', 'like', "%{$keyword}%"))
                    ->orWhere('judul', 'like', "%{$keyword}%");
            });
        }

        if (!empty($filters['kelas'])) {
            $query->whereHas('siswa', fn($q) => $q->whereHas('kelas', fn($q2) => $q2->where('nama_kelas', $filters['kelas'])));
        }

        if (!empty($filters['tingkat'])) {
            $query->whereHas('siswa.kelas', fn($q) => $q->where('tingkat', $filters['tingkat']));
        }

        if (!empty($filters['jurusan'])) {
            $query->whereHas('siswa.kelas.jurusan', fn($q) => $q->where('nama_jurusan', $filters['jurusan']));
        }

        return $query->latest()->get();
    }

    public function getFilterOptions(): array
    {
        $all = $this->getAll();

        return [
            'kelasOptions' => $all->pluck('siswa.kelas_label')->filter()->unique()->sort()->values()->toArray(),
            'jurusanOptions' => $all->pluck('siswa.jurusan_label')->filter()->unique()->sort()->values()->toArray(),
        ];
    }

    // ===========================
    // IMPORT / EXPORT
    // ===========================

    public function importFromFile(UploadedFile $file): array
    {
        $rows = $this->importExportService->parseUploadedFile($file);

        return $this->validateAndImport($rows);
    }

    /**
     * Format gform (disamakan karena belum ada contoh klien):
     *   Timestamp | Nama Lengkap | Kelas | Tahun Pelajaran | Q1. <pertanyaan> .. Q5. <pertanyaan>
     * Sel Qn = nama teman dipisah koma.
     */
    public function exportRows(array $filters = []): array
    {
        return $this->getFiltered($filters)->each->load('respons.siswaDipilih')->map(function (Sosiometri $s) {
            $row = [
                'Timestamp' => $s->created_at?->format('Y-m-d') ?? '',
                'Nama Lengkap' => $s->siswa?->nama ?? '',
                'Kelas' => $s->siswa?->kelas_label ?? '',
                'Tahun Pelajaran' => $s->created_at?->format('Y') ?? '',
            ];

            $respons = $s->respons ?? collect();

            foreach (Sosiometri::PERTANYAAN as $key => $pertanyaan) {
                $names = $respons
                    ->where('pertanyaan', $key)
                    ->sortBy('urutan')
                    ->map(fn ($r) => $r->siswaDipilih?->nama ?? '')
                    ->filter()
                    ->values()
                    ->implode(', ');

                $row[$key.'. '.$pertanyaan] = $names;
            }

            return $row;
        })->toArray();
    }

    public function getExportCount(array $filters = []): int
    {
        return $this->getFiltered($filters)->count();
    }

    public function getTemplateHeaders(): array
    {
        $headers = ['Timestamp', 'Nama Lengkap', 'Kelas', 'Tahun Pelajaran'];

        foreach (Sosiometri::PERTANYAAN as $key => $pertanyaan) {
            $headers[] = $key.'. '.$pertanyaan;
        }

        return $headers;
    }

    public function getTemplateSampleRows(): array
    {
        $row = [
            'Timestamp' => '01/08/2026 23:44:58',
            'Nama Lengkap' => 'Test',
            'Kelas' => 'XI RPL 1',
            'Tahun Pelajaran' => '2026',
        ];

        foreach (Sosiometri::PERTANYAAN as $key => $pertanyaan) {
            $row[$key.'. '.$pertanyaan] = '';
        }

        $row['Q1. '.Sosiometri::PERTANYAAN['Q1']] = 'Ahmad, Budi';

        return [$row];
    }

    private function validateAndImport(array $rows): array
    {
        $imported = 0;
        $errors = [];

        $namaKey = $this->firstKey($rows[0] ?? [], ['nama_lengkap', 'nama_siswa', 'nama']);
        $kelasKey = $this->firstKey($rows[0] ?? [], ['kelas']);
        $timestampKey = $this->firstKey($rows[0] ?? [], ['timestamp']);
        $judul = 'Asesmen Sosiometri';

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2;

            $nama = trim((string) ($row[$namaKey] ?? ''));
            $kelas = trim((string) ($row[$kelasKey] ?? ''));
            $tanggal = AsesmenImportHelper::parseTimestamp($row[$timestampKey] ?? null);

            if ($nama === '' || $kelas === '') {
                $errors[] = "Baris {$lineNumber}: kolom Nama dan Kelas wajib diisi.";
                continue;
            }

            $siswa = AsesmenImportHelper::resolveSiswa($nama, $kelas);

            if (! $siswa) {
                $errors[] = "Baris {$lineNumber}: siswa \"{$nama}\" tidak bisa diproses.";
                continue;
            }

            DB::transaction(function () use ($siswa, $row, $judul, $lineNumber, &$errors) {
                $sosiometri = Sosiometri::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'judul' => $judul],
                    [
                        'instruksi' => 'Pilih teman sekelas Anda.',
                        'jumlah_pilihan' => 3,
                    ]
                );

                $sosiometri->respons()->delete();

                foreach (Sosiometri::PERTANYAAN as $key => $pertanyaan) {
                    $cell = $this->questionCellValue($row, $key);

                    $names = array_values(array_filter(array_map('trim', preg_split('/[,;]/', (string) $cell, -1, PREG_SPLIT_NO_EMPTY))));

                    foreach ($names as $urutan => $name) {
                        if ($urutan >= 3) {
                            break;
                        }

                        $dipilih = DataSiswa::query()
                            ->whereHas('user', fn ($q) => $q->whereRaw('LOWER(nama) = ?', [mb_strtolower($name)]))
                            ->first();

                        if (! $dipilih) {
                            $errors[] = "Baris {$lineNumber}: teman \"{$name}\" (".$key.") tidak ditemukan.";
                            continue;
                        }

                        $sosiometri->respons()->create([
                            'siswa_dipilih_id' => $dipilih->id,
                            'siswa_pemilih_id' => $siswa->id,
                            'urutan' => $urutan + 1,
                            'alasan' => '',
                            'pertanyaan' => $key,
                        ]);
                    }
                }
            });

            $imported++;
        }

        return compact('imported', 'errors');
    }

    private function questionCellValue(array $row, string $key): mixed
    {
        foreach ($row as $col => $value) {
            if (str_starts_with(strtoupper(trim((string) $col)), $key.'.')) {
                return $value;
            }
        }

        return $row[strtolower($key)] ?? '';
    }

    private function firstKey(array $row, array $candidates): ?string
    {
        foreach ($candidates as $c) {
            if (array_key_exists($c, $row)) {
                return $c;
            }
        }

        return null;
    }
}
