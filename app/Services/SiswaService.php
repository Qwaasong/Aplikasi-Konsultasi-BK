<?php

namespace App\Services;

use App\Models\DataSiswa;
use App\Repositories\Contracts\SiswaRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class SiswaService
{
    public function __construct(
        protected SiswaRepositoryInterface $siswaRepository,
        protected ImportExportService $importExportService,
    ) {}

    // ─────────────────────────────────────────
    // READ
    // ─────────────────────────────────────────

    public function getAll(): Collection
    {
        return $this->siswaRepository->getAll();
    }

    public function getTotalSiswa()
    {
        // Memanggil fungsi dari repository
        return $this->siswaRepository->countSiswa();
    }

    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        return $this->siswaRepository->getPaginated($filters);
    }

    public function search(string $keyword = '', int $limit = 50): Collection
    {
        return $this->siswaRepository->search($keyword, $limit);
    }

    public function findById(int $id): DataSiswa
    {
        return $this->siswaRepository->findById($id);
    }

    public function getFilterOptions(): array
    {
        return [
            'jurusan' => $this->siswaRepository->getJurusan(),
            'kelas'   => $this->siswaRepository->getKelas(),
            'periode' => $this->siswaRepository->getPeriode(),
        ];
    }

    public function getStats(): array
    {
        return $this->siswaRepository->getStats();
    }

    // ─────────────────────────────────────────
    // WRITE
    // ─────────────────────────────────────────

    /**
     * Buat siswa baru.
     * Otomatis membuat User akun dan menghubungkan ke Kelas yang dipilih.
     * Lempar ValidationException jika NIS sudah dipakai.
     */
    public function create(array $data): DataSiswa
    {
        $this->ensureNisUnique($data['nis']);

        // Buat user terlebih dahulu
        $user = \App\Models\User::create([
            'nama' => $data['nama'],
            'username' => $data['username'] ?? 'siswa_' . $data['nis'],
            'email' => $data['email'] ?? $data['nis'] . '@sekolah.sch.id',
            'jenis_kelamin' => $data['jenis_kelamin'] ?? 'L',
            'no_hp' => $data['no_hp'] ?? '-',
            'foto' => '',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'status' => 'aktif',
        ]);

        // Siapkan data untuk tabel data_siswa
        $siswaData = [
            'user_id' => $user->id,
            'nis' => (int) $data['nis'],
            'kelas_id' => (int) ($data['kelas'] ?? $data['kelas_id'] ?? 0),
            'alamat' => $data['alamat'] ?? '',
        ];

        return $this->siswaRepository->create($siswaData);
    }

    /**
     * Perbarui data siswa dan user terkait.
     * Lempar ValidationException jika NIS baru sudah dipakai siswa lain.
     */
    public function update(int $id, array $data): DataSiswa
    {
        $existing = $this->siswaRepository->findById($id);

        // Cek NIS hanya jika berubah
        if ((int) $data['nis'] !== (int) $existing->nis) {
            $this->ensureNisUnique($data['nis']);
        }

        // Update data user
        if ($existing->user) {
            $existing->user->update([
                'nama' => $data['nama'] ?? $existing->user->nama,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? $existing->user->jenis_kelamin,
            ]);
        }

        // Siapkan data untuk tabel data_siswa
        $siswaData = [
            'nis' => (int) $data['nis'],
            'kelas_id' => (int) ($data['kelas'] ?? $data['kelas_id'] ?? $existing->kelas_id),
            'alamat' => $data['alamat'] ?? $existing->alamat ?? '',
        ];

        return $this->siswaRepository->update($id, $siswaData);
    }

    /**
     * Hapus siswa.
     * Siswa yang punya riwayat konsultasi tetap bisa dihapus
     * (konsultasi ter-cascade berdasarkan migration).
     */
    public function delete(int $id): void
    {
        $this->siswaRepository->delete($id);
    }

    public function getFiltered(array $filters = []): \Illuminate\Support\Collection
    {
        $query = \App\Models\DataSiswa::with(['user', 'kelas.jurusan.sekolah']);

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('nis', 'like', "%{$keyword}%")
                    ->orWhereHas('kelas', fn($q2) => $q2->where('nama_kelas', 'like', "%{$keyword}%"))
                    ->orWhereHas('kelas.jurusan', fn($q2) => $q2->where('nama_jurusan', 'like', "%{$keyword}%"));
            });
        }

        if (!empty($filters['kelas'])) {
            $query->whereHas('kelas', fn($q) => $q->where('nama_kelas', $filters['kelas']));
        }

        if (!empty($filters['jurusan'])) {
            $query->whereHas('kelas.jurusan', fn($q) => $q->where('nama_jurusan', $filters['jurusan']));
        }

        if (!empty($filters['jenis_kelamin'])) {
            $query->where('jenis_kelamin', $filters['jenis_kelamin']);
        }

        return $query->latest()->get();
    }

    // ─────────────────────────────────────────
    // IMPORT
    // ─────────────────────────────────────────

    /**
     * Parse file Excel/CSV lalu lakukan import.
     *
     * Format kolom yang diharapkan (urutan bebas, cek header):
     *   nis | nama | kelas | jenis_kelamin | jurusan | periode_ajaran
     *
     * @return array{imported: int, errors: array}
     */
    public function importFromFile(UploadedFile $file): array
    {
        $rows = $this->importExportService->parseUploadedFile($file);

        [$validRows, $errors] = $this->validateImportRows($rows);

        $imported = 0;
        foreach ($validRows as $row) {
            $this->importSingleRow($row);
            $imported++;
        }

        return [
            'imported' => $imported,
            'errors'   => $errors,
        ];
    }

    // ─────────────────────────────────────────
    // EXPORT
    // ─────────────────────────────────────────

    /**
     * Kembalikan string CSV dari data siswa sesuai filter.
     * Filter yang didukung: kelas, jurusan, periode_ajaran.
     */
    public function exportToCsv(array $filters = []): string
    {
        // Hapus key yang null agar tidak mengganggu query
        $filters = array_filter($filters, fn ($v) => $v !== null && $v !== '');

        $siswaList = empty($filters)
            ? $this->siswaRepository->getAll()
            : $this->siswaRepository->getPaginated(
                array_merge($filters, ['per_page' => 99999])
              )->getCollection();

        $header = ['NIS', 'Nama', 'Kelas', 'Jenis Kelamin', 'Jurusan', 'Periode Ajaran'];

        $output  = fopen('php://temp', 'r+');
        fputcsv($output, $header);

        foreach ($siswaList as $siswa) {
            fputcsv($output, [
                $siswa->nis,
                $siswa->nama,
                $siswa->kelas_label,
                $siswa->jenis_kelamin,
                $siswa->kelas?->jurusan?->nama_jurusan ?? '',
                $siswa->periode_ajaran ?? '',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    // ─────────────────────────────────────────
    // IMPORT/EXPORT ENHANCED
    // ─────────────────────────────────────────

    public function getTemplateHeaders(): array
    {
        return ['nis', 'nama', 'kelas', 'jenis_kelamin', 'jurusan', 'periode_ajaran'];
    }

    public function getTemplateSampleRows(): array
    {
        return [
            [
                'nis' => '1234567890',
                'nama' => 'Budi Santoso',
                'kelas' => '10',
                'jenis_kelamin' => 'L',
                'jurusan' => 'RPL',
                'periode_ajaran' => '2025/2026',
            ],
        ];
    }

    public function exportRows(array $filters = []): array
    {
        $filters = array_filter($filters, fn ($v) => $v !== null && $v !== '');

        $siswaList = empty($filters)
            ? $this->siswaRepository->getAll()
            : $this->siswaRepository->getPaginated(
                array_merge($filters, ['per_page' => 99999])
              )->getCollection();

        return $siswaList->map(fn ($siswa) => [
            'nis' => $siswa->nis,
            'nama' => $siswa->nama,
            'kelas' => $siswa->kelas_label,
            'jenis_kelamin' => $siswa->jenis_kelamin,
            'jurusan' => $siswa->kelas?->jurusan?->nama_jurusan ?? '',
            'periode_ajaran' => $siswa->periode_ajaran ?? '',
        ])->toArray();
    }

    public function getExportCount(array $filters = []): int
    {
        $filters = array_filter($filters, fn ($v) => $v !== null && $v !== '');

        return empty($filters)
            ? $this->siswaRepository->countSiswa()
            : $this->siswaRepository->getPaginated(
                array_merge($filters, ['per_page' => 1, 'page' => 1])
              )->total();
    }

    // ─────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────

    // ─────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────

    private function importSingleRow(array $row): void
    {
        $existing = $this->siswaRepository->findByNis($row['nis']);

        $kelasId = $this->resolveKelasId($row['kelas'], $row['jurusan']);

        if ($existing) {
            $this->update($existing->id, [
                'nis' => $row['nis'],
                'nama' => $row['nama'],
                'kelas_id' => $kelasId,
                'jenis_kelamin' => $row['jenis_kelamin'],
                'alamat' => $row['alamat'] ?? '',
            ]);
            return;
        }

        // Check if user with same email or username already exists (orphan from failed previous import)
        $email = $row['email'] ?? ($row['nis'] . '@sekolah.sch.id');
        $username = 'siswa_' . $row['nis'];

        $existingUser = \App\Models\User::where('email', $email)
            ->orWhere('username', $username)
            ->first();

        if ($existingUser) {
            // Link existing user to new DataSiswa record
            $this->siswaRepository->create([
                'user_id' => $existingUser->id,
                'nis' => (int) $row['nis'],
                'kelas_id' => $kelasId,
                'alamat' => $row['alamat'] ?? '',
            ]);
            return;
        }

        $this->create([
            'nis' => $row['nis'],
            'nama' => $row['nama'],
            'kelas' => $kelasId,
            'jenis_kelamin' => $row['jenis_kelamin'],
            'email' => $row['email'] ?? null,
            'no_hp' => $row['no_hp'] ?? '-',
            'alamat' => $row['alamat'] ?? '',
        ]);
    }

    private function resolveKelasId(int $tingkat, string $jurusanCode): int
    {
        // Map Arabic numerals to Roman numerals for tingkat
        $tingkatMap = [
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            'X' => 'X',
            'XI' => 'XI',
            'XII' => 'XII',
        ];
        $tingkatValue = $tingkatMap[$tingkat] ?? $tingkat;

        // Try to find jurusan by code (numeric) or name
        $jurusan = \App\Models\Jurusan::where('kode_jurusan', $jurusanCode)
            ->orWhere('nama_jurusan', 'like', "%{$jurusanCode}%")
            ->first();

        $query = \App\Models\Kelas::where('tingkat', $tingkatValue);

        if ($jurusan) {
            $query->where('jurusan_id', $jurusan->id);
        }

        $kelas = $query->first();

        return $kelas?->id ?? 0;
    }

    private function ensureNisUnique(int|string $nis): void
    {
        if ($this->siswaRepository->findByNis((int) $nis)) {
            throw ValidationException::withMessages([
                'nis' => 'NIS ' . $nis . ' sudah digunakan oleh siswa lain.',
            ]);
        }
    }

    /** Parse CSV → array of associative arrays */
    private function parseCsv(UploadedFile $file): array
    {
        $rows    = [];
        $handle  = fopen($file->getRealPath(), 'r');
        $headers = null;

        while (($line = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                // Normalisasi header: lowercase, ganti spasi dengan underscore
                $headers = array_map(
                    fn (string $h) => strtolower(trim(str_replace(' ', '_', $h))),
                    $line
                );
                continue;
            }

            if (count($line) === count($headers)) {
                $rows[] = array_combine($headers, $line);
            }
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Parse Excel menggunakan PhpSpreadsheet (opsional).
     * Fallback ke CSV parser jika library tidak tersedia.
     */
    private function parseExcel(UploadedFile $file): array
    {
        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            throw new \RuntimeException(
                'Library PhpSpreadsheet tidak ditemukan. ' .
                'Jalankan: composer require phpoffice/phpspreadsheet'
            );
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true);

        if (empty($rows)) {
            return [];
        }

        // Baris pertama sebagai header
        $rawHeaders = array_shift($rows);
        $headers    = array_map(
            fn (string $h) => strtolower(trim(str_replace(' ', '_', (string) $h))),
            $rawHeaders
        );

        $result = [];
        foreach ($rows as $row) {
            $values = array_values($row);
            if (count($values) === count($headers)) {
                $result[] = array_combine($headers, $values);
            }
        }

        return $result;
    }

    /**
     * Validasi setiap baris import.
     *
     * @return array{0: array, 1: array}  [$validRows, $errors]
     */
    private function validateImportRows(array $rows): array
    {
        $validRows  = [];
        $errors     = [];

        // Kolom wajib yang harus ada
        $required = ['nis', 'nama', 'kelas', 'jenis_kelamin', 'jurusan', 'periode_ajaran'];

        // Mapping alias header yang umum dipakai
        $aliases = [
            'no_induk'      => 'nis',
            'no induk'      => 'nis',
            'jenis kelamin' => 'jenis_kelamin',
            'periode'       => 'periode_ajaran',
            'tahun_ajaran'  => 'periode_ajaran',
        ];

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2; // +2 karena baris 1 = header

            // Normalisasi kunci menggunakan alias
            $normalized = [];
            foreach ($row as $key => $value) {
                $key             = strtolower(trim($key));
                $key             = $aliases[$key] ?? $key;
                $normalized[$key] = trim((string) $value);
            }

            // Cek kolom wajib
            $missing = array_filter(
                $required,
                fn (string $col) => !isset($normalized[$col]) || $normalized[$col] === ''
            );

            if (!empty($missing)) {
                $errors[] = "Baris {$lineNumber}: Kolom " . implode(', ', $missing) . ' tidak boleh kosong.';
                continue;
            }

            // Validasi NIS harus berupa angka
            if (!ctype_digit($normalized['nis'])) {
                $errors[] = "Baris {$lineNumber}: NIS harus berupa angka.";
                continue;
            }

            // Validasi kelas harus berupa angka antara 10-12
            if (!in_array((int) $normalized['kelas'], [10, 11, 12], true)) {
                $errors[] = "Baris {$lineNumber}: Kelas harus bernilai 10, 11, atau 12.";
                continue;
            }

            // Validasi jenis kelamin
            $jenisKelamin = match (strtolower($normalized['jenis_kelamin'])) {
                'l', 'lk', 'laki', 'laki-laki'         => 'L',
                'p', 'pr', 'perempuan', 'wanita', 'w'  => 'P',
                default                                 => null,
            };

            if ($jenisKelamin === null) {
                $errors[] = "Baris {$lineNumber}: Jenis kelamin tidak valid (isi: Laki-laki atau Perempuan).";
                continue;
            }

            $validRows[] = [
                'nis'            => (int) $normalized['nis'],
                'nama'           => $normalized['nama'],
                'kelas'          => (int) $normalized['kelas'],
                'jenis_kelamin'  => $jenisKelamin,
                'jurusan'        => strtoupper($normalized['jurusan']),
                'periode_ajaran' => $normalized['periode_ajaran'],
                'alamat'         => $normalized['alamat'] ?? '',
                'email'          => $normalized['email'] ?? null,
                'no_hp'          => $normalized['no_hp'] ?? '-',
            ];
        }

        return [$validRows, $errors];
    }
}