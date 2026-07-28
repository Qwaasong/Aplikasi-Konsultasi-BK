<?php

namespace App\Services;

use App\Models\DataSiswa;
use App\Models\Kehadiran;
use App\Models\TahunAjaran;
use App\Repositories\Contracts\KehadiranRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class KehadiranService
{
    private const STATUS_MAP = [
        'hadir' => 'Hadir',
        'sakit' => 'Sakit',
        'izin' => 'Izin',
        'alpha' => 'Alpha',
        'absen' => 'Alpha',
        'a' => 'Alpha',
        'i' => 'Izin',
        's' => 'Sakit',
        'h' => 'Hadir',
    ];

    public function __construct(
        protected KehadiranRepositoryInterface $repo,
        protected ImportExportService $importExportService,
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?Kehadiran
    {
        return $this->repo->findById($id);
    }

    public function findByIdForCurrentUser(int $id): Kehadiran
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Kehadiran
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }

    public function getFiltered(array $filters = []): Collection
    {
        $query = $this->repo->query();

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->whereHas('siswa.user', fn($q) => $q->where('nama', 'like', "%{$keyword}%"));
        }

        if (!empty($filters['kelas'])) {
            $query->whereHas('siswa', fn($q) => $q->whereHas('kelas', fn($q2) => $q2->where('nama_kelas', $filters['kelas'])));
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['tanggal'])) {
            $query->whereDate('tanggal_kehadiran', $filters['tanggal']);
        }

        if (!empty($filters['tahun'])) {
            $query->whereHas('tahunAjaran', fn($q) => $q->where('tahun', $filters['tahun']));
        }

        return $query->latest('tanggal_kehadiran')->get();
    }

    public function getFilterOptions(): array
    {
        $all = $this->getAll();

        return [
            'kelasOptions' => $all->pluck('siswa.kelas_label')->filter()->unique()->sort()->values()->toArray(),
            'statusOptions' => $all->pluck('status')->filter()->unique()->values()->toArray(),
            'tahunOptions' => $all->pluck('tahunAjaran.tahun')->filter()->unique()->values()->toArray(),
        ];
    }

    // ── IMPORT / EXPORT ─────────────────────────────────

    public function importFromFile(UploadedFile $file): array
    {
        $rows = $this->importExportService->parseUploadedFile($file);

        return $this->validateAndImport($rows);
    }

    public function exportRows(array $filters = []): array
    {
        return $this->getFiltered($filters)->map(fn (Kehadiran $record) => [
            'nis' => $record->siswa?->nis ?? '',
            'nama' => $record->siswa->user->nama ?? '',
            'tanggal_kehadiran' => optional($record->tanggal_kehadiran)->format('Y-m-d'),
            'status' => $record->status,
            'tahun_ajaran' => $record->tahunAjaran?->tahun ?? '',
            'semester' => $record->tahunAjaran?->semester ?? '',
        ])->toArray();
    }

    public function getExportCount(array $filters = []): int
    {
        return $this->getFiltered($filters)->count();
    }

    public function getTemplateHeaders(): array
    {
        return ['nis', 'nama', 'tanggal_kehadiran', 'status', 'tahun_ajaran', 'semester'];
    }

    public function getTemplateSampleRows(): array
    {
        $activeYear = TahunAjaran::where('status_aktif', true)->first();

        return [
            [
                'nis' => '1234567890',
                'nama' => 'Budi Santoso',
                'tanggal_kehadiran' => now()->format('Y-m-d'),
                'status' => 'Hadir',
                'tahun_ajaran' => $activeYear?->tahun ?? '2025/2026',
                'semester' => $activeYear?->semester ?? 'Ganjil',
            ],
        ];
    }

    // ── PRIVATE HELPERS ─────────────────────────────────

    private function validateAndImport(array $rows): array
    {
        $validRows = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2;

            $nis = trim((string) ($row['nis'] ?? ''));
            $tanggal = trim((string) ($row['tanggal_kehadiran'] ?? ''));
            $statusRaw = strtolower(trim((string) ($row['status'] ?? '')));
            $tahunRaw = trim((string) ($row['tahun_ajaran'] ?? ''));
            $semesterRaw = trim((string) ($row['semester'] ?? ''));

            if ($nis === '' || $tanggal === '' || $statusRaw === '') {
                $errors[] = "Baris {$lineNumber}: kolom nis, tanggal_kehadiran, dan status wajib diisi.";
                continue;
            }

            $siswa = DataSiswa::where('nis', $nis)->first();
            if (!$siswa) {
                $errors[] = "Baris {$lineNumber}: NIS {$nis} tidak ditemukan.";
                continue;
            }

            $status = self::STATUS_MAP[$statusRaw] ?? null;
            if (!$status) {
                $errors[] = "Baris {$lineNumber}: status \"{$row['status']}\" tidak valid. Gunakan Hadir, Sakit, Izin, atau Alpha.";
                continue;
            }

            $parsedDate = $this->importExportService->normalizeDate($tanggal);
            if (!$parsedDate) {
                $errors[] = "Baris {$lineNumber}: tanggal \"{$tanggal}\" tidak valid.";
                continue;
            }

            $tahunAjaran = $this->resolveTahunAjaran($tahunRaw, $semesterRaw);
            if (!$tahunAjaran) {
                $errors[] = "Baris {$lineNumber}: tahun ajaran \"{$tahunRaw} {$semesterRaw}\" tidak ditemukan.";
                continue;
            }

            $validRows[] = [
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'tanggal_kehadiran' => $parsedDate,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($validRows)) {
            $this->repo->bulkUpsert($validRows);
        }

        return [
            'imported' => count($validRows),
            'errors' => $errors,
        ];
    }

    private function resolveTahunAjaran(string $tahun, string $semester): ?TahunAjaran
    {
        $query = TahunAjaran::query();

        if ($tahun !== '') {
            $query->where('tahun', $tahun);
        }

        if ($semester !== '') {
            $normalizedSemester = match (strtolower($semester)) {
                'ganjil' => 'Ganjil',
                'genap' => 'Genap',
                default => $semester,
            };
            $query->where('semester', $normalizedSemester);
        }

        return $query->first()
            ?? TahunAjaran::where('status_aktif', true)->first()
            ?? TahunAjaran::latest()->first();
    }
}
