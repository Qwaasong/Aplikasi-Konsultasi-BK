<?php

namespace App\Services\Asesmen;

use App\Models\DataSiswa;
use App\Models\GayaBelajar;
use App\Repositories\Contracts\Asesmen\GayaBelajarRepositoryInterface;
use App\Services\ImportExportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GayaBelajarService
{
    public function __construct(
        protected GayaBelajarRepositoryInterface $repo,
        protected ImportExportService $importExportService
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?GayaBelajar
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): GayaBelajar
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): GayaBelajar
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
        return GayaBelajar::with('siswa')
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
                    ->orWhere('hasil', 'like', "%{$keyword}%");
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
     * Header mengikuti spreadsheet Google Forms klien Gaya Belajar:
     *   Timestamp | Nama Lengkap | Tahun Pelajaran | Kelas | <39 pernyataan> |
     *   Gaya belajar yang sesuai | Faktor yang menghambat | Faktor yang mendukung
     */
    public function exportRows(array $filters = []): array
    {
        return $this->getFiltered($filters)->map(function (GayaBelajar $g) {
            $row = [
                'Timestamp' => $g->tanggal?->format('Y-m-d') ?? '',
                'Nama Lengkap' => $g->siswa?->nama ?? '',
                'Tahun Pelajaran' => $g->tanggal?->format('Y') ?? '',
                'Kelas' => $g->siswa?->kelas_label ?? '',
            ];

            foreach ($this->statementHeaders() as $header) {
                $row[$header] = '';
            }

            $row['Gaya belajar yang sesuai'] = (string) $g->hasil;
            $row['Faktor apa sajakah yang menghambat belajar saya?'] = (string) ($g->faktor_penghambat ?? '');
            $row['Faktor apa sajakah yang mendukung belajar saya?'] = (string) ($g->faktor_pendukung ?? '');

            return $row;
        })->toArray();
    }

    public function getExportCount(array $filters = []): int
    {
        return $this->getFiltered($filters)->count();
    }

    public function getTemplateHeaders(): array
    {
        return [
            'Timestamp',
            'Nama Lengkap',
            'Tahun Pelajaran',
            'Kelas',
            ...$this->statementHeaders(),
            'Gaya belajar yang sesuai',
            'Faktor apa sajakah yang menghambat belajar saya?',
            'Faktor apa sajakah yang mendukung belajar saya?',
        ];
    }

    public function getTemplateSampleRows(): array
    {
        $row = [
            'Timestamp' => '01/08/2026 23:44:58',
            'Nama Lengkap' => 'Test',
            'Tahun Pelajaran' => '2026',
            'Kelas' => 'XI RPL 1',
        ];

        foreach ($this->statementHeaders() as $header) {
            $row[$header] = '';
        }

        $row['Gaya belajar yang sesuai'] = 'VISUAL';

        return [$row];
    }

    /**
     * Header 39 pernyataan gform — urutan persis seperti GayaBelajar::QUESTION_GROUPS.
     */
    private function statementHeaders(): array
    {
        $headers = [];

        foreach (GayaBelajar::QUESTION_GROUPS as $group => $statements) {
            foreach ($statements as $statement) {
                $headers[] = $statement;
            }
        }

        return $headers;
    }

    private function validateAndImport(array $rows): array
    {
        $imported = 0;
        $errors = [];

        // Map kolom header ternormalisasi → (kelompok, urutan) untuk bentuk 39 kolom pernyataan.
        $statementColumns = $this->buildStatementColumns();

        $namaKey = $this->firstKey($rows[0] ?? [], ['nama_lengkap', 'nama_siswa', 'nama']);
        $kelasKey = $this->firstKey($rows[0] ?? [], ['kelas']);
        $timestampKey = $this->firstKey($rows[0] ?? [], ['timestamp']);

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

            // Skor dihitung dari 39 kolom pernyataan gform (Ya/Tidak).
            $scores = $this->scoresFromStatementColumns($row, $statementColumns);

            $hasil = $this->firstContains($row, ['gaya_belajar_yang_sesuai']);

            if ($hasil === '') {
                $hasil = max($scores) > 0 ? array_search(max($scores), $scores, true) : '';
            }

            GayaBelajar::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tanggal' => Carbon::parse($tanggal)],
                [
                    'visual' => $scores['visual'],
                    'auditori' => $scores['auditori'],
                    'kinestetik' => $scores['kinestetik'],
                    'hasil' => $hasil,
                    'catatan' => null,
                    'faktor_penghambat' => $this->firstContains($row, ['menghambat']) ?: null,
                    'faktor_pendukung' => $this->firstContains($row, ['mendukung']) ?: null,
                ]
            );

            $imported++;
        }

        return compact('imported', 'errors');
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

    private function firstContains(array $row, array $needles): string
    {
        foreach ($row as $key => $value) {
            $norm = strtolower(str_replace('_', ' ', trim((string) $key)));

            foreach ($needles as $needle) {
                if (str_contains($norm, $needle)) {
                    return trim((string) $value);
                }
            }
        }

        return '';
    }

    /**
     * Petakan header kolom (ternormalisasi) ke [kelompok => [statementIndex => kolomHeader]].
     * Hanya kolom yang cocok dengan teks pernyataan model yang dikembalikan.
     */
    private function buildStatementColumns(): array
    {
        $map = [];

        foreach (GayaBelajar::QUESTION_GROUPS as $group => $statements) {
            foreach ($statements as $i => $statement) {
                $key = $this->normalizeStatementKey($statement);
                $map[$key] = [$group, $i];
            }
        }

        return $map;
    }

    private function normalizeStatementKey(string $text): string
    {
        // Gunakan normalisasi header yang sama dengan ImportExportService
        // (lowercase + spasi/dash/titik → underscore) agar cocok dengan kolom CSV.
        return $this->importExportService->normalizeHeaders([$text])[0];
    }

    private function scoresFromStatementColumns(array $row, array $map): array
    {
        $scores = ['visual' => 0, 'auditori' => 0, 'kinestetik' => 0];
        $groupToScore = ['Visual' => 'visual', 'Auditorial' => 'auditori', 'Kinestetik' => 'kinestetik'];
        $affirmative = ['ya', 'y', '1', 'x', 'true', 'benar', 'setuju'];

        foreach ($map as $key => [$group, $index]) {
            if (! array_key_exists($key, $row)) {
                continue;
            }

            $value = strtolower(trim((string) ($row[$key] ?? '')));

            if ($value !== '' && in_array($value, $affirmative, true)) {
                $scores[$groupToScore[$group]]++;
            }
        }

        return $scores;
    }
}
