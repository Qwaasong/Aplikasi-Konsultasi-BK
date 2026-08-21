<?php

namespace App\Services\Asesmen;

use App\Models\Peminatan;
use App\Repositories\Contracts\Asesmen\PeminatanRepositoryInterface;
use App\Services\ImportExportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PeminatanService
{
    private const RESULT_HEADER = 'Berdasarkan jawaban diatas maka saya lebih cenderung pada kecerdasan apasaja? sebutkan dan jelaskan!';

    private const SECTION_HEADERS = [
        'Linguistik' => 'KECERDASAN LINGUISTIK ( mengacu pada kemampuan penggunaan kata-kata dengan baik)',
        'Logis-Matematik' => 'KECERDASAN LOGIS MATEMATIK ( cenderung mengaitkan suatu hal dalam logika atau senang mengalisis sebuah masalah)',
        'Visual-Spasial' => 'KECERDASAN VISUAL SPASIAL (  kemampuan untuk berpikir secara abstrak dan sangat lihai dalam memvisualisasikan sesuatu)',
        'Musikal' => 'KECERDASAN MUSIKAL ( cenderung mengekspresikan dirinya lewat musik, entah itu bernyanyi atau bermain alat musik.)',
        'Interpersonal' => 'KECERDASAN INTERPERSONAL ( pandai dalam memahami dan berinteraksi dengan orang lain)',
        'Intrapersonal' => 'KECERDASAN INTRAPERSONAL (  lebih memahami karakter dan perasaan diri sendiri.)',
        'Kinestetik' => 'KECERDASAN KINESTETIK ( memiliki keterampilan motorik yang baik dan kemampuan fisik yang mumpuni)',
        'Naturalis' => 'KECERDASAN NATURALIS (  senang berbaur dengan alam, baik itu dengan hewan, tanaman, atau lingkungan)',
    ];

    public function __construct(
        protected PeminatanRepositoryInterface $repo,
        protected ImportExportService $importExportService
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?Peminatan
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Peminatan
    {
        $data['pilihan1'] = $data['pilihan1'] ?? '';
        $data['pilihan2'] = $data['pilihan2'] ?? '';
        $data['pilihan3'] = $data['pilihan3'] ?? '';

        return $this->repo->create($data);
    }

    public function update(int $id, array $data): Peminatan
    {
        $data['pilihan1'] = $data['pilihan1'] ?? '';
        $data['pilihan2'] = $data['pilihan2'] ?? '';
        $data['pilihan3'] = $data['pilihan3'] ?? '';

        $this->repo->update($id, $data);

        return $this->repo->findById($id);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }

    public function search(string $keyword)
    {
        return Peminatan::with('siswa')
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
        return $this->getFiltered($filters)->map(function (Peminatan $p) {
            $row = [
                'Timestamp' => $p->tanggal?->format('Y-m-d') ?? '',
                'Nama Lengkap' => $p->siswa?->nama ?? '',
                'Tahun Pelajaran' => $p->tanggal?->format('Y') ?? '',
                'Kelas' => $p->siswa?->kelas_label ?? '',
            ];

            foreach (self::SECTION_HEADERS as $section => $header) {
                $checked = collect($p->jawaban[$section] ?? []);
                $row[$header] = collect(Peminatan::QUESTION_GROUPS[$section] ?? [])
                    ->filter(fn ($pertanyaan, $kode) => $checked->contains($kode))
                    ->values()
                    ->implode(', ');
            }

            $row[self::RESULT_HEADER] = (string) $p->hasil;

            return $row;
        })->toArray();
    }

    public function getExportCount(array $filters = []): int
    {
        return $this->getFiltered($filters)->count();
    }

    public function getTemplateHeaders(): array
    {
        return ['Timestamp', 'Nama Lengkap', 'Tahun Pelajaran', 'Kelas', ...array_values(self::SECTION_HEADERS), self::RESULT_HEADER];
    }

    public function getTemplateSampleRows(): array
    {
        return [[
            'Timestamp' => '01/08/2026 23:44:58',
            'Nama Lengkap' => 'Test',
            'Tahun Pelajaran' => '2026',
            'Kelas' => 'XI RPL 1',
            self::SECTION_HEADERS['Naturalis'] => Peminatan::QUESTION_GROUPS['Naturalis']['NA02'],
            self::RESULT_HEADER => 'Naturalis',
        ]];
    }

    protected function mapSectionToCodes(string $section, mixed $answers): array
    {
        $answers = array_map(
            [$this, 'normalize'],
            is_array($answers) ? $answers : preg_split('/[,;]/', (string) $answers, -1, PREG_SPLIT_NO_EMPTY)
        );

        $codes = [];

        foreach (Peminatan::QUESTION_GROUPS[$section] ?? [] as $code => $question) {
            $needleTokens = preg_split('/\s+/', $this->normalize($question), -1, PREG_SPLIT_NO_EMPTY) ?: [];

            foreach ($answers as $answer) {
                $answerTokens = preg_split('/\s+/', $answer, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $shorter = min(count($answerTokens), count($needleTokens));

                if ($shorter > 0 && count(array_intersect($answerTokens, $needleTokens)) / $shorter >= 0.7) {
                    $codes[] = $code;
                    break;
                }
            }
        }

        return $codes;
    }

    protected function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = str_replace(['anda', 'saya'], ' ', $text);
        $text = preg_replace('/[^a-z0-9\s]/u', ' ', $text) ?? '';
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }

    private function validateAndImport(array $rows): array
    {
        $imported = 0;
        $errors = [];
        $namaKey = $this->firstKey($rows[0] ?? [], ['nama_lengkap', 'nama_siswa', 'nama']);
        $kelasKey = $this->firstKey($rows[0] ?? [], ['kelas']);
        $timestampKey = $this->firstKey($rows[0] ?? [], ['timestamp']);

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2;
            $nama = trim((string) ($row[$namaKey] ?? ''));
            $kelas = trim((string) ($row[$kelasKey] ?? ''));

            if ($nama === '' || $kelas === '') {
                $errors[] = "Baris {$lineNumber}: kolom Nama dan Kelas wajib diisi.";
                continue;
            }

            $siswa = AsesmenImportHelper::resolveSiswa($nama, $kelas);

            if (! $siswa) {
                $errors[] = "Baris {$lineNumber}: siswa \"{$nama}\" tidak bisa diproses.";
                continue;
            }

            $jawaban = [];

            foreach (Peminatan::SECTIONS as $section) {
                $jawaban[$section] = $this->mapSectionToCodes($section, $this->sectionColumnValue($row, $section));
            }

            Peminatan::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tanggal' => Carbon::parse(AsesmenImportHelper::parseTimestamp($row[$timestampKey] ?? null))],
                [
                    'pilihan1' => '',
                    'pilihan2' => '',
                    'pilihan3' => '',
                    'hasil' => $this->resultValue($row),
                    'jawaban' => $jawaban,
                    'catatan' => trim((string) ($row['catatan'] ?? '')) ?: null,
                ]
            );

            $imported++;
        }

        return compact('imported', 'errors');
    }

    private function sectionColumnValue(array $row, string $section): mixed
    {
        $needles = [AsesmenImportHelper::normalizeText($section), AsesmenImportHelper::normalizeText((string) (self::SECTION_HEADERS[$section] ?? ''))];

        foreach ($row as $key => $value) {
            $key = AsesmenImportHelper::normalizeText((string) $key);

            foreach ($needles as $needle) {
                if ($needle !== '' && str_contains($key, $needle)) {
                    return $value;
                }
            }
        }

        return '';
    }

    private function resultValue(array $row): string
    {
        foreach ($row as $key => $value) {
            if ((string) $key === 'hasil' || str_contains(AsesmenImportHelper::normalizeText((string) $key), 'berdasarkan jawaban diatas')) {
                return trim((string) $value);
            }
        }

        return '';
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
}
