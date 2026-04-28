<?php

namespace App\Services;

use App\Constants\GlobalMessages;
use App\Models\DataSiswa;
use App\Repositories\Contracts\SiswaRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class SiswaService
{
    public function __construct(
        protected SiswaRepositoryInterface $siswaRepository
    ) {}

    // ─────────────────────────────────────────
    // READ
    // ─────────────────────────────────────────

    public function getAll(): Collection
    {
        return $this->siswaRepository->getAll();
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
     * Lempar ValidationException jika NIS sudah dipakai.
     */
    public function create(array $data): DataSiswa
    {
        $this->ensureNisUnique($data['nis']);

        return $this->siswaRepository->create($data);
    }

    /**
     * Perbarui data siswa.
     * Lempar ValidationException jika NIS baru sudah dipakai siswa lain.
     */
    public function update(int $id, array $data): DataSiswa
    {
        $existing = $this->siswaRepository->findById($id);

        // Cek NIS hanya jika berubah
        if ((int) $data['nis'] !== (int) $existing->nis) {
            $this->ensureNisUnique($data['nis']);
        }

        return $this->siswaRepository->update($id, $data);
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

    // ─────────────────────────────────────────
    // IMPORT
    // ─────────────────────────────────────────

    /**
     * Parse file Excel/CSV lalu lakukan bulk upsert.
     *
     * Format kolom yang diharapkan (urutan bebas, cek header):
     *   nis | nama | kelas | jenis_kelamin | jurusan | periode_ajaran
     *
     * @return array{imported: int, errors: array}
     */
    public function importFromFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        $rows = match ($extension) {
            'csv'           => $this->parseCsv($file),
            'xlsx', 'xls'   => $this->parseExcel($file),
            default         => throw new \InvalidArgumentException(
                'Format file tidak didukung. Gunakan CSV, XLS, atau XLSX.'
            ),
        };

        [$validRows, $errors] = $this->validateImportRows($rows);

        $imported = 0;
        if (!empty($validRows)) {
            $imported = $this->siswaRepository->bulkUpsert($validRows);
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
     * Kembalikan array data siswa yang siap di-export ke CSV.
     */
    public function exportToCsv(array $filters = []): string
    {
        $siswaList = empty($filters)
            ? $this->siswaRepository->getAll()
            : $this->siswaRepository->getPaginated(array_merge($filters, ['per_page' => 99999]))->getCollection();

        $header = ['NIS', 'Nama', 'Kelas', 'Jenis Kelamin', 'Jurusan', 'Periode Ajaran'];

        $output  = fopen('php://temp', 'r+');
        fputcsv($output, $header);

        foreach ($siswaList as $siswa) {
            fputcsv($output, [
                $siswa->nis,
                $siswa->nama,
                $siswa->kelas,
                $siswa->jenis_kelamin,
                $siswa->jurusan,
                $siswa->periode_ajaran,
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    // ─────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────

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
            $jenisKelamin = ucfirst(strtolower($normalized['jenis_kelamin']));
            if (!in_array($jenisKelamin, ['Laki-laki', 'Perempuan'], true)) {
                // Coba normalisasi singkatan umum
                $jenisKelamin = match (strtolower($normalized['jenis_kelamin'])) {
                    'l', 'lk', 'laki'                    => 'Laki-laki',
                    'p', 'pr', 'perempuan', 'wanita', 'w' => 'Perempuan',
                    default                               => null,
                };

                if ($jenisKelamin === null) {
                    $errors[] = "Baris {$lineNumber}: Jenis kelamin tidak valid (isi: Laki-laki atau Perempuan).";
                    continue;
                }
            }

            $validRows[] = [
                'nis'            => (int) $normalized['nis'],
                'nama'           => $normalized['nama'],
                'kelas'          => (int) $normalized['kelas'],
                'jenis_kelamin'  => $jenisKelamin,
                'jurusan'        => strtoupper($normalized['jurusan']),
                'periode_ajaran' => $normalized['periode_ajaran'],
            ];
        }

        return [$validRows, $errors];
    }
}