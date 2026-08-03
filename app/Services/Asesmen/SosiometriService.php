<?php

namespace App\Services\Asesmen;

use App\Models\DataSiswa;
use App\Models\Sosiometri;
use App\Repositories\Contracts\Asesmen\SosiometriRepositoryInterface;
use App\Services\ImportExportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\Asesmen\AsesmenImportHelper;

class SosiometriService
{
    public function __construct(
        protected SosiometriRepositoryInterface $repo,
        protected ImportExportService $importExportService
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }

    public function findById(int $id): ?Sosiometri
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Sosiometri
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): Sosiometri
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
        return Sosiometri::with('siswa')
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
                    ->orWhere('judul', 'like', "%{$keyword}%");
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
     * Format gform (disamakan karena belum ada contoh klien):
     *   Timestamp | Nama Lengkap | Kelas | Tahun Pelajaran | Q1. <pertanyaan> .. Q5. <pertanyaan>
     * Sel Qn = nama teman dipisah koma.
     */
    public function exportRows(array $filters = []): array
    {
        return $this->getFiltered($filters)->each->load('respons.siswaDipilih')->map(function (Sosiometri $s) {
            $row = [
                'Timestamp' => $s->created_at?->format('Y-m-d') ?? '',
                'Nama Lengkap' => $s->siswa?->nama ?? '',
                'Kelas' => $s->siswa?->kelas_label ?? '',
                'Tahun Pelajaran' => $s->created_at?->format('Y') ?? '',
            ];

            $respons = $s->respons ?? collect();

            foreach (Sosiometri::PERTANYAAN as $key => $pertanyaan) {
                $names = $respons
                    ->where('pertanyaan', $key)
                    ->sortBy('urutan')
                    ->map(fn ($r) => $r->siswaDipilih?->nama ?? '')
                    ->filter()
                    ->values()
                    ->implode(', ');

                $row[$key.'. '.$pertanyaan] = $names;
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
        // Gunakan format numerik "1. Pertanyaan" agar konsisten dengan ekspor Google Forms
        $headers = ['Timestamp', 'Nama Lengkap', 'Kelas'];

        foreach (Sosiometri::PERTANYAAN as $key => $pertanyaan) {
            $num = preg_replace('/[^0-9]/', '', $key);
            $headers[] = $num.'. '.$pertanyaan;
        }

        return $headers;
    }

    public function getTemplateSampleRows(): array
    {
        $row = [
            'Timestamp'    => '01/08/2026 23:44:58',
            'Nama Lengkap' => 'AHMAD FAUZI',
            'Kelas'        => 'XII RPL',
        ];

        foreach (Sosiometri::PERTANYAAN as $key => $pertanyaan) {
            $num = preg_replace('/[^0-9]/', '', $key);
            $row[$num.'. '.$pertanyaan] = '';
        }

        // Contoh jawaban Q1
        $num1 = '1';
        $row[$num1.'. '.Sosiometri::PERTANYAAN['Q1']] = 'Ahmad, Budi, Charlie';

        return [$row];
    }

    private function validateAndImport(array $rows): array
    {
        $imported = 0;
        $errors   = [];

        // Kolom identitas — support format manual (nama+kelas) dan format GForm (email berisi NIS)
        $namaKey      = $this->firstKey($rows[0] ?? [], ['nama_lengkap', 'nama_siswa', 'nama']);
        $kelasKey     = $this->firstKey($rows[0] ?? [], ['kelas']);
        $emailKey     = $this->firstKey($rows[0] ?? [], ['alamat_email', 'email_address', 'email']);
        $timestampKey = $this->firstKey($rows[0] ?? [], ['timestamp']);
        $judul        = 'Asesmen Sosiometri';

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2;

            $nama    = trim((string) ($row[$namaKey] ?? ''));
            $kelas   = trim((string) ($row[$kelasKey] ?? ''));
            $email   = trim((string) ($row[$emailKey] ?? ''));
            $tanggal = AsesmenImportHelper::parseTimestamp($row[$timestampKey] ?? null);

            $siswa = null;

            // 1. Lookup via nama + kelas (format template manual)
            if ($nama !== '' && $kelas !== '') {
                $siswa = AsesmenImportHelper::resolveSiswa($nama, $kelas);
            }

            // 2. Fallback: lookup via email (format GForm — email seperti "20243136@siswa.smkn9malang.sch.id")
            if (! $siswa && $email !== '') {
                // Coba cari berdasarkan email langsung di tabel users
                $siswa = DataSiswa::whereHas(
                    'user',
                    fn ($q) => $q->where('email', $email)
                )->first();

                // Jika tidak ketemu via email, ekstrak NIS dari bagian sebelum "@"
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

            DB::transaction(function () use ($siswa, $row, $judul, $lineNumber, &$errors) {
                $sosiometri = Sosiometri::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'judul' => $judul],
                    [
                        'instruksi'      => 'Pilih teman sekelas Anda.',
                        'jumlah_pilihan' => 3,
                    ]
                );

                $sosiometri->respons()->delete();

                foreach (Sosiometri::PERTANYAAN as $key => $pertanyaan) {
                    $cell  = $this->questionCellValue($row, $key);
                    $names = array_values(array_filter(
                        array_map('trim', preg_split('/[,;]/', (string) $cell, -1, PREG_SPLIT_NO_EMPTY))
                    ));

                    foreach ($names as $urutan => $name) {
                        if ($urutan >= 3) break;

                        $dipilih = DataSiswa::query()
                            ->whereHas('user', fn ($q) => $q->whereRaw('LOWER(nama) = ?', [mb_strtolower($name)]))
                            ->first();

                        if (! $dipilih) {
                            $errors[] = "Baris {$lineNumber}: teman \"{$name}\" ({$key}) tidak ditemukan.";
                            continue;
                        }

                        $sosiometri->respons()->create([
                            'siswa_dipilih_id' => $dipilih->id,
                            'siswa_pemilih_id' => $siswa->id,
                            'urutan'           => $urutan + 1,
                            'alasan'           => '',
                            'pertanyaan'       => $key,
                        ]);
                    }
                }
            });

            $imported++;
        }

        return compact('imported', 'errors');
    }

    /**
     * Temukan nilai sel untuk pertanyaan sosiometri berdasarkan kunci (mis. "Q1").
     * Mendukung dua format header kolom:
     *   - Format template kami  : "Q1. Siapa teman..." / normalisasi "q1_siapa..."
     *   - Format ekspor GForm   : "1. Siapa teman..."  / normalisasi "1_siapa..."
     */
    private function questionCellValue(array $row, string $key): mixed
    {
        // Ekstrak nomor dari key: "Q1" → "1", "Q2" → "2"
        $num      = preg_replace('/[^0-9]/', '', $key);
        $keyUpper = strtoupper($key); // "Q1"

        foreach ($row as $col => $value) {
            $colStr = trim((string) $col);

            // Format template kami: "Q1." atau "Q1 " (asli dan setelah normalisasi "q1_")
            if (
                str_starts_with(strtoupper($colStr), $keyUpper.'.') ||
                str_starts_with(strtoupper($colStr), $keyUpper.' ') ||
                preg_match('/^'.preg_quote(strtolower($keyUpper), '/').'[_]+/i', $colStr)
            ) {
                return $value;
            }

            // Format GForm: "1." atau "1 " (asli) dan normalisasi "1_" atau "1__"
            if ($num !== '' && (
                str_starts_with($colStr, $num.'.') ||
                str_starts_with($colStr, $num.' ') ||
                preg_match('/^'.preg_quote($num, '/').'[_]+/', $colStr)
            )) {
                return $value;
            }
        }

        // Fallback: cari key lowercase atau angka saja
        return $row[strtolower($key)] ?? $row[$num] ?? '';
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
