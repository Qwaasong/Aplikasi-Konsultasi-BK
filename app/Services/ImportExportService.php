<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportService
{
    public function parseUploadedFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'csv' => $this->parseCsv($file),
            'xlsx', 'xls' => $this->excelToCsvRows($file),
            default => throw new \InvalidArgumentException('Format file tidak didukung. Gunakan CSV, XLS, atau XLSX.'),
        };
    }

    public function excelToCsvRows(UploadedFile $file): array
    {
        if (!class_exists(IOFactory::class)) {
            throw new \RuntimeException('Library PhpSpreadsheet tidak ditemukan. Jalankan: composer require phpoffice/phpspreadsheet');
        }

        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('Ekstensi PHP "zip" tidak aktif. Aktifkan extension=zip di php.ini lalu restart server.');
        }

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        return $this->rowsToAssociativeArray($rows);
    }

    public function normalizeHeaders(array $headers): array
    {
        return array_map(function ($header) {
            $header = strtolower(trim((string) $header));
            $header = str_replace([' ', '-', '.'], '_', $header);
            $header = preg_replace('/_+/', '_', $header);

            return trim($header, '_');
        }, $headers);
    }

    public function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable) {
                // Continue to string parsing fallback.
            }
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    public function streamCsv(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $this->mapRowToHeaders($row, $headers));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function streamExcelTemplate(string $filename, array $headers, array $sampleRows = []): StreamedResponse
    {
        return $this->streamExcel($filename, $headers, $sampleRows);
    }

    public function streamExcelExport(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return $this->streamExcel($filename, $headers, $rows);
    }

    private function parseCsv(UploadedFile $file): array
    {
        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');
        $headers = null;

        while (($line = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                $headers = $this->normalizeHeaders($line);
                continue;
            }

            if ($this->isEmptyRow($line)) {
                continue;
            }

            $line = array_pad($line, count($headers), null);
            $rows[] = array_combine($headers, array_slice($line, 0, count($headers)));
        }

        fclose($handle);

        return $rows;
    }

    private function rowsToAssociativeArray(array $rows): array
    {
        if (empty($rows)) {
            return [];
        }

        $rawHeaders = array_shift($rows);
        $headers = $this->normalizeHeaders(array_values($rawHeaders));
        $result = [];

        foreach ($rows as $row) {
            $values = array_values($row);

            if ($this->isEmptyRow($values)) {
                continue;
            }

            $values = array_pad($values, count($headers), null);
            $result[] = array_combine($headers, array_slice($values, 0, count($headers)));
        }

        return $result;
    }

    private function streamExcel(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            foreach ($headers as $index => $header) {
                $column = Coordinate::stringFromColumnIndex($index + 1);
                $sheet->setCellValue("{$column}1", $header);
                $sheet->getStyle("{$column}1")->getFont()->setBold(true);
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $rowNumber = 2;
            foreach ($rows as $row) {
                foreach ($this->mapRowToHeaders($row, $headers) as $index => $value) {
                    $column = Coordinate::stringFromColumnIndex($index + 1);
                    $sheet->setCellValue("{$column}{$rowNumber}", $value);
                }
                $rowNumber++;
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function mapRowToHeaders(array|object $row, array $headers): array
    {
        $row = (array) $row;

        return array_map(fn ($header) => $row[$header] ?? '', $headers);
    }

    private function isEmptyRow(array $row): bool
    {
        return collect($row)->filter(fn ($value) => trim((string) $value) !== '')->isEmpty();
    }
}
