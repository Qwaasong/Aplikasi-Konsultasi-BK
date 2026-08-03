<?php

namespace App\Livewire\Admin\KelolaUser\Siswa;

use App\Services\ImportExportService;
use App\Services\Siswa\SiswaService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    use WithFileUploads;

    public string $search = '';
    public string $filterKelas = '';
    public string $filterJurusan = '';
    public string $filterJenisKelamin = '';
    public bool $showFilters = false;
    public array $selected = [];
    public bool $selectAll = false;

    // ── IMPORT / EXPORT STATE ─────────────────────────
    public bool $showImportModal = false;
    #[Validate('required|file|mimes:csv,xlsx,xls|max:5120')]
    public $importFile = null;
    public int $importedCount = 0;
    public array $importErrors = [];
    public bool $showExportModal = false;
    public int $exportPreviewCount = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(SiswaService::class);

        $filters = [
            'search' => $this->search ?: null,
            'kelas' => $this->filterKelas ?: null,
            'jurusan' => $this->filterJurusan ?: null,
            'jenis_kelamin' => $this->filterJenisKelamin ?: null,
        ];

        $options = $service->getFilterOptions();

        return [
            'records' => $service->getFiltered($filters),
            'kelasOptions' => $options['kelasOptions'] ?? [],
            'jurusanOptions' => $options['jurusanOptions'] ?? [],
            'jenisKelaminOptions' => $options['jenisKelaminOptions'] ?? [],
        ];
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $records = $this->with()['records'];
            $this->selected = $records->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        $recordsCount = $this->with()['records']->count();
        $this->selectAll = count($this->selected) === $recordsCount && $recordsCount > 0;
    }

    public function create()
    {
        $this->dispatch('create-siswa');
    }

    public function edit($id)
    {
        $this->dispatch('edit-siswa', id: $id);
    }

    public function delete($id)
    {
        $service = app(SiswaService::class);
        $service->delete($id);
        session()->flash('success', 'data berhasil dihapus!');
        $this->selected = array_diff($this->selected, [(string) $id]);
    }

    public function filterAction()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';
        $this->filterJenisKelamin = '';
        $this->selected = [];
        $this->selectAll = false;
    }

    // ── IMPORT (GOOGLE FORMS KOMULATIF RECORD) ──────

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

    public function processImport(SiswaService $service): void
    {
        $this->validate();

        $result = $service->importGformFromFile($this->importFile);

        $this->importedCount = $result['imported'];
        $this->importErrors = $result['errors'];

        if (empty($this->importErrors)) {
            $this->showImportModal = false;
            session()->flash('success', "{$this->importedCount} data komulatif record berhasil diimport.");
        }
    }

    // ── EXPORT ─────────────────────────────────────

    public function openExport(SiswaService $service): void
    {
        $this->exportPreviewCount = $service->getGformExportCount();
        $this->showExportModal = true;
    }

    public function closeExport(): void
    {
        $this->showExportModal = false;
    }

    public function exportCsv(SiswaService $service, ImportExportService $ies): StreamedResponse
    {
        $this->showExportModal = false;

        return $ies->streamCsv('data-komulatif-record-'.date('Ymd-His').'.csv', $service->getGformTemplateHeaders(), $service->exportGformRows());
    }

    public function exportExcel(SiswaService $service, ImportExportService $ies): StreamedResponse
    {
        $this->showExportModal = false;

        return $ies->streamExcelExport('data-komulatif-record-'.date('Ymd-His').'.xlsx', $service->getGformTemplateHeaders(), $service->exportGformRows());
    }

    public function downloadTemplate(SiswaService $service, ImportExportService $ies): StreamedResponse
    {
        return $ies->streamExcelTemplate('template-komulatif-record.xlsx', $service->getGformTemplateHeaders(), []);
    }
}
