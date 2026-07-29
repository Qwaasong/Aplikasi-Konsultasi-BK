<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\KehadiranService;
use Illuminate\Http\UploadedFile;

echo "Testing Kehadiran import with existing Siswa data...\n";

$testFile = __DIR__ . '/test-kehadiran.csv';
$tmpFile = tempnam(sys_get_temp_dir(), 'csv_test');
copy($testFile, $tmpFile);

$uploadedFile = new UploadedFile($tmpFile, 'test-kehadiran.csv', 'text/csv', null, true);

$service = app(KehadiranService::class);
$result = $service->importFromFile($uploadedFile);

echo "Imported: " . $result['imported'] . " rows\n";
echo "Errors: " . count($result['errors']) . "\n";
foreach ($result['errors'] as $err) {
    echo "  - " . $err . "\n";
}

unlink($tmpFile);

echo "\nTest completed!\n";
