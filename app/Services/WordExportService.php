<?php

namespace App\Services;

use App\Models\Konsultasi;
use App\Repositories\Contracts\KonsultasiRepositoryInterface;

class WordExportService
{
    private const VALID_TEMPLATES = [
        'form-penanganan-siswa',
        'komulatif-record',
        'lembar-sosiometri',
    ];

    public function __construct(
        protected KonsultasiRepositoryInterface $konsultasiRepository
    ) {}

    public function generateDocument(int $konsultasiId, string $templateName): string
    {
        $this->validateTemplate($templateName);

        $templatePath = $this->getTemplatePath($templateName);
        $this->ensureTemplateExists($templatePath);

        $konsultasi = $this->konsultasiRepository->findById($konsultasiId);
        $data = $this->buildTemplateData($konsultasi);

        $tempPath = $this->createTempCopy($templatePath, $konsultasiId);
        $this->injectDataToXml($tempPath, $templateName, $data);

        return $tempPath;
    }

    public function getAvailableTemplates(): array
    {
        $templates = [];
        foreach (self::VALID_TEMPLATES as $slug) {
            $path = $this->getTemplatePath($slug);
            if (file_exists($path)) {
                $templates[] = [
                    'slug' => $slug,
                    'name' => str_replace('-', ' ', ucfirst($slug)),
                ];
            }
        }
        return $templates;
    }

    // ─────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────

    private function buildTemplateData(Konsultasi $konsultasi): array
    {
        return [
            'nama_siswa'         => $konsultasi->siswa->nama ?? '-',
            'nis'                => (string) ($konsultasi->siswa->nis ?? '-'),
            'kelas'              => $konsultasi->siswa->kelas_label ?? '-',
            'jurusan'            => $konsultasi->siswa->jurusan_label ?? '-',
            'alamat_siswa'       => $konsultasi->siswa->alamat ?? '-',
            'jenis_kelamin'      => $konsultasi->siswa->jenis_kelamin ?? '-',
            'judul'              => $konsultasi->judul ?? '-',
            'jenis_layanan'      => $konsultasi->jenis_layanan ?? '-',
            'tanggal_konsultasi' => $konsultasi->tanggal_konsultasi
                ? \Carbon\Carbon::parse($konsultasi->tanggal_konsultasi)->locale('id')->translatedFormat('l, d F Y')
                : '-',
            'tanggal_hari'       => $konsultasi->tanggal_konsultasi
                ? \Carbon\Carbon::parse($konsultasi->tanggal_konsultasi)->locale('id')->translatedFormat('l')
                : '-',
            'tanggal_tgl'        => $konsultasi->tanggal_konsultasi
                ? \Carbon\Carbon::parse($konsultasi->tanggal_konsultasi)->format('d/m/Y')
                : '-',
            'isi_konsultasi'     => $konsultasi->isi_konsultasi ?? '-',
            'hasil_layanan'      => $konsultasi->hasil_layanan ?? '-',
            'tindak_lanjut'      => $konsultasi->tindak_lanjut ?? '-',
            'status'             => $konsultasi->status ?? '-',
            'prioritas'          => $konsultasi->prioritas ?? '-',
            'nama_konselor'      => $konsultasi->gurubk->user->nama ?? '-',
            'nip_konselor'       => $konsultasi->gurubk->nip ?? '-',
            'nama_kategori'      => $konsultasi->kategori->nama_kategori ?? '-',
            'tahun_ajaran'       => $konsultasi->tahunAjaran->tahun ?? '-',
            'semester'           => $konsultasi->tahunAjaran->semester ?? '-',
            'tanggal_cetak'      => now()->locale('id')->translatedFormat('d F Y'),
        ];
    }

    /**
     * Inject data ke dalam template XML.
     * Strategi: cari label lalu replace text element ": " atau kosong SETELAH label
     * dengan data yang sesuai.
     */
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
            if (!isset($data[$variable])) {
                continue;
            }

            $value = htmlspecialchars($data[$variable], ENT_XML1, 'UTF-8');
            $escapedLabel = preg_quote($label, '/');

            // Cari posisi label dalam XML
            $labelPattern = '/<w:t(?:\s[^>]*)?>' . $escapedLabel . '<\/w:t>/';
            if (!preg_match($labelPattern, $xml, $m, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            $labelEnd = $m[0][1] + strlen($m[0][0]);

            // Cari <w:t> pertama SETELAH label, skip tabs
            // Pattern: <w:t ...>TEXT</w:t> - bisa berisi ": ", " ", atau kosong
            $afterLabel = substr($xml, $labelEnd);

            // Cari <w:t> pertama yang mengandung ":" atau spasi/kosong
            if (preg_match('/<w:t(?:\s[^>]*)?>([^<]*)<\/w:t>/', $afterLabel, $tm, PREG_OFFSET_CAPTURE)) {
                $matchStart = $labelEnd + $tm[0][1];
                $matchLen = strlen($tm[0][0]);
                $oldContent = $tm[1][0];

                // Tentukan konten baru: pertahankan ":" jika ada
                if (str_contains($oldContent, ':')) {
                    $newContent = ': ' . $value;
                } else {
                    $newContent = $value;
                }

                // Ganti text element
                $newTag = '<w:t' . ($tm[0][0][3] === ' ' ? substr($tm[0][0], 3, strpos($tm[0][0], '>') - 3) : '') . '>'
                        . $newContent . '</w:t>';

                // Reconstruct: find the full opening tag
                if (preg_match('/<w:t([^>]*)>/', $tm[0][0], $attrMatch)) {
                    $newTag = '<w:t' . $attrMatch[1] . '>' . $newContent . '</w:t>';
                }

                $xml = substr($xml, 0, $matchStart) . $newTag . substr($xml, $matchStart + $matchLen);
            }
        }

        // Tulis ulang document.xml ke ZIP
        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /**
     * Mapping label di template → nama variabel data.
     *
     * @return array<string, string>
     */
    private function getFieldMap(string $templateName): array
    {
        return match ($templateName) {
            'form-penanganan-siswa' => [
                'NAMA'              => 'nama_siswa',
                'KELAS'             => 'kelas',
                'TAHUN AJARAN'      => 'tahun_ajaran',
                'ALAMAT'            => 'alamat_siswa',
                'NIP.'              => 'nip_konselor',
            ],
            'komulatif-record' => [
                'No. Induk'         => 'nis',
                'Nama Siswa'        => 'nama_siswa',
                'Alamat Rumah'      => 'alamat_siswa',
            ],
            'lembar-sosiometri' => [
                'Nama'              => 'nama_siswa',
                'Kelas '            => 'kelas',
            ],
            default => [],
        };
    }

    private function validateTemplate(string $templateName): void
    {
        if (!in_array($templateName, self::VALID_TEMPLATES, true)) {
            throw new \InvalidArgumentException(
                "Template '{$templateName}' tidak valid."
            );
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

    private function createTempCopy(string $templatePath, int $konsultasiId): string
    {
        $tempDir = sys_get_temp_dir();
        $filename = "konsultasi-{$konsultasiId}-" . time() . ".docx";
        $tempPath = $tempDir . DIRECTORY_SEPARATOR . $filename;

        if (!copy($templatePath, $tempPath)) {
            throw new \RuntimeException("Gagal membuat salinan template.");
        }

        return $tempPath;
    }
}
