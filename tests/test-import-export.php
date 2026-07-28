<?php

/**
 * Test script for Import/Export functionality
 * Run: php tests/test-import-export.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ImportExportService;
use App\Services\KehadiranService;
use App\Services\SiswaService;
use App\Services\PegawaiService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

echo "==========================================\n";
echo "TEST IMPORT/EXPORT SYSTEM\n";
echo "==========================================\n\n";

// ==========================================
// TEST 1: ImportExportService - Parse CSV
// ==========================================
echo "TEST 1: ImportExportService - Parse CSV\n";
echo "------------------------------------------\n";

$ies = app(ImportExportService::class);

$testCsvFile = __DIR__ . '/test-kehadiran.csv';
$tmpFile = tempnam(sys_get_temp_dir(), 'csv_test');
copy($testCsvFile, $tmpFile);

$uploadedFile = new UploadedFile(
    $tmpFile,
    'test-kehadiran.csv',
    'text/csv',
    null,
    true
);

try {
    $rows = $ies->parseUploadedFile($uploadedFile);
    echo "✓ CSV parsed successfully\n";
    echo "  Rows found: " . count($rows) . "\n";
    echo "  First row: " . json_encode($rows[0] ?? []) . "\n\n";
} catch (\Exception $e) {
    echo "✗ CSV parse failed: " . $e->getMessage() . "\n\n";
}

unlink($tmpFile);

// ==========================================
// TEST 2: ImportExportService - Normalize Headers
// ==========================================
echo "TEST 2: ImportExportService - Normalize Headers\n";
echo "------------------------------------------\n";

$headers = ['NIS', 'Nama Lengkap', 'Tanggal Lahir', 'Jenis Kelamin'];
$normalized = $ies->normalizeHeaders($headers);
echo "Input: " . implode(', ', $headers) . "\n";
echo "Output: " . implode(', ', $normalized) . "\n";
echo "✓ Headers normalized correctly\n\n";

// ==========================================
// TEST 3: ImportExportService - Normalize Date
// ==========================================
echo "TEST 3: ImportExportService - Normalize Date\n";
echo "------------------------------------------\n";

$dates = ['2026-07-28', '28/07/2026', '46224', null, ''];
foreach ($dates as $date) {
    $result = $ies->normalizeDate($date);
    echo "Input: " . var_export($date, true) . " → Output: " . var_export($result, true) . "\n";
}
echo "✓ Date normalization working\n\n";

// ==========================================
// TEST 4: KehadiranService - Template Headers
// ==========================================
echo "TEST 4: KehadiranService - Template Headers\n";
echo "------------------------------------------\n";

$kehadiranService = app(KehadiranService::class);
$headers = $kehadiranService->getTemplateHeaders();
echo "Headers: " . implode(', ', $headers) . "\n";

$sampleRows = $kehadiranService->getTemplateSampleRows();
echo "Sample rows: " . count($sampleRows) . "\n";
echo "First sample: " . json_encode($sampleRows[0] ?? []) . "\n";
echo "✓ Kehadiran template working\n\n";

// ==========================================
// TEST 5: SiswaService - Template Headers
// ==========================================
echo "TEST 5: SiswaService - Template Headers\n";
echo "------------------------------------------\n";

$siswaService = app(SiswaService::class);
$headers = $siswaService->getTemplateHeaders();
echo "Headers: " . implode(', ', $headers) . "\n";

$sampleRows = $siswaService->getTemplateSampleRows();
echo "Sample rows: " . count($sampleRows) . "\n";
echo "First sample: " . json_encode($sampleRows[0] ?? []) . "\n";
echo "✓ Siswa template working\n\n";

// ==========================================
// TEST 6: PegawaiService - Template Headers
// ==========================================
echo "TEST 6: PegawaiService - Template Headers\n";
echo "------------------------------------------\n";

$pegawaiService = app(PegawaiService::class);
$headers = $pegawaiService->getTemplateHeaders();
echo "Headers: " . implode(', ', $headers) . "\n";

$sampleRows = $pegawaiService->getTemplateSampleRows();
echo "Sample rows: " . count($sampleRows) . "\n";
echo "First sample: " . json_encode($sampleRows[0] ?? []) . "\n";
echo "✓ Pegawai template working\n\n";

// ==========================================
// TEST 7: KehadiranService - Import CSV
// ==========================================
echo "TEST 7: KehadiranService - Import CSV\n";
echo "------------------------------------------\n";

$testCsvFile = __DIR__ . '/test-kehadiran.csv';
$tmpFile = tempnam(sys_get_temp_dir(), 'csv_test');
copy($testCsvFile, $tmpFile);

$uploadedFile = new UploadedFile(
    $tmpFile,
    'test-kehadiran.csv',
    'text/csv',
    null,
    true
);

try {
    $result = $kehadiranService->importFromFile($uploadedFile);
    echo "Imported: " . $result['imported'] . " rows\n";
    echo "Errors: " . count($result['errors']) . "\n";
    if (!empty($result['errors'])) {
        foreach ($result['errors'] as $err) {
            echo "  - " . $err . "\n";
        }
    }
    echo "✓ Kehadiran import working\n\n";
} catch (\Exception $e) {
    echo "✗ Kehadiran import failed: " . $e->getMessage() . "\n\n";
}

unlink($tmpFile);

// ==========================================
// TEST 8: SiswaService - Import CSV
// ==========================================
echo "TEST 8: SiswaService - Import CSV\n";
echo "------------------------------------------\n";

$testCsvFile = __DIR__ . '/test-siswa.csv';
$tmpFile = tempnam(sys_get_temp_dir(), 'csv_test');
copy($testCsvFile, $tmpFile);

$uploadedFile = new UploadedFile(
    $tmpFile,
    'test-siswa.csv',
    'text/csv',
    null,
    true
);

try {
    $result = $siswaService->importFromFile($uploadedFile);
    echo "Imported: " . $result['imported'] . " rows\n";
    echo "Errors: " . count($result['errors']) . "\n";
    if (!empty($result['errors'])) {
        foreach ($result['errors'] as $err) {
            echo "  - " . $err . "\n";
        }
    }
    echo "✓ Siswa import working\n\n";
} catch (\Exception $e) {
    echo "✗ Siswa import failed: " . $e->getMessage() . "\n\n";
}

unlink($tmpFile);

// ==========================================
// TEST 9: PegawaiService - Import CSV
// ==========================================
echo "TEST 9: PegawaiService - Import CSV\n";
echo "------------------------------------------\n";

$testCsvFile = __DIR__ . '/test-pegawai.csv';
$tmpFile = tempnam(sys_get_temp_dir(), 'csv_test');
copy($testCsvFile, $tmpFile);

$uploadedFile = new UploadedFile(
    $tmpFile,
    'test-pegawai.csv',
    'text/csv',
    null,
    true
);

try {
    $result = $pegawaiService->importFromFile($uploadedFile);
    echo "Imported: " . $result['imported'] . " rows\n";
    echo "Errors: " . count($result['errors']) . "\n";
    if (!empty($result['errors'])) {
        foreach ($result['errors'] as $err) {
            echo "  - " . $err . "\n";
        }
    }
    echo "✓ Pegawai import working\n\n";
} catch (\Exception $e) {
    echo "✗ Pegawai import failed: " . $e->getMessage() . "\n\n";
}

unlink($tmpFile);

// ==========================================
// TEST 10: KehadiranService - Export Rows
// ==========================================
echo "TEST 10: KehadiranService - Export Rows\n";
echo "------------------------------------------\n";

try {
    $rows = $kehadiranService->exportRows();
    echo "Exported rows: " . count($rows) . "\n";
    if (!empty($rows)) {
        echo "First row: " . json_encode($rows[0]) . "\n";
    }
    echo "✓ Kehadiran export working\n\n";
} catch (\Exception $e) {
    echo "✗ Kehadiran export failed: " . $e->getMessage() . "\n\n";
}

// ==========================================
// TEST 11: SiswaService - Export Rows
// ==========================================
echo "TEST 11: SiswaService - Export Rows\n";
echo "------------------------------------------\n";

try {
    $rows = $siswaService->exportRows();
    echo "Exported rows: " . count($rows) . "\n";
    if (!empty($rows)) {
        echo "First row: " . json_encode($rows[0]) . "\n";
    }
    echo "✓ Siswa export working\n\n";
} catch (\Exception $e) {
    echo "✗ Siswa export failed: " . $e->getMessage() . "\n\n";
}

// ==========================================
// TEST 12: PegawaiService - Export Rows
// ==========================================
echo "TEST 12: PegawaiService - Export Rows\n";
echo "------------------------------------------\n";

try {
    $rows = $pegawaiService->exportRows();
    echo "Exported rows: " . count($rows) . "\n";
    if (!empty($rows)) {
        echo "First row: " . json_encode($rows[0]) . "\n";
    }
    echo "✓ Pegawai export working\n\n";
} catch (\Exception $e) {
    echo "✗ Pegawai export failed: " . $e->getMessage() . "\n\n";
}

// ==========================================
// TEST 13: ImportExportService - Stream CSV
// ==========================================
echo "TEST 13: ImportExportService - Stream CSV\n";
echo "------------------------------------------\n";

try {
    $headers = ['nis', 'nama', 'tanggal_kehadiran', 'status'];
    $rows = [
        ['nis' => '1234567890', 'nama' => 'Budi', 'tanggal_kehadiran' => '2026-07-28', 'status' => 'Hadir'],
    ];

    ob_start();
    $response = $ies->streamCsv('test.csv', $headers, $rows);
    $content = ob_get_clean();

    echo "Response type: " . get_class($response) . "\n";
    echo "✓ CSV stream working\n\n";
} catch (\Exception $e) {
    echo "✗ CSV stream failed: " . $e->getMessage() . "\n\n";
}

// ==========================================
// TEST 14: ImportExportService - Stream Excel Template
// ==========================================
echo "TEST 14: ImportExportService - Stream Excel Template\n";
echo "------------------------------------------\n";

try {
    $headers = ['nis', 'nama', 'kelas'];
    $sampleRows = [
        ['nis' => '1234567890', 'nama' => 'Budi Santoso', 'kelas' => '10'],
    ];

    ob_start();
    $response = $ies->streamExcelTemplate('template-test.xlsx', $headers, $sampleRows);
    $content = ob_get_clean();

    echo "Response type: " . get_class($response) . "\n";
    echo "✓ Excel template stream working\n\n";
} catch (\Exception $e) {
    echo "✗ Excel template stream failed: " . $e->getMessage() . "\n\n";
}

// ==========================================
// TEST 15: ImportExportService - Stream Excel Export
// ==========================================
echo "TEST 15: ImportExportService - Stream Excel Export\n";
echo "------------------------------------------\n";

try {
    $headers = ['nis', 'nama', 'status'];
    $rows = [
        ['nis' => '1234567890', 'nama' => 'Budi', 'status' => 'Hadir'],
    ];

    ob_start();
    $response = $ies->streamExcelExport('export-test.xlsx', $headers, $rows);
    $content = ob_get_clean();

    echo "Response type: " . get_class($response) . "\n";
    echo "✓ Excel export stream working\n\n";
} catch (\Exception $e) {
    echo "✗ Excel export stream failed: " . $e->getMessage() . "\n\n";
}

// ==========================================
// SUMMARY
// ==========================================
echo "==========================================\n";
echo "ALL TESTS COMPLETED\n";
echo "==========================================\n";
