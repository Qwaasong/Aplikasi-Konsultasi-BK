<?php

namespace App\Services\Siswa;

use App\Models\DataSiswa;
use App\Models\KeluargaSiswa;
use App\Repositories\Contracts\Siswa\SiswaRepositoryInterface;
use App\Services\Asesmen\AsesmenImportHelper;
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

    public function getAll(): Collection
    {
        return $this->siswaRepository->getAll();
    }

    public function getTotalSiswa()
    {
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

    public function create(array $data): DataSiswa
    {
        $this->ensureNisUnique($data['nis']);

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

        return $this->siswaRepository->create([
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
        ]);
    }

    public function update(int $id, array $data): DataSiswa
    {
        $existing = $this->siswaRepository->findById($id);

        if (isset($data['nis']) && (int) $data['nis'] !== (int) $existing->nis) {
            $this->ensureNisUnique($data['nis']);
        }

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

        return $this->siswaRepository->update($id, [
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
        ]);
    }

    public function delete(int $id): void
    {
        $this->siswaRepository->delete($id);
    }

    public function getFiltered(array $filters = []): Collection
    {
        $query = DataSiswa::with(['user', 'kelas.jurusan.sekolah']);

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

    public function importFromFile(UploadedFile $file): array
    {
        $rows = $this->importExportService->parseUploadedFile($file);
        [$validRows, $errors] = $this->validateImportRows($rows);
        $imported = 0;

        foreach ($validRows as $row) {
            $this->importSingleRow($row);
            $imported++;
        }

        return compact('imported', 'errors');
    }

    public function exportToCsv(array $filters = []): string
    {
        $headers = ['NIS', 'Nama', 'Kelas', 'Jenis Kelamin', 'Jurusan', 'Periode Ajaran'];
        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);

        foreach ($this->getFiltered($filters) as $siswa) {
            fputcsv($output, [$siswa->nis, $siswa->nama, $siswa->kelas_label, $siswa->jenis_kelamin, $siswa->kelas?->jurusan?->nama_jurusan ?? '', $siswa->periode_ajaran ?? '']);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    public function getTemplateHeaders(): array
    {
        return [
            'nis', 'nama', 'email', 'no_hp', 'jenis_kelamin', 'status', 'kelas', 'jurusan', 'periode_ajaran', 'alamat',
            'tempat_lahir', 'tgl_lahir', 'anak_ke', 'jml_saudara', 'asal_smp', 'agama', 'hobi', 'bakat',
            'rencana_lulus', 'detail_rencana_lulus', 'nama_ayah', 'nama_ibu', 'pendidikan_ayah', 'pendidikan_ibu',
            'pekerjaan_ayah', 'pekerjaan_ibu', 'telp_ortu', 'status_rumah', 'dinding_rumah', 'lantai_rumah',
            'jml_kamar', 'punya_kamar_sendiri', 'jml_tv', 'kendaraan_mobil', 'kendaraan_motor',
            'biaya_sekolah_dari', 'kendaraan_ke_sekolah',
        ];
    }

    public function getTemplateSampleRows(): array
    {
        return [[
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
        ]];
    }

    public function exportRows(array $filters = []): array
    {
        return $this->getFiltered($filters)->map(function ($siswa) {
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
                'nama_ayah' => $k?->nama_ayah ?? '',
                'nama_ibu' => $k?->nama_ibu ?? '',
                'pendidikan_ayah' => $k?->pendidikan_ayah ?? '',
                'pendidikan_ibu' => $k?->pendidikan_ibu ?? '',
                'pekerjaan_ayah' => $k?->pekerjaan_ayah ?? '',
                'pekerjaan_ibu' => $k?->pekerjaan_ibu ?? '',
                'telp_ortu' => $k?->telp_ortu ?? '',
                'status_rumah' => $k?->status_rumah ?? '',
                'kendaraan_ke_sekolah' => $k?->kendaraan_ke_sekolah ?? '',
            ];
        })->toArray();
    }

    public function getExportCount(array $filters = []): int
    {
        return $this->getFiltered($filters)->count();
    }

    public function importGformFromFile(UploadedFile $file): array
    {
        $rows = array_map(fn ($row) => $this->normalizeGformKeys($row), $this->importExportService->parseUploadedFile($file));
        $imported = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2;
            $nama = trim((string) ($row['nama_lengkap'] ?? $row['nama_siswa'] ?? $row['nama'] ?? ''));
            $kelas = trim((string) ($row['kelas'] ?? ''));

            if ($nama === '' || $kelas === '') {
                $errors[] = "Baris {$lineNumber}: kolom Nama dan Kelas wajib diisi.";
                continue;
            }

            [$tempatLahir, $tglLahir] = AsesmenImportHelper::splitTempatTanggalLahir($row['tempat_tanggal_lahir'] ?? '');
            $siswa = AsesmenImportHelper::resolveSiswa($nama, $kelas);

            if (! $siswa) {
                $errors[] = "Baris {$lineNumber}: siswa \"{$nama}\" tidak bisa diproses.";
                continue;
            }

            $siswa->user?->update([
                'nama' => $nama,
                'jenis_kelamin' => AsesmenImportHelper::normalizeJenisKelamin($row['jenis_kelamin'] ?? ''),
                'foto' => trim((string) ($row['foto_diri_selfie'] ?? '')) ?: $siswa->user?->foto,
            ]);

            $siswa->update([
                'kelas_id' => AsesmenImportHelper::resolveKelasId($kelas),
                'alamat' => $this->clean($row['alamat_rumah_rt_rw'] ?? $row['alamat_rumah'] ?? '') ?? '',
                'tempat_lahir' => $tempatLahir,
                'tgl_lahir' => $tglLahir,
                'asal_smp' => $this->clean($row['asal_smp'] ?? ''),
                'agama' => $this->clean($row['agama'] ?? ''),
            ]);

            KeluargaSiswa::updateOrCreate(['siswa_id' => $siswa->id], [
                'siswa_id' => $siswa->id,
                'tahun_pelajaran' => AsesmenImportHelper::resolveTahunPelajaran($row['tahun_pelajaran'] ?? null),
                'nama_ayah' => $this->clean($row['nama_lengkap_ayah_wali'] ?? ''),
                'nama_ibu' => $this->clean($row['nama_lengkap_ibu_wali'] ?? ''),
                'pendidikan_ayah' => $this->clean($row['pendidikan_terakhir_ayah_wali'] ?? ''),
                'pendidikan_ibu' => $this->clean($row['pendidikan_terakhir_ibu_wali'] ?? ''),
                'pekerjaan_ayah' => $this->clean($row['pekerjaan_ayah_wali'] ?? ''),
                'pekerjaan_ibu' => $this->clean($row['pekerjaan_ibu_wali'] ?? ''),
                'nomor_wa_ayah' => $this->clean($row['nomor_wa_ayah_wali'] ?? ''),
                'nomor_wa_ibu' => $this->clean($row['nomor_wa_ibu_wali'] ?? ''),
                'alamat_ayah' => $this->clean($row['alamat_rumah_ayah_wali'] ?? ''),
                'alamat_ibu' => $this->clean($row['alamat_rumah_ibu_wali'] ?? ''),
                'status_rumah' => $this->clean($row['status_rumah'] ?? ''),
                'lokasi_rumah' => $this->clean($row['lokasi_rumah'] ?? ''),
                'punya_kamar_sendiri' => $this->toBool($row['punya_kamar_sendiri'] ?? ''),
                'kendaraan_ke_sekolah' => $this->clean($row['transportasi_ke_sekolah'] ?? ''),
                'media_sosial' => $this->clean($row['akun_media_sosial'] ?? ''),
            ]);

            $imported++;
        }

        return compact('imported', 'errors');
    }

    public function getGformTemplateHeaders(): array
    {
        return [
            'Timestamp', 'KELAS', 'TAHUN PELAJARAN', 'NAMA LENGKAP', 'JENIS KELAMIN', 'TEMPAT, TANGGAL LAHIR',
            'ASAL SMP', 'AGAMA', 'ALAMAT RUMAH (RT, RW)', 'FOTO DIRI / SELFIE', 'NAMA LENGKAP AYAH / WALI',
            'PENDIDIKAN TERAKHIR AYAH / WALI', 'PEKERJAAN AYAH / WALI', 'NOMOR WA AYAH / WALI',
            'ALAMAT RUMAH  AYAH / WALI', 'NAMA LENGKAP IBU / WALI', 'PENDIDIKAN TERAKHIR IBU / WALI',
            'PEKERJAAN IBU / WALI', 'NOMOR WA IBU / WALI', 'ALAMAT RUMAH IBU / WALI', 'STATUS RUMAH',
            'LOKASI RUMAH', 'PUNYA KAMAR SENDIRI', 'TRANSPORTASI KE SEKOLAH', 'AKUN MEDIA SOSIAL',
        ];
    }

    public function exportGformRows(): array
    {
        return $this->siswaRepository->getAll()->map(function (DataSiswa $s) {
            $k = $s->keluarga;

            return [
                'Timestamp' => $s->created_at?->format('d/m/Y H:i:s') ?? '',
                'KELAS' => $s->kelas_label,
                'TAHUN PELAJARAN' => $k?->tahun_pelajaran ?? '',
                'NAMA LENGKAP' => $s->nama,
                'JENIS KELAMIN' => $s->jenis_kelamin === 'P' ? 'PEREMPUAN' : 'LAKI-LAKI',
                'TEMPAT, TANGGAL LAHIR' => trim(($s->tempat_lahir ?? '').', '.($s->tgl_lahir?->format('d-m-Y') ?? '')),
                'ASAL SMP' => (string) ($s->asal_smp ?? ''),
                'AGAMA' => (string) ($s->agama ?? ''),
                'ALAMAT RUMAH (RT, RW)' => (string) ($s->alamat ?? ''),
                'FOTO DIRI / SELFIE' => (string) ($s->user?->foto ?? ''),
                'NAMA LENGKAP AYAH / WALI' => (string) ($k?->nama_ayah ?? ''),
                'PENDIDIKAN TERAKHIR AYAH / WALI' => (string) ($k?->pendidikan_ayah ?? ''),
                'PEKERJAAN AYAH / WALI' => (string) ($k?->pekerjaan_ayah ?? ''),
                'NOMOR WA AYAH / WALI' => (string) ($k?->nomor_wa_ayah ?? ''),
                'ALAMAT RUMAH  AYAH / WALI' => (string) ($k?->alamat_ayah ?? ''),
                'NAMA LENGKAP IBU / WALI' => (string) ($k?->nama_ibu ?? ''),
                'PENDIDIKAN TERAKHIR IBU / WALI' => (string) ($k?->pendidikan_ibu ?? ''),
                'PEKERJAAN IBU / WALI' => (string) ($k?->pekerjaan_ibu ?? ''),
                'NOMOR WA IBU / WALI' => (string) ($k?->nomor_wa_ibu ?? ''),
                'ALAMAT RUMAH IBU / WALI' => (string) ($k?->alamat_ibu ?? ''),
                'STATUS RUMAH' => (string) ($k?->status_rumah ?? ''),
                'LOKASI RUMAH' => (string) ($k?->lokasi_rumah ?? ''),
                'PUNYA KAMAR SENDIRI' => $k?->punya_kamar_sendiri ? 'YA' : 'TIDAK',
                'TRANSPORTASI KE SEKOLAH' => (string) ($k?->kendaraan_ke_sekolah ?? ''),
                'AKUN MEDIA SOSIAL' => (string) ($k?->media_sosial ?? ''),
            ];
        })->values()->toArray();
    }

    public function getGformExportCount(): int
    {
        return $this->siswaRepository->getAll()->count();
    }

    private function importSingleRow(array $row): void
    {
        $existing = $this->siswaRepository->findByNis($row['nis']);
        $kelasId = $this->resolveKelasId($row['kelas'], $row['jurusan']);
        $row['kelas'] = $kelasId;
        $row['kelas_id'] = $kelasId;

        if ($existing) {
            $this->update($existing->id, $row);
            return;
        }

        $this->create($row);
    }

    private function resolveKelasId(int|string $tingkat, string $jurusanCode): int
    {
        $tingkatMap = [10 => 'X', 11 => 'XI', 12 => 'XII', 'X' => 'X', 'XI' => 'XI', 'XII' => 'XII'];
        $jurusan = \App\Models\Jurusan::where('kode_jurusan', $jurusanCode)->orWhere('nama_jurusan', 'like', "%{$jurusanCode}%")->first();
        $query = \App\Models\Kelas::where('tingkat', $tingkatMap[$tingkat] ?? $tingkat);

        if ($jurusan) {
            $query->where('jurusan_id', $jurusan->id);
        }

        return $query->first()?->id ?? 0;
    }

    private function ensureNisUnique(int|string $nis): void
    {
        if ($this->siswaRepository->findByNis((int) $nis)) {
            throw ValidationException::withMessages(['nis' => 'NIS ' . $nis . ' sudah digunakan oleh siswa lain.']);
        }
    }

    private function validateImportRows(array $rows): array
    {
        $validRows = [];
        $errors = [];
        $required = ['nis', 'nama', 'kelas', 'jenis_kelamin', 'jurusan', 'periode_ajaran'];

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2;
            $missing = array_filter($required, fn (string $col) => !isset($row[$col]) || trim((string) $row[$col]) === '');

            if ($missing) {
                $errors[] = "Baris {$lineNumber}: Kolom " . implode(', ', $missing) . ' tidak boleh kosong.';
                continue;
            }

            $validRows[] = [
                'nis' => (int) $row['nis'],
                'nama' => trim((string) $row['nama']),
                'kelas' => (int) $row['kelas'],
                'jenis_kelamin' => AsesmenImportHelper::normalizeJenisKelamin($row['jenis_kelamin']),
                'jurusan' => strtoupper(trim((string) $row['jurusan'])),
                'periode_ajaran' => trim((string) $row['periode_ajaran']),
            ];
        }

        return [$validRows, $errors];
    }

    private function normalizeGformKeys(array $row): array
    {
        $out = [];

        foreach ($row as $key => $value) {
            $key = strtolower(trim((string) $key));
            $key = str_replace([',', '/', '(', ')'], '_', $key);
            $key = preg_replace('/_+/', '_', $key);
            $out[trim($key, '_')] = $value;
        }

        return $out;
    }

    private function clean(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    private function toBool(mixed $value): bool
    {
        return in_array(strtolower(trim((string) ($value ?? ''))), ['ya', 'y', '1', 'true', 'ada'], true);
    }
}
