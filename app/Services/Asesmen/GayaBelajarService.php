<?php

namespace App\Services\Asesmen;

use App\Models\GayaBelajar;
use App\Repositories\Contracts\Asesmen\GayaBelajarRepositoryInterface;
use App\Services\ImportExportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GayaBelajarService
{
    private const RESULT_HEADER = 'Gaya belajar yang sesuai dengan saya adalah';

    private const FACTOR_BLOCKER = 'Faktor apa sajakah yang menghambat belajar saya?';

    private const FACTOR_SUPPORT = 'Faktor apa sajakah yang mendukung belajar saya?';

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

    public function importFromFile(UploadedFile $file): array
    {
        return $this->validateAndImport($this->importExportService->parseUploadedFile($file));
    }

    public function exportRows(array $filters = []): array
    {
        return $this->getFiltered($filters)->map(fn (GayaBelajar $g) => [
            'Timestamp' => $g->tanggal?->format('Y-m-d') ?? '',
            'NAMA LENGKAP' => $g->siswa?->nama ?? '',
            'TAHUN PELAJARAN' => $g->tanggal?->format('Y') ?? '',
            'KELAS' => $g->siswa?->kelas_label ?? '',
            'VISUAL' => '',
            'AUDITORIAL' => '',
            'KINESTETIK' => '',
            self::RESULT_HEADER => (string) $g->hasil,
            self::FACTOR_BLOCKER => (string) ($g->faktor_penghambat ?? ''),
            self::FACTOR_SUPPORT => (string) ($g->faktor_pendukung ?? ''),
        ])->toArray();
    }

    public function getExportCount(array $filters = []): int
    {
        return $this->getFiltered($filters)->count();
    }

    public function getTemplateHeaders(): array
    {
        return ['Timestamp', 'NAMA LENGKAP', 'TAHUN PELAJARAN', 'KELAS', 'VISUAL', 'AUDITORIAL', 'KINESTETIK', self::RESULT_HEADER, self::FACTOR_BLOCKER, self::FACTOR_SUPPORT];
    }

    public function getTemplateSampleRows(): array
    {
        return [[
            'Timestamp' => '01/08/2026 23:44:58',
            'NAMA LENGKAP' => 'Test',
            'TAHUN PELAJARAN' => '2026',
            'KELAS' => 'XI RPL 1',
            'VISUAL' => GayaBelajar::QUESTION_GROUPS['Visual'][0],
            'AUDITORIAL' => GayaBelajar::QUESTION_GROUPS['Auditorial'][0],
            'KINESTETIK' => GayaBelajar::QUESTION_GROUPS['Kinestetik'][0],
            self::RESULT_HEADER => 'VISUAL',
            self::FACTOR_BLOCKER => 'Malas',
            self::FACTOR_SUPPORT => 'Musik',
        ]];
    }

    private function validateAndImport(array $rows): array
    {
        $imported = 0;
        $errors = [];
        $statementColumns = $this->buildStatementColumns();
        $namaKey = $this->firstKey($rows[0] ?? [], ['nama_lengkap', 'nama_siswa', 'nama']);
        $kelasKey = $this->firstKey($rows[0] ?? [], ['kelas']);
        $emailKey = $this->firstKey($rows[0] ?? [], ['alamat_email', 'email_address', 'email']);
        $timestampKey = $this->firstKey($rows[0] ?? [], ['timestamp']);

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2;
            $nama = trim((string) ($row[$namaKey] ?? ''));
            $kelas = trim((string) ($row[$kelasKey] ?? ''));
            $email = trim((string) ($row[$emailKey] ?? ''));

            $siswa = null;

            if ($nama !== '' && $kelas !== '') {
                $siswa = AsesmenImportHelper::resolveSiswa($nama, $kelas);
            }

            if (! $siswa && $email !== '') {
                $siswa = DataSiswa::whereHas('user', fn ($q) => $q->where('email', $email))->first();

                if (! $siswa) {
                    $nis = explode('@', $email)[0];
                    if ($nis !== '') {
                        $siswa = DataSiswa::where('nis', $nis)->first();
                    }
                }
            }

            if (! $siswa) {
                $errors[] = "Baris {$lineNumber}: siswa tidak dapat diidentifikasi. Pastikan ada kolom Nama+Kelas atau Email yang valid.";
                continue;
            }

            $scores = $this->scoresFromSectionCells($row);

            if (array_sum($scores) === 0) {
                $scores = $this->scoresFromStatementColumns($row, $statementColumns);
            }

            $hasil = $this->firstContains($row, ['gaya belajar yang sesuai']) ?: (max($scores) > 0 ? array_search(max($scores), $scores, true) : '');

            GayaBelajar::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tanggal' => Carbon::parse(AsesmenImportHelper::parseTimestamp($row[$timestampKey] ?? null))],
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

    private function scoresFromSectionCells(array $row): array
    {
        return [
            'visual' => $this->scoreSection($row, 'visual', 'Visual'),
            'auditori' => $this->scoreSection($row, 'auditorial', 'Auditorial'),
            'kinestetik' => $this->scoreSection($row, 'kinestetik', 'Kinestetik'),
        ];
    }

    private function scoreSection(array $row, string $key, string $group): int
    {
        $answers = array_map(
            fn ($value) => AsesmenImportHelper::normalizeText((string) $value),
            preg_split('/[,;]/', (string) ($row[$key] ?? ''), -1, PREG_SPLIT_NO_EMPTY) ?: []
        );
        $score = 0;

        foreach (GayaBelajar::QUESTION_GROUPS[$group] as $statement) {
            $needle = AsesmenImportHelper::normalizeText($statement);

            foreach ($answers as $answer) {
                if ($answer !== '' && ($answer === $needle || str_contains($answer, $needle) || str_contains($needle, $answer))) {
                    $score++;
                    break;
                }
            }
        }

        return $score;
    }

    private function buildStatementColumns(): array
    {
        $map = [];

        foreach (GayaBelajar::QUESTION_GROUPS as $group => $statements) {
            foreach ($statements as $statement) {
                $map[$this->importExportService->normalizeHeaders([$statement])[0]] = $group;
            }
        }

        return $map;
    }

    private function scoresFromStatementColumns(array $row, array $map): array
    {
        $scores = ['visual' => 0, 'auditori' => 0, 'kinestetik' => 0];
        $groupToScore = ['Visual' => 'visual', 'Auditorial' => 'auditori', 'Kinestetik' => 'kinestetik'];
        $affirmative = ['ya', 'y', '1', 'x', 'true', 'benar', 'setuju'];

        foreach ($map as $key => $group) {
            $value = strtolower(trim((string) ($row[$key] ?? '')));

            if ($value !== '' && in_array($value, $affirmative, true)) {
                $scores[$groupToScore[$group]]++;
            }
        }

        return $scores;
    }

    private function firstKey(array $row, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (array_key_exists($candidate, $row)) {
                return $candidate;
            }
        }

        return null;
    }

    private function firstContains(array $row, array $needles): string
    {
        foreach ($row as $key => $value) {
            $key = strtolower(str_replace('_', ' ', trim((string) $key)));

            foreach ($needles as $needle) {
                if (str_contains($key, $needle)) {
                    return trim((string) $value);
                }
            }
        }

        return '';
    }
}
