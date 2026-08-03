<?php

namespace App\Services\Asesmen;

use App\Models\Akpd;
use App\Repositories\Contracts\Asesmen\AkpdRepositoryInterface;
use App\Services\ImportExportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Services\Asesmen\AsesmenImportHelper;

class AkpdService
{
    public function __construct(
        protected AkpdRepositoryInterface $repo,
        protected ImportExportService $importExportService
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?Akpd
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Akpd
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): Akpd
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
        return Akpd::with('siswa')
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
                    ->orWhere('tahun_pelajaran', 'like', "%{$keyword}%");
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
     * Header mengikuti spreadsheet Google Forms klien AKPD:
     *   Timestamp | Nama Siswa | Tahun Pelajaran | Kelas | 1. <soal> .. 50. <soal>
     */
    public function exportRows(array $filters = []): array
    {
        return $this->getFiltered($filters)->map(function (Akpd $a) {
            $row = [
                'Timestamp' => $a->tanggal?->format('Y-m-d') ?? '',
                'Nama Siswa' => $a->siswa?->nama ?? '',
                'Tahun Pelajaran' => (string) ($a->tahun_pelajaran ?? ''),
                'Kelas' => $a->siswa?->kelas_label ?? '',
            ];

            foreach (range(1, 50) as $no) {
                $key = 'q'.str_pad((string) $no, 2, '0', STR_PAD_LEFT);
                $row[$no.'. '.Akpd::QUESTIONS[$no]] = (string) ($a->{$key} ?? '');
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
        $headers = ['Timestamp', 'Nama Siswa', 'Tahun Pelajaran', 'Kelas'];

        foreach (range(1, 50) as $no) {
            $headers[] = $no.'. '.Akpd::QUESTIONS[$no];
        }

        return $headers;
    }

    public function getTemplateSampleRows(): array
    {
        $row = [
            'Timestamp' => '01/08/2026 23:44:58',
            'Nama Siswa' => 'Test',
            'Tahun Pelajaran' => '2026',
            'Kelas' => 'XI RPL 1',
        ];

        foreach (range(1, 50) as $no) {
            $row[$no.'. '.Akpd::QUESTIONS[$no]] = 'Ya';
        }

        return [$row];
    }

    private function validateAndImport(array $rows): array
    {
        $imported = 0;
        $errors = [];

        // Key header ternormalisasi untuk kolom identitas gform.
        $namaKey = $this->firstKey($rows[0] ?? [], ['nama_siswa', 'nama_lengkap', 'nama']);
        $kelasKey = $this->firstKey($rows[0] ?? [], ['kelas']);
        $timestampKey = $this->firstKey($rows[0] ?? [], ['timestamp']);
        $tahunKey = $this->firstKey($rows[0] ?? [], ['tahun_pelajaran']);

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
                $kelasId = AsesmenImportHelper::resolveKelasId($kelas);
                if ($kelasId === null) {
                    $errors[] = "Baris {$lineNumber}: kelas \"{$kelas}\" tidak ditemukan di database.";
                } else {
                    $errors[] = "Baris {$lineNumber}: siswa \"{$nama}\" tidak bisa diproses.";
                }
                continue;
            }

            $data = [
                'tahun_pelajaran' => AsesmenImportHelper::resolveTahunPelajaran($row[$tahunKey] ?? null),
            ];

            foreach (range(1, 50) as $no) {
                $key = 'q'.str_pad((string) $no, 2, '0', STR_PAD_LEFT);
                $value = strtolower(trim((string) ($this->questionColumnValue($row, $no) ?? '')));

                $data[$key] = match (true) {
                    $value === 'ya' => 'Ya',
                    $value === 'tidak' => 'Tidak',
                    default => null,
                };
            }

            Akpd::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tanggal' => Carbon::parse($tanggal)],
                $data
            );

            $imported++;
        }

        return compact('imported', 'errors');
    }

    /**
     * Temukan kolom soal ke-$no. Header gform memakai "{no}. <teks>" (mis. "1. Kualitas ibadah...")
     * yang ternormalisasi jadi "{no}_<teks>" — cocokkan key yang diawali angka soal.
     */
    private function questionColumnValue(array $row, int $no): mixed
    {
        foreach ($row as $key => $value) {
            $norm = trim((string) $key);

            if (preg_match('/^\d+/', $norm, $m) === 1 && (int) $m[0] === $no) {
                return $value;
            }
        }

        return null;
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
