<?php

namespace App\Services\Bimbingan;

use App\Models\KonsultasiLampiran;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LampiranService
{
    /**
     * Simpan file lampiran dan buat record di tabel konsultasi_lampiran.
     *
     * @param int $kasusId ID dari kasus_bk yang akan dikaitkan
     * @param UploadedFile[] $files Array file yang diupload
     * @param string $subFolder Sub-folder di dalam uploads/ (misal: konferensi, kunjungan)
     */
    public function storeLampirans(int $kasusId, array $files, string $subFolder = 'lampiran'): void
    {
        foreach ($files as $file) {
            $path = $file->store("uploads/{$subFolder}", 'public');

            KonsultasiLampiran::create([
                'kasus_id'  => $kasusId,
                'nama_file' => $file->getClientOriginalName(),
                'path_file' => $path,
                'tipe_file' => $file->getClientOriginalExtension(),
                'ukuran'    => $file->getSize(),
            ]);
        }
    }

    /**
     * Hapus lampiran berdasarkan ID.
     */
    public function deleteLampiran(int $id): void
    {
        $lampiran = KonsultasiLampiran::find($id);
        if ($lampiran) {
            Storage::disk('public')->delete($lampiran->path_file);
            $lampiran->delete();
        }
    }

    /**
     * Hapus beberapa lampiran sekaligus.
     */
    public function deleteMultiple(array $ids): void
    {
        foreach ($ids as $id) {
            $this->deleteLampiran($id);
        }
    }
}
