<?php

namespace App\Services;

use App\Models\KasusBk;
use App\Repositories\Contracts\e\KasusBkRepositoryInterface;

class WordExportService
{
    private const VALID_TEMPLATES = [
        'form-penanganan-siswa',
        'komulatif-record',
        'lembar-sosiometri',
    ];

    public function __construct(
        protected KasusBkRepositoryInterface $kasusBkRepository
    ) {}

    public function generateDocument(int $kasusId, string $templateName): string
    {
        $this->validateTemplate($templateName);
        $templatePath = $this->getTemplatePath($templateName);
        $this->ensureTemplateExists($templatePath);

        $kasus = $this->kasusBkRepository->findById($kasusId);
        $data = $this->buildTemplateData($kasus);

        $tempPath = $this->createTempCopy($templatePath, $kasusId);
        $this->injectDataToXml($tempPath, $templateName, $data);

        return $tempPath;
    }

    private function buildTemplateData(KasusBk $kasus): array
    {
        return [
            'nama_siswa'         => $kasus->siswa->nama ?? '-',
            'nis'                => (string) ($kasus->siswa->nis ?? '-'),
            'kelas'              => $kasus->siswa->kelas_label ?? '-',
            'jurusan'            => $kasus->siswa->jurusan_label ?? '-',
            'alamat_siswa'       => $kasus->siswa->alamat ?? '-',
            'jenis_kelamin'      => $kasus->siswa->jenis_kelamin ?? '-',
            'judul'              => $kasus->penanganan ?? '-',
            'prioritas'          => $kasus->prioritas ?? '-',
            'tanggal_mulai'      => $kasus->tanggal_mulai
                ? \Carbon\Carbon::parse($kasus->tanggal_mulai)->locale('id')->translatedFormat('l, d F Y')
                : '-',
            'tanggal_hari'       => $kasus->tanggal_mulai
                ? \Carbon\Carbon::parse($kasus->tanggal_mulai)->locale('id')->translatedFormat('l')
                : '-',
            'tanggal_tgl'        => $kasus->tanggal_mulai
                ? \Carbon\Carbon::parse($kasus->tanggal_mulai)->format('d/m/Y')
                : '-',
            'deksripsi'          => $kasus->uraian_masalah ?? '-',
            'hasil_akhir'        => $kasus->tindak_lanjut ?? '-',
            'status'             => $kasus->status ?? '-',
            'nama_konselor'      => $kasus->guruBk->user->nama ?? '-',
            'nip_konselor'       => $kasus->guruBk->nip ?? '-',
            'nama_kategori'      => $kasus->kategori->nama_kategori ?? '-',
            'tahun_ajaran'       => $kasus->tahunAjaran->tahun ?? '-',
            'semester'           => $kasus->tahunAjaran->semester ?? '-',
            'tanggal_cetak'      => now()->locale('id')->translatedFormat('d F Y'),
        ];
    }

    private function injectDataToXml(string $tempPath, string $templateName, array $data): void
    {
        $fieldMap = $this->getFieldMap($templateName);
        $zip = new \ZipArchive();
        if ($zip->open($tempPath) !== true) {
            throw new \RuntimeException('Gagal membuka file template temporary.');
        }
        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();
            throw new \RuntimeException('Gagal membaca document.xml dari template.');
        }
        foreach ($fieldMap as $label => $variable) {
            if (!isset($data[$variable])) continue;
            $value = htmlspecialchars($data[$variable], ENT_XML1, 'UTF-8');
            $escapedLabel = preg_quote($label, '/');
            $labelPattern = '/<w:t(?:\s[^>]*)?>' . $escapedLabel . '<\/w:t>/';
            if (!preg_match($labelPattern, $xml, $m, PREG_OFFSET_CAPTURE)) continue;
            $labelEnd = $m[0][1] + strlen($m[0][0]);
            $afterLabel = substr($xml, $labelEnd);
            if (preg_match('/<w:t(?:\s[^>]*)?>([^<]*)<\/w:t>/', $afterLabel, $tm, PREG_OFFSET_CAPTURE)) {
                $matchStart = $labelEnd + $tm[0][1];
                $matchLen = strlen($tm[0][0]);
                $oldContent = $tm[1][0];
                $newContent = str_contains($oldContent, ':') ? ': ' . $value : $value;
                if (preg_match('/<w:t([^>]*)>/', $tm[0][0], $attrMatch)) {
                    $newTag = '<w:t' . $attrMatch[1] . '>' . $newContent . '</w:t>';
                    $xml = substr($xml, 0, $matchStart) . $newTag . substr($xml, $matchStart + $matchLen);
                }
            }
        }
        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    private function getFieldMap(string $templateName): array
    {
        return match ($templateName) {
            'form-penanganan-siswa' => [
                'NAMA' => 'nama_siswa', 'KELAS' => 'kelas',
                'TAHUN AJARAN' => 'tahun_ajaran', 'ALAMAT' => 'alamat_siswa',
                'NIP.' => 'nip_konselor',
            ],
            'komulatif-record' => [
                'No. Induk' => 'nis', 'Nama Siswa' => 'nama_siswa',
                'Alamat Rumah' => 'alamat_siswa',
            ],
            'lembar-sosiometri' => [
                'Nama' => 'nama_siswa', 'Kelas ' => 'kelas',
            ],
            default => [],
        };
    }

    private function validateTemplate(string $templateName): void
    {
        if (!in_array($templateName, self::VALID_TEMPLATES, true)) {
            throw new \InvalidArgumentException("Template '{$templateName}' tidak valid.");
        }
    }

    private function getTemplatePath(string $templateName): string
    {
        return resource_path("templates/{$templateName}.docx");
    }

    private function ensureTemplateExists(string $path): void
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("File template tidak ditemukan: {$path}");
        }
    }

    private function createTempCopy(string $templatePath, int $kasusId): string
    {
        $tempDir = sys_get_temp_dir();
        $filename = "kasus-bk-{$kasusId}-" . time() . ".docx";
        $tempPath = $tempDir . DIRECTORY_SEPARATOR . $filename;
        if (!copy($templatePath, $tempPath)) {
            throw new \RuntimeException("Gagal membuat salinan template.");
        }
        return $tempPath;
    }
}
