<?php

namespace App\Livewire\Admin\KelolaUser\Pegawai;

use App\Services\ImportExportService;
use App\Services\PegawaiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    use WithFileUploads;

    public string $search = '';
    public string $filterJabatan = '';
    public bool $showFilters = false;
    public array $selected = [];
    public bool $selectAll = false;

    // ── IMPORT STATE ─────────────────────────
    public bool $showImportModal = false;
    #[Validate('required|file|mimes:csv,xlsx,xls|max:5120')]
    public $importFile = null;
    public int $importedCount = 0;
    public array $importErrors = [];

    // ── EXPORT STATE ─────────────────────────
    public bool $showExportModal = false;
    public int $exportPreviewCount = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(PegawaiService::class);

        $filters = [
            'search' => $this->search ?: null,
            'jabatan' => $this->filterJabatan ?: null,
        ];

        $options = $service->getFilterOptions();

        return [
            'records' => $service->getFiltered($filters),
            'jabatanOptions' => $options['jabatanOptions'] ?? [],
        ];
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->with()['records']->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        $count = $this->with()['records']->count();
        $this->selectAll = count($this->selected) === $count && $count > 0;
    }

    public function create()
    {
        $this->dispatch('create-pegawai');
    }

    public function edit($id)
    {
        $this->dispatch('edit-pegawai', id: $id);
    }

    public function delete($id)
    {
        app(PegawaiService::class)->delete($id);
        session()->flash('success', 'Data pegawai berhasil dihapus.');
        $this->selected = array_diff($this->selected, [(string) $id]);
    }

    public function filterAction()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterJabatan = '';
        $this->selected = [];
        $this->selectAll = false;
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

    public function processImport(PegawaiService $service): void
    {
        $this->validate();

        $result = $service->importFromFile($this->importFile);

        $this->importedCount = $result['imported'];
        $this->importErrors = $result['errors'];

        if (empty($this->importErrors)) {
            $this->showImportModal = false;
            session()->flash('success', "{$this->importedCount} data pegawai berhasil diimport.");
        }
    }

    // ── EXPORT ───────────────────────────────

    public function openExport(PegawaiService $service): void
    {
        $this->exportPreviewCount = $service->getExportCount([
            'search' => $this->search ?: null,
            'jabatan' => $this->filterJabatan ?: null,
        ]);
        $this->showExportModal = true;
    }

    public function closeExport(): void
    {
        $this->showExportModal = false;
    }

    public function exportCsv(PegawaiService $service, ImportExportService $ies): StreamedResponse
    {
        $rows = $service->exportRows([
            'search' => $this->search ?: null,
            'jabatan' => $this->filterJabatan ?: null,
        ]);

        $this->showExportModal = false;

        return $ies->streamCsv('data-pegawai-' . date('Ymd-His') . '.csv', $service->getTemplateHeaders(), $rows);
    }

    public function exportExcel(PegawaiService $service, ImportExportService $ies): StreamedResponse
    {
        $rows = $service->exportRows([
            'search' => $this->search ?: null,
            'jabatan' => $this->filterJabatan ?: null,
        ]);

        $this->showExportModal = false;

        return $ies->streamExcelExport('data-pegawai-' . date('Ymd-His') . '.xlsx', $service->getTemplateHeaders(), $rows);
    }

    public function downloadTemplate(PegawaiService $service, ImportExportService $ies): StreamedResponse
    {
        return $ies->streamExcelTemplate('template-pegawai.xlsx', $service->getTemplateHeaders(), $service->getTemplateSampleRows());
    }
}
