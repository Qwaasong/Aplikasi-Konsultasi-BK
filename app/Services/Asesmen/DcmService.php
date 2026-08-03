<?php

namespace App\Services\Asesmen;

use App\Models\Dcm;
use App\Repositories\Contracts\Asesmen\DcmRepositoryInterface;
use App\Services\ImportExportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DcmService
{
    public function __construct(
        protected DcmRepositoryInterface $repo,
        protected ImportExportService $importExportService
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?Dcm
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Dcm
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): Dcm
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
        return Dcm::with('siswa')
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
                    ->orWhere('kesimpulan', 'like', "%{$keyword}%");
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
     * Header mengikuti spreadsheet Google Forms klien DCM:
     *   Timestamp | NAMA | KELAS | TAHUN PELAJARAN | A. Masalah Kesehatan | ... | L. ...
     */
    public function exportRows(array $filters = []): array
    {
        return $this->getFiltered($filters)->map(function (Dcm $d) {
            $row = [
                'Timestamp' => $d->tanggal?->format('Y-m-d') ?? '',
                'NAMA' => $d->siswa?->nama ?? '',
                'KELAS' => $d->siswa?->kelas_label ?? '',
                'TAHUN PELAJARAN' => $d->tanggal?->format('Y') ?? '',
            ];

            foreach (Dcm::SECTIONS as $letter => $title) {
                $codes = collect($d->jawaban[$letter] ?? []);

                $cell = collect(Dcm::QUESTION_GROUPS[$letter] ?? [])
                    ->filter(fn ($pertanyaan, $kode) => $codes->contains($kode))
                    ->map(fn ($pertanyaan, $kode) => $kode.' '.$pertanyaan)
                    ->values()
                    ->implode(', ');

                $row[$letter.'. '.$title] = $cell;
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
        $headers = ['Timestamp', 'NAMA', 'KELAS', 'TAHUN PELAJARAN'];

        foreach (Dcm::SECTIONS as $letter => $title) {
            $headers[] = $letter.'. '.$title;
        }

        return $headers;
    }

    public function getTemplateSampleRows(): array
    {
        $row = [
            'Timestamp' => '30/07/2026 10:27:39',
            'NAMA' => 'Test',
            'KELAS' => 'X RPL 1',
            'TAHUN PELAJARAN' => '2026',
        ];

        foreach (Dcm::SECTIONS as $letter => $title) {
            $first = array_key_first(Dcm::QUESTION_GROUPS[$letter] ?? []);
            $row[$letter.'. '.$title] = $first ? $first.' '.Dcm::QUESTION_GROUPS[$letter][$first] : '';
        }

        return [$row];
    }

    private function validateAndImport(array $rows): array
    {
        $imported = 0;
        $errors = [];

        // Set semua kode soal valid untuk penyaringan (284 kode).
        $validCodes = collect(Dcm::QUESTION_GROUPS)->flatMap(fn ($codes) => array_keys($codes))->flip();

        $namaKey = $this->firstKey($rows[0] ?? [], ['nama', 'nama_siswa', 'nama_lengkap']);
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

            $jawaban = $this->parseJawaban($row, $validCodes);

            $dcm = new Dcm();
            $dcm->jawaban = $jawaban;

            Dcm::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tanggal' => Carbon::parse($tanggal)],
                [
                    'jawaban' => $jawaban,
                    'masalah_teridentifikasi' => $dcm->masalahSummary(),
                    'kesimpulan' => trim((string) ($row['kesimpulan'] ?? '')) ?: null,
                    'catatan' => trim((string) ($row['catatan'] ?? '')) ?: null,
                ]
            );

            $imported++;
        }

        return compact('imported', 'errors');
    }

    /**
     * Header gform = 12 kolom section ("A. Masalah Kesehatan", dst). Isi sel =
     * "A01 Sering sakit ketika SD, A02 ..." → parse kode [A-Z]\d{2} dari sel.
     * Juga tetap menerima kolom `jawaban` berisi kode dipisah koma.
     */
    private function parseJawaban(array $row, $validCodes): array
    {
        $jawaban = [];

        // Kolom `jawaban` (format lama) berisi kode dipisah koma/titik-koma.
        foreach (preg_split('/[,;]/', (string) ($row['jawaban'] ?? ''), -1, PREG_SPLIT_NO_EMPTY) as $rawCode) {
            $code = strtoupper(trim($rawCode));

            if ($validCodes->has($code)) {
                $jawaban[$code[0]][] = $code;
            }
        }

        // 12 kolom section gform.
        foreach (Dcm::SECTIONS as $letter => $title) {
            $cell = $this->sectionCellValue($row, $letter);

            if (preg_match_all('/\b([A-Z]\d{2})\b/i', (string) $cell, $m)) {
                foreach ($m[1] as $code) {
                    $code = strtoupper($code);

                    if ($validCodes->has($code)) {
                        $jawaban[$code[0]][] = $code;
                    }
                }
            }
        }

        return $jawaban;
    }

    private function sectionCellValue(array $row, string $letter): mixed
    {
        foreach ($row as $key => $value) {
            $norm = strtoupper(trim((string) $key));

            // "A. Masalah Kesehatan" → "a__masalah_kesehatan"; cocokkan huruf + pemisah.
            if (preg_match('/^'.preg_quote($letter, '/').'[_\s\.]/', $norm) === 1) {
                return $value;
            }
        }

        return $row[strtolower($letter)] ?? '';
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
