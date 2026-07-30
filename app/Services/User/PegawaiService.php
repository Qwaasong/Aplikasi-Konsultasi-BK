<?php

namespace App\Services\User;

use App\Models\Pegawai;
use App\Models\User;
use App\Repositories\Contracts\User\PegawaiRepositoryInterface;
use App\Services\ImportExportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PegawaiService
{
    public function __construct(
        protected PegawaiRepositoryInterface $pegawaiRepository,
        protected ImportExportService $importExportService,
    ) {}

    // ===========================
    // READ
    // ===========================

    public function getAll(): Collection
    {
        return $this->pegawaiRepository->getAll();
    }

    public function getTotalPegawai(): int
    {
        return $this->pegawaiRepository->countPegawai();
    }

    public function findById(int $id): Pegawai
    {
        return $this->pegawaiRepository->findById($id);
    }

    // ===========================
    // WRITE
    // ===========================

    public function create(array $data): Pegawai
    {
        $this->ensureNipUnique($data['nip']);

        $user = User::create([
            'nama'            => $data['nama'],
            'username'        => $data['username'] ?? strtolower(str_replace(' ', '', $data['nama'])),
            'email'           => $data['email'],
            'jenis_kelamin'   => $data['jenis_kelamin'],
            'no_hp'           => $data['no_hp'] ?? '-',
            'foto'            => '',
            'password'        => bcrypt('password'),
            'role'            => $data['role'] ?? 'pegawai',
            'status'          => $data['status'] ?? 'aktif',
        ]);

        return $this->pegawaiRepository->create([
            'user_id' => $user->id,
            'nip' => $data['nip'],
            'jabatan' => $data['jabatan'],
        ]);
    }

    public function update(int $id, array $data): Pegawai
    {
        $pegawai = $this->pegawaiRepository->findById($id);

        if ($pegawai->nip != $data['nip']) {
            $this->ensureNipUnique($data['nip']);
        }

        $pegawai->user->update([
            'nama'          => $data['nama'],
            'email'         => $data['email'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'no_hp'         => $data['no_hp'],
            'role'          => $data['role'],
            'status'        => $data['status'],
        ]);

        return $this->pegawaiRepository->update($id, [
            'nip'      => $data['nip'],
            'jabatan'  => $data['jabatan'],
        ]);
    }

    public function delete(int $id): void
    {
        $pegawai = $this->pegawaiRepository->findById($id);

        $pegawai->user()->delete();

        $this->pegawaiRepository->delete($id);
    }

    public function getFiltered(array $filters = []): Collection
    {
        $query = Pegawai::with('user');

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('user', fn($q2) => $q2->where('nama', 'like', "%{$keyword}%"))
                    ->orWhere('nip', 'like', "%{$keyword}%")
                    ->orWhere('jabatan', 'like', "%{$keyword}%");
            });
        }

        if (!empty($filters['jabatan'])) {
            $query->where('jabatan', $filters['jabatan']);
        }

        return $query->latest()->get();
    }

    public function getFilterOptions(): array
    {
        $all = $this->getAll();

        return [
            'jabatanOptions' => $all->pluck('jabatan')->filter()->unique()->sort()->values()->toArray(),
        ];
    }

    /**
     * Ambil pegawai untuk user yang sedang login.
     */
    public function getCurrentPegawai(): ?\App\Models\Pegawai
    {
        return $this->pegawaiRepository->findByUserId(auth()->id());
    }

    // ===========================
    // IMPORT / EXPORT
    // ===========================

    public function importFromFile(UploadedFile $file): array
    {
        $rows = $this->importExportService->parseUploadedFile($file);

        return $this->validateAndImport($rows);
    }

    public function exportRows(array $filters = []): array
    {
        return $this->getFiltered($filters)->map(fn (Pegawai $p) => [
            'nip' => $p->nip,
            'nama' => $p->user->nama ?? '',
            'email' => $p->user->email ?? '',
            'no_hp' => $p->user->no_hp ?? '',
            'jenis_kelamin' => $p->user->jenis_kelamin ?? '',
            'jabatan' => $p->jabatan,
            'role' => $p->user->role ?? 'pegawai',
            'status' => $p->user->status ?? 'aktif',
        ])->toArray();
    }

    public function getExportCount(array $filters = []): int
    {
        return $this->getFiltered($filters)->count();
    }

    public function getTemplateHeaders(): array
    {
        return ['nip', 'nama', 'email', 'no_hp', 'jenis_kelamin', 'jabatan', 'role', 'status'];
    }

    public function getTemplateSampleRows(): array
    {
        return [
            [
                'nip' => '1987654321',
                'nama' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'no_hp' => '08123456789',
                'jenis_kelamin' => 'P',
                'jabatan' => 'Guru BK',
                'role' => 'guru_bk',
                'status' => 'aktif',
            ],
        ];
    }

    // ===========================
    // PRIVATE
    // ===========================

    private function validateAndImport(array $rows): array
    {
        $imported = 0;
        $errors = [];

        $roleMap = [
            'admin' => 'admin',
            'guru_bk' => 'guru_bk',
            'konselor' => 'guru_bk',
            'guru bk' => 'guru_bk',
            'guru' => 'guru_bk',
            'wali kelas' => 'guru_bk',
            'wali_kelas' => 'guru_bk',
            'kepala sekolah' => 'admin',
            'kepala_sekolah' => 'admin',
            'staff tu' => 'admin',
            'staff_tu' => 'admin',
            'pegawai' => 'guru_bk',
            'siswa' => 'siswa',
        ];

        $jabatanAllowed = ['Guru BK', 'Wali Kelas', 'Guru', 'Kepala Sekolah', 'Staff TU'];

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2;

            $nip = trim((string) ($row['nip'] ?? ''));
            $nama = trim((string) ($row['nama'] ?? ''));
            $email = trim((string) ($row['email'] ?? ''));
            $jabatan = trim((string) ($row['jabatan'] ?? ''));
            $roleRaw = strtolower(trim((string) ($row['role'] ?? 'pegawai')));
            $jenisKelaminRaw = strtolower(trim((string) ($row['jenis_kelamin'] ?? '')));
            $noHp = trim((string) ($row['no_hp'] ?? '-'));
            $statusRaw = strtolower(trim((string) ($row['status'] ?? 'aktif')));

            if ($nip === '' || $nama === '' || $email === '' || $jabatan === '') {
                $errors[] = "Baris {$lineNumber}: kolom nip, nama, email, dan jabatan wajib diisi.";
                continue;
            }

            $jenisKelamin = match ($jenisKelaminRaw) {
                'l', 'lk', 'laki', 'laki-laki' => 'L',
                'p', 'pr', 'perempuan', 'wanita', 'w' => 'P',
                default => null,
            };
            if (!$jenisKelamin) {
                $errors[] = "Baris {$lineNumber}: jenis kelamin \"{$row['jenis_kelamin']}\" tidak valid.";
                continue;
            }

            $role = $roleMap[$roleRaw] ?? 'pegawai';

            $normalizedJabatan = match (strtolower($jabatan)) {
                'guru bk' => 'Guru BK',
                'wali kelas' => 'Wali Kelas',
                'guru' => 'Guru',
                'kepala sekolah' => 'Kepala Sekolah',
                'staff tu', 'staff_tu' => 'Staff TU',
                default => in_array($jabatan, $jabatanAllowed) ? $jabatan : 'Guru',
            };

            $status = in_array($statusRaw, ['aktif', 'nonaktif']) ? $statusRaw : 'aktif';

            DB::transaction(function () use ($nip, $nama, $email, $jenisKelamin, $noHp, $role, $status, $normalizedJabatan, $lineNumber, &$errors, &$imported) {
                $existingPegawai = Pegawai::where('nip', $nip)->first();

                if ($existingPegawai) {
                    $existingPegawai->user->update([
                        'nama' => $nama,
                        'email' => $email,
                        'jenis_kelamin' => $jenisKelamin,
                        'no_hp' => $noHp,
                        'role' => $role,
                        'status' => $status,
                    ]);
                    $existingPegawai->update([
                        'jabatan' => $normalizedJabatan,
                    ]);
                } else {
                    $user = User::create([
                        'nama' => $nama,
                        'username' => strtolower(str_replace(' ', '', $nama)) . '_' . substr($nip, -4),
                        'email' => $email,
                        'jenis_kelamin' => $jenisKelamin,
                        'no_hp' => $noHp,
                        'foto' => '',
                        'password' => bcrypt('password'),
                        'role' => $role,
                        'status' => $status,
                    ]);

                    Pegawai::create([
                        'user_id' => $user->id,
                        'nip' => $nip,
                        'jabatan' => $normalizedJabatan,
                    ]);
                }

                $imported++;
            });
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
        ];
    }

    // ===========================
    // PRIVATE
    // ===========================

    private function ensureNipUnique(string|int $nip): void
    {
        if (Pegawai::where('nip', $nip)->exists()) {
            throw ValidationException::withMessages([
                'nip' => "NIP {$nip} sudah digunakan.",
            ]);
        }
    }
}