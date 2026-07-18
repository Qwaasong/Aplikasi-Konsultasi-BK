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

    public function export(int $id, string $template): BinaryFileResponse|Response
    {
        try {
            $tempPath = $this->wordExportService->generateDocument($id, $template);
            $filename = "kasus-bk-{$id}-{$template}.docx";
            return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
        } catch (\InvalidArgumentException $e) {
            return response()->view('errors.404', ['message' => $e->getMessage()], 404);
        } catch (\RuntimeException $e) {
            Log::error('Word export failed', ['kasus_id' => $id, 'template' => $template, 'error' => $e->getMessage()]);
            return response()->view('errors.500', ['message' => 'Gagal membuat dokumen Word.'], 500);
        }
    }
}
