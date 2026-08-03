<?php

namespace App\Services\Siswa;

use App\Models\DataSiswa;
use App\Models\Kehadiran;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Repositories\Contracts\Siswa\KehadiranRepositoryInterface;
use App\Services\ImportExportService;
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
            'email' => $record->siswa?->user?->email ?? '',
            'no_hp' => $record->siswa?->user?->no_hp ?? '',
            'jenis_kelamin' => $record->siswa?->jenis_kelamin ?? '',
            'kelas' => $record->siswa?->kelas_label ?? '',
            'jurusan' => $record->siswa?->jurusan_label ?? '',
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
        return [
            'nis', 'nama', 'email', 'no_hp', 'jenis_kelamin',
            'kelas', 'jurusan',
            'tanggal_kehadiran', 'status', 'tahun_ajaran', 'semester',
        ];
    }

    public function getTemplateSampleRows(): array
    {
        $activeYear = TahunAjaran::where('status_aktif', true)->first();

        return [
            [
                'nis' => '1234567890',
                'nama' => 'Budi Santoso',
                'email' => '1234567890@sekolah.sch.id',
                'no_hp' => '08123456789',
                'jenis_kelamin' => 'L',
                'kelas' => '10',
                'jurusan' => 'RPL',
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

            // Cari atau buat siswa
            $siswa = DataSiswa::where('nis', $nis)->first();
            $nama = trim((string) ($row['nama'] ?? ''));

            if (!$siswa) {
                if ($nama === '') {
                    $errors[] = "Baris {$lineNumber}: NIS {$nis} tidak ditemukan. Isi kolom nama untuk membuat siswa baru.";
                    continue;
                }
                $siswa = $this->ensureSiswaExists($row, $nis, $nama);
                if (!$siswa) {
                    $errors[] = "Baris {$lineNumber}: gagal membuat siswa dengan NIS {$nis}.";
                    continue;
                }
            } elseif ($nama !== '') {
                // Update data siswa jika ada data tambahan
                $this->updateSiswa($siswa, $row);
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

    private function ensureSiswaExists(array $row, string $nis, string $nama): ?DataSiswa
    {
        $email = trim((string) ($row['email'] ?? '')) ?: ($nis . '@sekolah.sch.id');
        $noHp = trim((string) ($row['no_hp'] ?? '')) ?: '-';
        $jenisKelaminRaw = strtolower(trim((string) ($row['jenis_kelamin'] ?? '')));
        $kelasRaw = trim((string) ($row['kelas'] ?? ''));
        $jurusanRaw = trim((string) ($row['jurusan'] ?? ''));

        $jenisKelamin = match ($jenisKelaminRaw) {
            'l', 'lk', 'laki', 'laki-laki' => 'L',
            'p', 'pr', 'perempuan', 'wanita', 'w' => 'P',
            default => 'L',
        };

        $kelasId = 0;
        if ($kelasRaw !== '') {
            $tingkatMap = ['10' => 'X', '11' => 'XI', '12' => 'XII', 'X' => 'X', 'XI' => 'XI', 'XII' => 'XII'];
            $tingkat = $tingkatMap[$kelasRaw] ?? $kelasRaw;

            $jurusan = null;
            if ($jurusanRaw !== '') {
                $jurusan = \App\Models\Jurusan::where('kode_jurusan', $jurusanRaw)
                    ->orWhere('nama_jurusan', 'like', "%{$jurusanRaw}%")
                    ->first();
            }

            $query = \App\Models\Kelas::where('tingkat', $tingkat);
            if ($jurusan) {
                $query->where('jurusan_id', $jurusan->id);
            }
            $kelas = $query->first();
            $kelasId = $kelas?->id ?? 0;
        }

        try {
            $user = User::create([
                'nama' => $nama,
                'username' => 'siswa_' . $nis,
                'email' => $email,
                'jenis_kelamin' => $jenisKelamin,
                'no_hp' => $noHp,
                'foto' => '',
                'password' => bcrypt('password'),
                'role' => 'siswa',
                'status' => 'aktif',
            ]);

            return DataSiswa::create([
                'user_id' => $user->id,
                'nis' => (int) $nis,
                'kelas_id' => $kelasId,
                'alamat' => trim((string) ($row['alamat'] ?? '')),
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function updateSiswa(DataSiswa $siswa, array $row): void
    {
        if ($siswa->user) {
            $userUpdate = [];
            if (!empty($row['email'])) $userUpdate['email'] = trim($row['email']);
            if (!empty($row['no_hp'])) $userUpdate['no_hp'] = trim($row['no_hp']);
            if (!empty($userUpdate)) {
                $siswa->user->update($userUpdate);
            }
        }

        $siswaUpdate = [];
        if (!empty($row['kelas']) && !empty($row['jurusan'])) {
            $tingkatMap = ['10' => 'X', '11' => 'XI', '12' => 'XII'];
            $tingkat = $tingkatMap[$row['kelas']] ?? $row['kelas'];
            $jurusan = \App\Models\Jurusan::where('kode_jurusan', $row['jurusan'])
                ->orWhere('nama_jurusan', 'like', "%{$row['jurusan']}%")
                ->first();
            if ($jurusan) {
                $kelas = \App\Models\Kelas::where('tingkat', $tingkat)
                    ->where('jurusan_id', $jurusan->id)
                    ->first();
                if ($kelas) {
                    $siswaUpdate['kelas_id'] = $kelas->id;
                }
            }
        }
        if (!empty($siswaUpdate)) {
            $siswa->update($siswaUpdate);
        }
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
