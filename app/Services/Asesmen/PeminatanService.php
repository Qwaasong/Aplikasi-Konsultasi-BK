<?php

namespace App\Services\Asesmen;

use App\Repositories\Contracts\Asesmen\PeminatanRepositoryInterface;
use App\Services\ImportExportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\Peminatan;

class PeminatanService
{
    public function __construct(
        protected PeminatanRepositoryInterface $repo,
        protected ImportExportService $importExportService
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?\App\Models\Peminatan
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): \App\Models\Peminatan
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): \App\Models\Peminatan
    {
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

    // ===========================
    // IMPORT / EXPORT
    // ===========================

    public function importFromFile(UploadedFile $file): array
    {
        $rows = $this->importExportService->parseUploadedFile($file);

        return $this->validateAndImport($rows);
    }

    /**
     * Header mengikuti spreadsheet Google Forms klien:
     *   Timestamp | Nama Lengkap | Tahun Pelajaran | Kelas | <8 bagian>
     */
    public function exportRows(array $filters = []): array
    {
        return $this->getFiltered($filters)->map(function (Peminatan $p) {
            $row = [
                'Timestamp' => $p->tanggal?->format('Y-m-d') ?? '',
                'Nama Lengkap' => $p->siswa?->nama ?? '',
                'Tahun Pelajaran' => $p->tanggal?->format('Y') ?? '',
                'Kelas' => $p->siswa?->kelas_label ?? '',
                'hasil' => (string) $p->hasil,
            ];

            foreach (Peminatan::SECTIONS as $section) {
                $checked = collect($p->jawaban[$section] ?? []);
                $statements = collect(Peminatan::QUESTION_GROUPS[$section] ?? [])
                    ->filter(fn ($pertanyaan, $kode) => $checked->contains($kode))
                    ->values()
                    ->implode(', ');

                $row[$this->sectionKey($section)] = $statements;
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
        return ['Timestamp', 'Nama Lengkap', 'Tahun Pelajaran', 'Kelas', 'hasil', ...$this->sectionHeaders()];
    }

    public function getTemplateSampleRows(): array
    {
        $section = 'Linguistik';
        $first = Peminatan::QUESTION_GROUPS[$section][array_key_first(Peminatan::QUESTION_GROUPS[$section])];

        $row = [
            'Timestamp' => '01/08/2026 23:44:58',
            'Nama Lengkap' => 'Test',
            'Tahun Pelajaran' => '2026',
            'Kelas' => 'XI RPL 1',
            'hasil' => $section,
            $this->sectionKey($section) => $first,
        ];

        return [$row];
    }

    private function sectionHeaders(): array
    {
        return array_map(fn ($s) => $this->sectionKey($s), Peminatan::SECTIONS);
    }

    /**
     * Ubah jawaban satu seksi menjadi daftar kode soal yang cocok.
     * Jawaban bisa string ("LG01, LG03") atau array of string.
     *
     * Pakai ambang tumpang-tindih kata (overlap >= 70% dari kalimat lebih
     * pendek) karena teks form bisa beda ejaan dari model ("serta" hilang,
     * titik vs koma), sehingga substring penuh gagal.
     */
    protected function mapSectionToCodes(string $section, mixed $answers): array
    {
        $normalizedAnswers = array_map(
            [$this, 'normalize'],
            is_array($answers) ? $answers : preg_split('/[,;]/', (string) $answers, -1, PREG_SPLIT_NO_EMPTY)
        );

        $codes = [];

        foreach (Peminatan::QUESTION_GROUPS[$section] ?? [] as $code => $pertanyaan) {
            $needleTokens = preg_split('/\s+/', $this->normalize($pertanyaan), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $needleCount = count($needleTokens);

            if ($needleCount === 0) {
                continue;
            }

            foreach ($normalizedAnswers as $answer) {
                $answerTokens = preg_split('/\s+/', $answer, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $overlap = count(array_intersect($answerTokens, $needleTokens));
                $shorter = min(count($answerTokens), $needleCount);

                // Cocok jika >= 70% kata dari kalimat yang lebih pendek ikut tertutup.
                if ($shorter > 0 && $overlap / $shorter >= 0.7) {
                    $codes[] = $code;

                    break;
                }
            }
        }

        return $codes;
    }

    protected function sectionKey(string $section): string
    {
        return strtolower(str_replace('-', '_', str_replace(' ', '_', $section)));
    }

    /**
     * Normalisasi teks: lowercase, buang kata "anda"/"saya", buang non-alnum,
     * lalu rapikan spasi. Dipakai untuk mencocokkan pertanyaan model vs form
     * yang beda kata ganti ("Saya" vs "Anda").
     */
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

        // Key header ternormalisasi untuk kolom identitas gform.
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

            $jawaban = [];

            foreach (Peminatan::SECTIONS as $section) {
                $jawaban[$section] = $this->mapSectionToCodes($section, $this->sectionColumnValue($row, $section));
            }

            Peminatan::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tanggal' => Carbon::parse($tanggal)],
                [
                    'pilihan1' => '',
                    'pilihan2' => '',
                    'pilihan3' => '',
                    'hasil' => trim((string) ($row['hasil'] ?? '')),
                    'jawaban' => $jawaban,
                    'catatan' => trim((string) ($row['catatan'] ?? '')) ?: null,
                ]
            );

            $imported++;
        }

        return compact('imported', 'errors');
    }

    /**
     * Cari nilai kolom bagian gform. Header gform memakai nama bagian panjang
     * (mis. "KECERDASAN LINGUISTIK ( mengacu pada kemampuan...)"), jadi cocokkan
     * dengan key ternormalisasi yang mengandung kata kunci bagian.
     */
    private function sectionColumnValue(array $row, string $section): mixed
    {
        $needle = AsesmenImportHelper::normalizeText($this->sectionKey($section));
        $needle = str_replace('_', ' ', $needle);

        foreach ($row as $key => $value) {
            if (str_contains(AsesmenImportHelper::normalizeText($key), $needle)) {
                return $value;
            }
        }

        return $row[$this->sectionKey($section)] ?? '';
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
