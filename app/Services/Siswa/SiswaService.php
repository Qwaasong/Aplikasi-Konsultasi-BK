<?php

namespace App\Services\Siswa;

use App\Models\DataSiswa;
use App\Repositories\Contracts\Siswa\SiswaRepositoryInterface;
use App\Services\ImportExportService;
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
            'tempat_lahir' => $data['tempat_lahir'] ?? null,
            'tgl_lahir' => $data['tgl_lahir'] ?? null,
            'anak_ke' => $data['anak_ke'] ?? null,
            'jml_saudara' => $data['jml_saudara'] ?? null,
            'asal_smp' => $data['asal_smp'] ?? null,
            'agama' => $data['agama'] ?? null,
            'hobi' => $data['hobi'] ?? null,
            'bakat' => $data['bakat'] ?? null,
            'rencana_lulus' => $data['rencana_lulus'] ?? null,
            'detail_rencana_lulus' => $data['detail_rencana_lulus'] ?? null,
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
        if (isset($data['nis']) && (int) $data['nis'] !== (int) $existing->nis) {
            $this->ensureNisUnique($data['nis']);
        }

        // Update data user
        if ($existing->user) {
            $userUpdate = [];
            if (isset($data['nama'])) $userUpdate['nama'] = $data['nama'];
            if (isset($data['jenis_kelamin'])) $userUpdate['jenis_kelamin'] = $data['jenis_kelamin'];
            if (isset($data['email'])) $userUpdate['email'] = $data['email'];
            if (isset($data['no_hp'])) $userUpdate['no_hp'] = $data['no_hp'];
            if (!empty($userUpdate)) {
                $existing->user->update($userUpdate);
            }
        }

        // Siapkan data untuk tabel data_siswa
        $siswaData = [
            'nis' => (int) ($data['nis'] ?? $existing->nis),
            'kelas_id' => (int) ($data['kelas'] ?? $data['kelas_id'] ?? $existing->kelas_id),
            'alamat' => $data['alamat'] ?? $existing->alamat ?? '',
            'tempat_lahir' => $data['tempat_lahir'] ?? $existing->tempat_lahir,
            'tgl_lahir' => $data['tgl_lahir'] ?? $existing->tgl_lahir,
            'anak_ke' => $data['anak_ke'] ?? $existing->anak_ke,
            'jml_saudara' => $data['jml_saudara'] ?? $existing->jml_saudara,
            'asal_smp' => $data['asal_smp'] ?? $existing->asal_smp,
            'agama' => $data['agama'] ?? $existing->agama,
            'hobi' => $data['hobi'] ?? $existing->hobi,
            'bakat' => $data['bakat'] ?? $existing->bakat,
            'rencana_lulus' => $data['rencana_lulus'] ?? $existing->rencana_lulus,
            'detail_rencana_lulus' => $data['detail_rencana_lulus'] ?? $existing->detail_rencana_lulus,
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
            $query->whereHas('user', fn($q) => $q->where('jenis_kelamin', $filters['jenis_kelamin']));
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
        return [
            'nis', 'nama', 'email', 'no_hp', 'jenis_kelamin', 'status',
            'kelas', 'jurusan', 'periode_ajaran', 'alamat',
            'tempat_lahir', 'tgl_lahir', 'anak_ke', 'jml_saudara',
            'asal_smp', 'agama', 'hobi', 'bakat',
            'rencana_lulus', 'detail_rencana_lulus',
            'nama_ayah', 'nama_ibu', 'pendidikan_ayah', 'pendidikan_ibu',
            'pekerjaan_ayah', 'pekerjaan_ibu', 'telp_ortu', 'status_rumah',
            'dinding_rumah', 'lantai_rumah', 'jml_kamar', 'punya_kamar_sendiri',
            'jml_tv', 'kendaraan_mobil', 'kendaraan_motor',
            'biaya_sekolah_dari', 'kendaraan_ke_sekolah',
        ];
    }

    public function getTemplateSampleRows(): array
    {
        return [
            [
                'nis' => '1234567890',
                'nama' => 'Budi Santoso',
                'email' => '1234567890@sekolah.sch.id',
                'no_hp' => '08123456789',
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
                'kelas' => '10',
                'jurusan' => 'RPL',
                'periode_ajaran' => '2025/2026',
                'alamat' => 'Jl. Merdeka No. 1',
                'tempat_lahir' => 'Jakarta',
                'tgl_lahir' => '2008-05-15',
                'anak_ke' => '2',
                'jml_saudara' => '3',
                'asal_smp' => 'SMPN 1 Jakarta',
                'agama' => 'Islam',
                'hobi' => 'Membaca',
                'bakat' => 'Olahraga',
                'rencana_lulus' => 'Kuliah',
                'detail_rencana_lulus' => 'Teknik Informatika',
                'nama_ayah' => 'Ahmad',
                'nama_ibu' => 'Siti',
                'pendidikan_ayah' => 'S1',
                'pendidikan_ibu' => 'SMA',
                'pekerjaan_ayah' => 'PNS',
                'pekerjaan_ibu' => 'IRT',
                'telp_ortu' => '021123456',
                'status_rumah' => 'Milik Sendiri',
                'dinding_rumah' => 'Tembok',
                'lantai_rumah' => 'Keramik',
                'jml_kamar' => '3',
                'punya_kamar_sendiri' => 'true',
                'jml_tv' => '2',
                'kendaraan_mobil' => '1',
                'kendaraan_motor' => '2',
                'biaya_sekolah_dari' => 'Orang Tua',
                'kendaraan_ke_sekolah' => 'Motor',
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

        return $siswaList->map(function ($siswa) {
            $k = $siswa->keluarga;

            return [
                'nis' => $siswa->nis,
                'nama' => $siswa->nama,
                'email' => $siswa->user?->email ?? '',
                'no_hp' => $siswa->user?->no_hp ?? '',
                'jenis_kelamin' => $siswa->jenis_kelamin,
                'status' => $siswa->user?->status ?? 'aktif',
                'kelas' => $siswa->kelas_label,
                'jurusan' => $siswa->kelas?->jurusan?->nama_jurusan ?? '',
                'periode_ajaran' => $siswa->periode_ajaran ?? '',
                'alamat' => $siswa->alamat ?? '',
                'tempat_lahir' => $siswa->tempat_lahir ?? '',
                'tgl_lahir' => optional($siswa->tgl_lahir)->format('Y-m-d'),
                'anak_ke' => $siswa->anak_ke,
                'jml_saudara' => $siswa->jml_saudara,
                'asal_smp' => $siswa->asal_smp ?? '',
                'agama' => $siswa->agama ?? '',
                'hobi' => $siswa->hobi ?? '',
                'bakat' => $siswa->bakat ?? '',
                'rencana_lulus' => $siswa->rencana_lulus ?? '',
                'detail_rencana_lulus' => $siswa->detail_rencana_lulus ?? '',
                'nama_ayah' => $k?->nama_ayah ?? '',
                'nama_ibu' => $k?->nama_ibu ?? '',
                'pendidikan_ayah' => $k?->pendidikan_ayah ?? '',
                'pendidikan_ibu' => $k?->pendidikan_ibu ?? '',
                'pekerjaan_ayah' => $k?->pekerjaan_ayah ?? '',
                'pekerjaan_ibu' => $k?->pekerjaan_ibu ?? '',
                'telp_ortu' => $k?->telp_ortu ?? '',
                'status_rumah' => $k?->status_rumah ?? '',
                'dinding_rumah' => $k?->dinding_rumah ?? '',
                'lantai_rumah' => $k?->lantai_rumah ?? '',
                'jml_kamar' => $k?->jml_kamar,
                'punya_kamar_sendiri' => $k?->punya_kamar_sendiri ?? '',
                'jml_tv' => $k?->jml_tv,
                'kendaraan_mobil' => $k?->kendaraan_mobil,
                'kendaraan_motor' => $k?->kendaraan_motor,
                'biaya_sekolah_dari' => $k?->biaya_sekolah_dari ?? '',
                'kendaraan_ke_sekolah' => $k?->kendaraan_ke_sekolah ?? '',
            ];
        })->toArray();
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
        $row['kelas'] = $kelasId;
        $row['kelas_id'] = $kelasId;

        if ($existing) {
            $siswa = $this->update($existing->id, $row);
            $this->importKomulatif($siswa->id, $row);
            return;
        }

        $email = $row['email'] ?? ($row['nis'] . '@sekolah.sch.id');
        $username = 'siswa_' . $row['nis'];

        $existingUser = \App\Models\User::where('email', $email)
            ->orWhere('username', $username)
            ->first();

        if ($existingUser) {
            $siswa = $this->siswaRepository->create([
                'user_id' => $existingUser->id,
                'nis' => (int) $row['nis'],
                'kelas_id' => $kelasId,
                'alamat' => $row['alamat'] ?? '',
                'tempat_lahir' => $row['tempat_lahir'] ?? null,
                'tgl_lahir' => $row['tgl_lahir'] ?? null,
                'anak_ke' => $row['anak_ke'] ?? null,
                'jml_saudara' => $row['jml_saudara'] ?? null,
                'asal_smp' => $row['asal_smp'] ?? null,
                'agama' => $row['agama'] ?? null,
                'hobi' => $row['hobi'] ?? null,
                'bakat' => $row['bakat'] ?? null,
                'rencana_lulus' => $row['rencana_lulus'] ?? null,
                'detail_rencana_lulus' => $row['detail_rencana_lulus'] ?? null,
            ]);
            $this->importKomulatif($siswa->id, $row);
            return;
        }

        $siswa = $this->create([
            'nis' => $row['nis'],
            'nama' => $row['nama'],
            'kelas' => $kelasId,
            'jenis_kelamin' => $row['jenis_kelamin'],
            'email' => $row['email'] ?? null,
            'no_hp' => $row['no_hp'] ?? '-',
            'alamat' => $row['alamat'] ?? '',
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'tgl_lahir' => $row['tgl_lahir'] ?? null,
            'anak_ke' => $row['anak_ke'] ?? null,
            'jml_saudara' => $row['jml_saudara'] ?? null,
            'asal_smp' => $row['asal_smp'] ?? null,
            'agama' => $row['agama'] ?? null,
            'hobi' => $row['hobi'] ?? null,
            'bakat' => $row['bakat'] ?? null,
            'rencana_lulus' => $row['rencana_lulus'] ?? null,
            'detail_rencana_lulus' => $row['detail_rencana_lulus'] ?? null,
        ]);
        $this->importKomulatif($siswa->id, $row);
    }

    private function importKomulatif(int $siswaId, array $row): void
    {
        $fields = [
            'nama_ayah', 'nama_ibu', 'pendidikan_ayah', 'pendidikan_ibu',
            'pekerjaan_ayah', 'pekerjaan_ibu', 'telp_ortu', 'status_rumah',
            'dinding_rumah', 'lantai_rumah', 'jml_kamar', 'punya_kamar_sendiri',
            'jml_tv', 'kendaraan_mobil', 'kendaraan_motor',
            'biaya_sekolah_dari', 'kendaraan_ke_sekolah',
        ];

        $hasData = false;
        $data = ['siswa_id' => $siswaId];
        foreach ($fields as $f) {
            if (isset($row[$f]) && $row[$f] !== '') {
                $data[$f] = $row[$f];
                $hasData = true;
            }
        }

        if ($hasData) {
            \App\Models\KeluargaSiswa::updateOrCreate(
                ['siswa_id' => $siswaId],
                $data
            );
        }
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
                'tempat_lahir'   => $normalized['tempat_lahir'] ?? null,
                'tgl_lahir'      => $normalized['tgl_lahir'] ?? null,
                'anak_ke'        => $normalized['anak_ke'] ?? null,
                'jml_saudara'    => $normalized['jml_saudara'] ?? null,
                'asal_smp'       => $normalized['asal_smp'] ?? null,
                'agama'          => $normalized['agama'] ?? null,
                'hobi'           => $normalized['hobi'] ?? null,
                'bakat'          => $normalized['bakat'] ?? null,
                'rencana_lulus'  => $normalized['rencana_lulus'] ?? null,
                'detail_rencana_lulus' => $normalized['detail_rencana_lulus'] ?? null,
            ];
        }

        return [$validRows, $errors];
    }
}