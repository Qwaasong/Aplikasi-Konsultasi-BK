<?php

namespace App\Livewire\Konselor\KehadiranSiswa;

use App\Services\ImportExportService;
use App\Services\Siswa\KehadiranService;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    use WithFileUploads;

    public string $search = '';

    public string $filterKelas = '';
    public string $filterStatus = '';
    public string $filterTanggal = '';
    public string $filterTahun = '';

    public bool $showFilters = false;

    public ?string $selectedKelas = null;

    public array $records = [];
    public array $kelasOptions = [];
    public array $tahunOptions = [];

    // ── IMPORT STATE ─────────────────────────
    public bool $showImportModal = false;
    #[Validate('required|file|mimes:csv,xlsx,xls|max:5120')]
    public $importFile = null;
    public int $importedCount = 0;
    public array $importErrors = [];

    // ── EXPORT STATE ─────────────────────────
    public bool $showExportModal = false;
    public int $exportPreviewCount = 0;

    public function mount(): void
    {
        $this->loadKelas();
    }

    /**
     * Mengambil daftar kelas
     */
    public function loadKelas(): void
    {
        $service = app(KehadiranService::class);

        $records = $service->getFiltered([
            'search' => null,
            'kelas' => null,
            'status' => null,
            'tanggal' => null,
            'tahun' => null,
        ]);

        $this->kelasOptions = $records
            ->map(fn ($item) => $item->siswa?->kelas_label)
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Mengambil data kehadiran berdasarkan kelas
     */
    public function loadData(): void
    {
        if (!$this->selectedKelas) {
            $this->records = [];
            return;
        }

        $service = app(KehadiranService::class);

        $filters = [
            'search' => $this->search ?: null,
            'kelas' => $this->selectedKelas,
            'status' => $this->filterStatus ?: null,
            'tanggal' => $this->filterTanggal ?: null,
            'tahun' => $this->filterTahun ?: null,
        ];

        $records = $service->getFiltered($filters);

        $this->records = $records->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->siswa?->nama ?? '-',
                'kelas' => $item->siswa?->kelas_label ?? '-',
                'tanggal' => $item->tanggal_kehadiran,
                'status' => $item->status,
                'tahun' => $item->tahunAjaran?->tahun ?? '-',
            ];
        })->toArray();

        $options = $service->getFilterOptions();
        $this->kelasOptions = $options['kelasOptions'] ?? [];
        $this->tahunOptions = $options['tahunOptions'] ?? [];
    }

    /**
     * Membuka data kehadiran berdasarkan kelas
     */
    public function pilihKelas(string $kelas): void
    {
        $this->selectedKelas = $kelas;

        $this->search = '';
        $this->filterKelas = '';
        $this->filterStatus = '';
        $this->filterTanggal = '';
        $this->filterTahun = '';
        $this->showFilters = false;

        $this->loadData();
    }

    /**
     * Kembali ke daftar kelas
     */
    public function kembaliKeKelas(): void
    {
        $this->selectedKelas = null;

        $this->search = '';
        $this->filterKelas = '';
        $this->filterStatus = '';
        $this->filterTanggal = '';
        $this->filterTahun = '';

        $this->records = [];
        $this->showFilters = false;

        $this->loadKelas();
    }

    public function create(): void
    {
        $this->dispatch('create-kehadiran');
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterTanggal = '';
        $this->filterTahun = '';

        $this->loadData();
    }

    public function refreshData(): void
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterTanggal = '';
        $this->filterTahun = '';

        $this->loadKelas();

        if ($this->selectedKelas) {
            $this->loadData();
        }
    }

    public function filterAction(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    // ── IMPORT ───────────────────────────────

    public function openImport(): void
    {
        $this->resetValidation('importFile');
        $this->importFile = null;
        $this->importedCount = 0;
        $this->importErrors = [];
        $this->showImportModal = true;
    }

    public function closeImport(): void
    {
        $this->showImportModal = false;
    }

    public function processImport(KehadiranService $service): void
    {
        $this->validate();

        $result = $service->importFromFile($this->importFile);

        $this->importedCount = $result['imported'];
        $this->importErrors = $result['errors'];

        if (empty($this->importErrors)) {
            $this->showImportModal = false;
            session()->flash('success', "{$this->importedCount} data kehadiran berhasil diimport.");
            $this->refreshData();
        }
    }

    // ── EXPORT ───────────────────────────────

    public function openExport(KehadiranService $service): void
    {
        $this->refreshExportPreview($service);
        $this->showExportModal = true;
    }

    public function closeExport(): void
    {
        $this->showExportModal = false;
    }

    public function refreshExportPreview(KehadiranService $service): void
    {
        $this->exportPreviewCount = $service->getExportCount([
            'kelas' => $this->selectedKelas,
            'search' => $this->search ?: null,
            'status' => $this->filterStatus ?: null,
            'tahun' => $this->filterTahun ?: null,
        ]);
    }

    public function exportCsv(KehadiranService $service, ImportExportService $ies): StreamedResponse
    {
        $rows = $service->exportRows([
            'kelas' => $this->selectedKelas,
            'search' => $this->search ?: null,
            'status' => $this->filterStatus ?: null,
            'tahun' => $this->filterTahun ?: null,
        ]);

        $this->showExportModal = false;

        return $ies->streamCsv('kehadiran-' . date('Ymd-His') . '.csv', $service->getTemplateHeaders(), $rows);
    }

    public function exportExcel(KehadiranService $service, ImportExportService $ies): StreamedResponse
    {
        $rows = $service->exportRows([
            'kelas' => $this->selectedKelas,
            'search' => $this->search ?: null,
            'status' => $this->filterStatus ?: null,
            'tahun' => $this->filterTahun ?: null,
        ]);

        $this->showExportModal = false;

        return $ies->streamExcelExport('kehadiran-' . date('Ymd-His') . '.xlsx', $service->getTemplateHeaders(), $rows);
    }

    // ── TEMPLATE ─────────────────────────────

    public function downloadTemplate(KehadiranService $service, ImportExportService $ies): StreamedResponse
    {
        return $ies->streamExcelTemplate('template-kehadiran.xlsx', $service->getTemplateHeaders(), $service->getTemplateSampleRows());
    }
}