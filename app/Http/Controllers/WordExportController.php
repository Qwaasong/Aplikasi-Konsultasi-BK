<?php

namespace App\Http\Controllers;

use App\Services\WordExportService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WordExportController extends Controller
{
    public function __construct(
        protected WordExportService $wordExportService
    ) {}

    /**
     * Export konsultasi ke dokumen Word berdasarkan template yang dipilih.
     *
     * @param int    $id       ID konsultasi
     * @param string $template Nama template (slug)
     */
    public function export(int $id, string $template): BinaryFileResponse|Response
    {
        try {
            $tempPath = $this->wordExportService->generateDocument($id, $template);

            // Format nama file download
            $filename = "konsultasi-{$id}-{$template}.docx";

            return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
        } catch (\InvalidArgumentException $e) {
            return response()->view('errors.404', [
                'message' => $e->getMessage(),
            ], 404);
        } catch (\RuntimeException $e) {
            Log::error('Word export failed', [
                'konsultasi_id' => $id,
                'template'      => $template,
                'error'         => $e->getMessage(),
            ]);

            return response()->view('errors.500', [
                'message' => 'Gagal membuat dokumen Word. Silakan coba lagi.',
            ], 500);
        }
    }
}
