<?php

namespace App\Services;

use App\Models\Konsultasi;
use App\Models\KonsultasiLampiran;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use App\Repositories\Contracts\KonsultasiRepositoryInterface;
use Illuminate\Http\UploadedFile;

class KonsultasiService
{
    public function __construct(
        protected KonsultasiRepositoryInterface $konsultasiRepository
    ) {}

    public function getAll()
    {
        return $this->konsultasiRepository->getAll();
    }

    /**
     * Mengambil konsultasi milik guru BK yang sedang login.
     * Otomatis resolve pegawai dari auth user.
     */
    public function getByGurubk(?int $pegawaiId = null)
    {
        $id = $pegawaiId ?? $this->resolveGurubkId();

        return $this->konsultasiRepository->getByGurubk($id);
    }

    public function getTotalKonsultasi(): int
    {
        return $this->konsultasiRepository->countKonsultasi();
    }

    public function findById(int $id)
    {
        return $this->konsultasiRepository->findById($id);
    }

    /**
     * Cari konsultasi by ID dan pastikan kepemilikan.
     * Admin bisa mengakses semua, konselor hanya data miliknya.
     */
    public function findByIdForCurrentUser(int $id): Konsultasi
    {
        $konsultasi = $this->konsultasiRepository->findById($id);

        if (!$this->isAdmin()) {
            $this->ensureOwnership($konsultasi);
        }

        return $konsultasi;
    }

    /**
     * Hapus konsultasi dengan pengecekan kepemilikan terlebih dahulu.
     * Admin bisa menghapus semua, konselor hanya data miliknya.
     */
    public function deleteForCurrentUser(int $id): void
    {
        $konsultasi = $this->konsultasiRepository->findById($id);

        if (!$this->isAdmin()) {
            $this->ensureOwnership($konsultasi);
        }

        // Hapus semua file lampiran terlebih dahulu
        foreach ($konsultasi->lampirans as $lampiran) {
            \Storage::disk('public')->delete($lampiran->path_file);
        }

        $this->konsultasiRepository->delete($id);
    }

    /**
     * Buat konsultasi baru + simpan lampiran (jika ada).
     *
     * @param  array  $data         Field konsultasi
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

        $konsultasi = $this->konsultasiRepository->create($data);

        // Simpan lampiran ke tabel konsultasi_lampiran
        if (!empty($uploadedFiles)) {
            $this->saveLampirans($konsultasi->id, $uploadedFiles);
        }
    }

    /**
     * Update konsultasi + kelola lampiran.
     *
     * @param  array  $keptPaths     Path lama yang dipertahankan (array string)
     * @param  array  $newFiles      File baru dari Livewire
     */
    public function update(int $id, array $data, array $keptPaths = [], array $newFiles = []): void
    {
        // Verifikasi kepemilikan sebelum update (skip untuk admin)
        $konsultasi = $this->konsultasiRepository->findById($id);

        if (!$this->isAdmin()) {
            $this->ensureOwnership($konsultasi);
        }

        $this->konsultasiRepository->update($id, $data);

        // Hapus lampiran lama yang tidak dipertahankan
        foreach ($konsultasi->lampirans as $lampiran) {
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
        $konsultasi = $this->konsultasiRepository->findById($id);
        foreach ($konsultasi->lampirans as $lampiran) {
            \Storage::disk('public')->delete($lampiran->path_file);
        }

        $this->konsultasiRepository->delete($id);
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
        $pegawai = Pegawai::where('user_id', auth()->id())->first();

        if (!$pegawai) {
            abort(403, 'Akun ini tidak terdaftar sebagai pegawai/guru BK.');
        }

        return $pegawai->id;
    }

    /**
     * Pastikan konsultasi milik guru BK yang sedang login.
     */
    private function ensureOwnership(Konsultasi $konsultasi): void
    {
        $pegawaiId = $this->resolveGurubkId();
        if ($konsultasi->guru_bk_id !== $pegawaiId) {
            abort(403, 'Anda tidak memiliki akses ke data konsultasi ini.');
        }
    }

    /**
     * Simpan array UploadedFile ke storage dan insert ke konsultasi_lampiran.
     *
     * @param  int    $konsultasiId
     * @param  array  $files  Array of Livewire UploadedFile
     */
    private function saveLampirans(int $konsultasiId, array $files): void
    {
        foreach ($files as $file) {
            /** @var UploadedFile $file */
            $ext      = strtolower($file->getClientOriginalExtension());
            $isImage  = in_array($ext, ['jpg', 'jpeg', 'png']);
            $folder   = $isImage ? 'data/images' : 'data/documents';
            $path     = $file->store($folder, 'public');

            KonsultasiLampiran::create([
                'konsultasi_id' => $konsultasiId,
                'nama_file'     => $file->getClientOriginalName(),
                'path_file'     => $path,
                'tipe_file'     => $file->getClientMimeType(),
                'ukuran'        => $file->getSize(),
            ]);
        }
    }
}
