<?php

namespace App\Services\Bk;

use App\Models\KasusBk;
use App\Models\KonsultasiLampiran;
use App\Models\TahunAjaran;
use App\Repositories\Contracts\k\Bk\KasusBkRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class KasusBkService
{
    public function __construct(
        protected KasusBkRepositoryInterface $kasusBkRepository
    ) {}

    public function all(): Collection
    {
        return $this->kasusBkRepository->all();
    }

    /**
     * Mengambil kasus BK milik guru BK yang sedang login.
     * Otomatis resolve pegawai dari auth user.
     */
    public function getByGurubk(?int $pegawaiId = null): Collection
    {
        $id = $pegawaiId ?? $this->resolveGurubkId();
        return $this->kasusBkRepository->getByGuruBk($id);
    }

    public function countKasus(): int
    {
        return $this->kasusBkRepository->countKasus();
    }

    /**
     * Hitung jumlah kasus per tingkat kelas untuk guru BK tertentu.
     */
    public function getCaseCountsByGuruBk(int $pegawaiId): array
    {
        return [
            'kelas_10' => KasusBk::where('guru_bk_id', $pegawaiId)
                ->whereHas('siswa', fn($q) => $q->whereHas('kelas', fn($qk) => $qk->where('tingkat', 10)))
                ->distinct()->count('siswa_id'),
            'kelas_11' => KasusBk::where('guru_bk_id', $pegawaiId)
                ->whereHas('siswa', fn($q) => $q->whereHas('kelas', fn($qk) => $qk->where('tingkat', 11)))
                ->distinct()->count('siswa_id'),
            'kelas_12' => KasusBk::where('guru_bk_id', $pegawaiId)
                ->whereHas('siswa', fn($q) => $q->whereHas('kelas', fn($qk) => $qk->where('tingkat', 12)))
                ->distinct()->count('siswa_id'),
        ];
    }

    /**
     * Cari kasus BK berdasarkan keyword.
     */
    public function search(string $keyword, int $limit = 5): \Illuminate\Support\Collection
    {
        return KasusBk::with('siswa')
            ->where(function ($query) use ($keyword) {
                $query->whereHas('siswa.user', fn($q) => $q->where('nama', 'like', "%{$keyword}%"))
                    ->orWhere('penanganan', 'like', "%{$keyword}%");
            })
            ->take($limit)
            ->get();
    }

    /**
     * Ambil data terfilter berdasarkan filters array.
     * Supports: search, status, prioritas, kelas, jurusan, jenis_kelamin, guru_bk_id
     */
    public function getFiltered(array $filters = []): \Illuminate\Support\Collection
    {
        $query = KasusBk::with(['siswa.kelas.jurusan', 'kategori', 'lampirans', 'guruBk.user']);

        // Scope by guru BK jika ada
        if (!empty($filters['guru_bk_id'])) {
            $query->where('guru_bk_id', $filters['guru_bk_id']);
        }

        // Search
        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('siswa.user', fn($q2) => $q2->where('nama', 'like', "%{$keyword}%"))
                    ->orWhere('penanganan', 'like', "%{$keyword}%")
                    ->orWhere('uraian_masalah', 'like', "%{$keyword}%");
            });
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Prioritas filter
        if (!empty($filters['prioritas'])) {
            $query->where('prioritas', $filters['prioritas']);
        }

        // Kelas filter (computed attribute — perlu whereHas via siswa.kelas)
        if (!empty($filters['kelas'])) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas_id', function ($q2) use ($filters) {
                // Resolve kelas_label to kelas_id
                $q2->whereIn('id', \App\Models\Kelas::where('nama_kelas', $filters['kelas'])->pluck('id'));
            }));
        }

        // Jurusan filter (computed attribute — perlu whereHas via siswa.kelas.jurusan)
        if (!empty($filters['jurusan'])) {
            $query->whereHas('siswa.kelas.jurusan', fn($q) => $q->where('nama_jurusan', $filters['jurusan']));
        }

        // Jenis Kelamin filter
        if (!empty($filters['jenis_kelamin'])) {
            $query->whereHas('siswa', fn($q) => $q->where('jenis_kelamin', $filters['jenis_kelamin']));
        }

        return $query->latest()->get();
    }

    /**
     * Ambil opsi filter dari database.
     */
    public function getFilterOptions(): array
    {
        $all = $this->all();

        return [
            'statusOptions' => $all->pluck('status')->filter()->unique()->sort()->values()->toArray(),
            'prioritasOptions' => $all->pluck('prioritas')->filter()->unique()->sort()->values()->toArray(),
            'kelasOptions' => $all->pluck('siswa.kelas_label')->filter()->unique()->sort()->values()->toArray(),
            'jurusanOptions' => $all->pluck('siswa.jurusan_label')->filter()->unique()->map(fn($j) => (string) $j)->sort()->values()->toArray(),
            'jenisKelaminOptions' => $all->pluck('siswa.jenis_kelamin')->filter()->unique()->values()->toArray(),
        ];
    }

    public function findById(int $id): ?KasusBk
    {
        return $this->kasusBkRepository->findById($id);
    }

    /**
     * Cari kasus BK by ID dan pastikan kepemilikan.
     * Admin bisa mengakses semua, konselor hanya data miliknya.
     */
    public function findByIdForCurrentUser(int $id): KasusBk
    {
        $kasus = $this->kasusBkRepository->findById($id);

        if (!$this->isAdmin()) {
            $this->ensureOwnership($kasus);
        }

        return $kasus;
    }

    /**
     * Hapus kasus BK dengan pengecekan kepemilikan terlebih dahulu.
     * Admin bisa menghapus semua, konselor hanya data miliknya.
     */
    public function deleteForCurrentUser(int $id): void
    {
        $kasus = $this->kasusBkRepository->findById($id);

        if (!$this->isAdmin()) {
            $this->ensureOwnership($kasus);
        }

        // Hapus semua file lampiran terlebih dahulu
        foreach ($kasus->lampirans as $lampiran) {
            \Storage::disk('public')->delete($lampiran->path_file);
        }

        $this->kasusBkRepository->delete($id);
    }

    /**
     * Buat kasus BK baru + simpan lampiran (jika ada).
     *
     * @param  array  $data         Field kasus BK
     * @param  array  $uploadedFiles  Array UploadedFile dari Livewire
     */
    public function create(array $data, array $uploadedFiles = []): void
    {
        // Auto-set guru_bk_id dari pegawai user yang login
        $data['guru_bk_id'] = $data['guru_bk_id'] ?? $this->resolveGurubkId();

        // Auto-set tahun ajaran aktif
        $data['tahun_ajaran_id'] = $data['tahun_ajaran_id']
            ?? TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id');

        // Default status dan prioritas
        $data['status'] = $data['status'] ?? KasusBk::STATUS_OPEN;
        $data['prioritas'] = $data['prioritas'] ?? KasusBk::PRIORITAS_RENDAH;

        $kasus = $this->kasusBkRepository->create($data);

        // Simpan lampiran ke tabel konsultasi_lampiran
        if (!empty($uploadedFiles)) {
            $this->saveLampirans($kasus->id, $uploadedFiles);
        }
    }

    /**
     * Update kasus BK + kelola lampiran.
     *
     * @param  array  $keptPaths     Path lama yang dipertahankan (array string)
     * @param  array  $newFiles      File baru dari Livewire
     */
    public function update(int $id, array $data, array $keptPaths = [], array $newFiles = []): void
    {
        // Verifikasi kepemilikan sebelum update (skip untuk admin)
        $kasus = $this->kasusBkRepository->findById($id);

        if (!$this->isAdmin()) {
            $this->ensureOwnership($kasus);
        }

        $this->kasusBkRepository->update($id, $data);

        // Hapus lampiran lama yang tidak dipertahankan
        foreach ($kasus->lampirans as $lampiran) {
            if (!in_array($lampiran->path_file, $keptPaths)) {
                \Storage::disk('public')->delete($lampiran->path_file);
                $lampiran->delete();
            }
        }

        // Simpan lampiran baru
        if (!empty($newFiles)) {
            $this->saveLampirans($id, $newFiles);
        }
    }

    public function delete(int $id): void
    {
        // Hapus semua file lampiran terlebih dahulu
        $kasus = $this->kasusBkRepository->findById($id);
        foreach ($kasus->lampirans as $lampiran) {
            \Storage::disk('public')->delete($lampiran->path_file);
        }

        $this->kasusBkRepository->delete($id);
    }

    // ─────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────

    /**
     * Cek apakah user yang sedang login adalah admin.
     */
    private function isAdmin(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Resolve ID pegawai dari user yang sedang login.
     */
    private function resolveGurubkId(): int
    {
        $pegawai = app(PegawaiService::class)->getCurrentPegawai();

        if (!$pegawai) {
            throw new \App\Exceptions\AuthorizationException('mengakses data pegawai/guru BK');
        }

        return $pegawai->id;
    }

    /**
     * Pastikan kasus milik guru BK yang sedang login.
     */
    private function ensureOwnership(KasusBk $kasus): void
    {
        $pegawaiId = $this->resolveGurubkId();
        if ($kasus->guru_bk_id !== $pegawaiId) {
            throw new \App\Exceptions\AuthorizationException('mengakses data kasus ini');
        }
    }

    /**
     * Simpan array UploadedFile ke storage dan insert ke konsultasi_lampiran.
     *
     * @param  int    $kasusId
     * @param  array  $files  Array of Livewire UploadedFile
     */
    private function saveLampirans(int $kasusId, array $files): void
    {
        foreach ($files as $file) {
            /** @var UploadedFile $file */
            $ext      = strtolower($file->getClientOriginalExtension());
            $isImage  = in_array($ext, ['jpg', 'jpeg', 'png']);
            $folder   = $isImage ? 'data/images' : 'data/documents';
            $path     = $file->store($folder, 'public');

            KonsultasiLampiran::create([
                'kasus_id'  => $kasusId,
                'nama_file' => $file->getClientOriginalName(),
                'path_file' => $path,
                'tipe_file' => $file->getClientMimeType(),
                'ukuran'    => $file->getSize(),
            ]);
        }
    }
}