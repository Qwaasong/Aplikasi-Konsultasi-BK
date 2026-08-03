<?php

namespace App\Livewire\Konselor\Asesmen\Dcm;

use App\Models\DataSiswa;
use App\Models\Dcm;
use App\Services\Asesmen\DcmService;
use App\Services\ImportExportService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    use WithFileUploads;

    public Collection $records;

    public bool $showFilters = false;

    public array $selected = [];

    public $students = [];

    public bool $showModal = false;
    public bool $isEdit = false;
    public bool $showStudentModal = false;
    public int $step = 1;

    public ?int $editingId = null;

    public string $search = '';
    public string $searchSiswa = '';
    public string $filterKelas = '';
    public string $filterJurusan = '';

    public ?string $selectedTingkat = null;

    public array $kelasOptions = [];
    public array $jurusanOptions = [];

    public $siswa_id = '';
    public $tanggal = '';

    public array $jawaban = [];

    public $kesimpulan = '';
    public $catatan = '';

    public array $files = [];
    public array $existingFiles = [];
    public array $newFiles = [];

    // ── IMPORT / EXPORT STATE ─────────────────────────
    public bool $showImportModal = false;
    #[Validate('required|file|mimes:csv,xlsx,xls|max:5120')]
    public $importFile = null;
    public int $importedCount = 0;
    public array $importErrors = [];
    public bool $showExportModal = false;
    public int $exportPreviewCount = 0;

    public function mount(): void
    {
        $service = app(DcmService::class);

        $options = $service->getFilterOptions();

        $this->kelasOptions = $options['kelasOptions'] ?? [];
        $this->jurusanOptions = $options['jurusanOptions'] ?? [];
        
        $this->records = collect();

        $this->students = DataSiswa::with([
            'user',
            'kelas.jurusan'
        ])
            ->get()
            ->sortBy(fn ($student) => $student->nama ?? '')
            ->values();

        $this->loadData();
        $this->loadFilterOptions();
    }

    public function loadData(): void
    {
        $service = app(DcmService::class);

        $this->records = $service->getFiltered([
            'search' => $this->search,
            'kelas' => $this->filterKelas,
            'jurusan' => $this->filterJurusan,
            'tingkat' => $this->selectedTingkat,
        ]);
    }

    public function pilihTingkat(string $tingkat): void
    {
        if (!in_array($tingkat, ['X', 'XI', 'XII'], true)) {
            return;
        }

        $this->selectedTingkat = $tingkat;
        $this->search = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';

        $this->loadData();
    }

    public function kembaliKeTingkat(): void
    {
        $this->selectedTingkat = null;
        $this->search = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';

        $this->records = collect();
    }

    public function loadFilterOptions(): void
    {
        $service = app(DcmService::class);

        $options = $service->getFilterOptions();

        $this->kelasOptions = $options['kelasOptions'] ?? [];
        $this->jurusanOptions = $options['jurusanOptions'] ?? [];
    }

    #[On('create-dcm')]
    public function createDcm(): void
    {
        $this->resetValidation();

        $this->reset([
            'siswa_id',
            'jawaban',
            'kesimpulan',
            'catatan',
        ]);

        $this->step = 1;
        $this->showStudentModal = false;

        $this->editingId = null;
        $this->tanggal = now()->format('Y-m-d');

        $this->dispatch('open-modal', 'form-dcm');
    }

    #[On('edit-dcm')]
    public function loadDcm(int $id): void
    {
        $service = app(DcmService::class);

        $this->resetValidation();

        $record = $service->findById($id);

        if (!$record) {
            return;
        }

        $this->editingId = $id;
        $this->siswa_id = $record->siswa_id;
        $this->tanggal = optional($record->tanggal)->format('Y-m-d');

        $this->jawaban = $record->jawaban ?? [];
        $this->kesimpulan = $record->kesimpulan;
        $this->catatan = $record->catatan;
        $this->step = 1;
        $this->showStudentModal = false;
        $this->dispatch('open-modal', 'form-dcm');
    }

    public function selectStudent(int $id): void
    {
        $this->siswa_id = $id;
        $this->showStudentModal = false;
        $this->searchSiswa = '';
    }

    public function openStudentModal(): void
    {
        $this->showStudentModal = true;
    }

    public function closeStudentModal(): void
    {
        $this->showStudentModal = false;
    }

    public function delete(int $id, DcmService $service): void
    {
        $service->delete($id);

        session()->flash(
            'success',
            'DCM berhasil dihapus!'
        );

        $this->loadData();
    }

    public function save(DcmService $service): void
    {
        $this->validate([
            'siswa_id' => 'required|integer',
            'tanggal' => 'required|date',
            'jawaban' => 'nullable|array',
            'kesimpulan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $jawaban = $this->jawaban;

        // Buat ringkasan masalah dari jawaban yang dicentang.
        $dcm = new Dcm();
        $dcm->jawaban = $jawaban;

        $data = [
            'siswa_id' => $this->siswa_id,
            'tanggal' => $this->tanggal,
            'jawaban' => $jawaban,
            'masalah_teridentifikasi' => $dcm->masalahSummary(),
            'kesimpulan' => $this->kesimpulan,
            'catatan' => $this->catatan,
        ];

        if ($this->editingId) {
            $service->update($this->editingId, $data);

            session()->flash(
                'success',
                'DCM berhasil diperbarui!'
            );
        } else {
            $service->create($data);

            session()->flash(
                'success',
                'DCM berhasil ditambahkan!'
            );
        }

        $this->reset([
            'siswa_id',
            'jawaban',
            'kesimpulan',
            'catatan',
        ]);

        $this->tanggal = now()->format('Y-m-d');
        $this->editingId = null;
        $this->step = 1;
        $this->dispatch('close-modal', 'form-dcm');
        $this->loadData();
    }

    public function updatedSearch(): void
    {
        $this->loadData();
    }

    public function updatedFilterKelas(): void
    {
        $this->loadData();
    }

    public function updatedFilterJurusan(): void
    {
        $this->loadData();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'filterKelas',
            'filterJurusan',
        ]);

        $this->loadData();
    }

    public function filterAction(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    public function nextStep(): void
    {
        $this->validate([
            'siswa_id' => 'required|integer',
            'tanggal' => 'required|date',
        ]);

        $this->step = 2;
    }

    public function previousStep(): void
    {
        $this->step = 1;
    }

    public function getInitials(?string $name): string
    {
        if (!$name) {
            return 'S';
        }

        $words = explode(' ', trim($name));

        if (count($words) >= 2) {
            return strtoupper(
                substr($words[0], 0, 1) .
                substr($words[1], 0, 1)
            );
        }

        return strtoupper(substr($name, 0, 2));
    }

    #[On('refreshTable')]
    public function refreshTable(): void
    {
        $this->loadData();
    }

    public function updatedNewFiles(): void
    {
        $this->validateOnly('newFiles.*', [
            'newFiles.*' => 'file|max:12288|mimes:pdf,jpg,jpeg,png,docx',
        ]);

        foreach ($this->newFiles as $file) {
            if (count($this->files) < 5) {
                $this->files[] = $file;
            }
        }

        $this->newFiles = [];
    }

    public function removeFile(int $index): void
    {
        unset($this->files[$index]);

        $this->files = array_values($this->files);
    }

    public function removeExistingFile(int $index): void
    {
        unset($this->existingFiles[$index]);

        $this->existingFiles = array_values($this->existingFiles);
    }

    public function goToDetail($id)
    {
        return redirect()->route('konselor.asesmen.dcm.detail', $id);
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

    public function processImport(DcmService $service): void
    {
        $this->validate();

        $result = $service->importFromFile($this->importFile);

        $this->importedCount = $result['imported'];
        $this->importErrors = $result['errors'];

        if (empty($this->importErrors)) {
            $this->showImportModal = false;
            session()->flash('success', "{$this->importedCount} data DCM berhasil diimport.");
            $this->loadData();
            $this->loadFilterOptions();
        }
    }

    // ── EXPORT ───────────────────────────────

    public function openExport(DcmService $service): void
    {
        $this->exportPreviewCount = $service->getExportCount($this->exportFilters());
        $this->showExportModal = true;
    }

    public function closeExport(): void
    {
        $this->showExportModal = false;
    }

    public function exportCsv(DcmService $service, ImportExportService $ies): StreamedResponse
    {
        $this->showExportModal = false;

        return $ies->streamCsv('data-dcm-'.date('Ymd-His').'.csv', $service->getTemplateHeaders(), $service->exportRows($this->exportFilters()));
    }

    public function exportExcel(DcmService $service, ImportExportService $ies): StreamedResponse
    {
        $this->showExportModal = false;

        return $ies->streamExcelExport('data-dcm-'.date('Ymd-His').'.xlsx', $service->getTemplateHeaders(), $service->exportRows($this->exportFilters()));
    }

    public function downloadTemplate(DcmService $service, ImportExportService $ies): StreamedResponse
    {
        return $ies->streamExcelTemplate('template-dcm.xlsx', $service->getTemplateHeaders(), $service->getTemplateSampleRows());
    }

    private function exportFilters(): array
    {
        return [
            'search' => $this->search ?: null,
            'kelas' => $this->filterKelas ?: null,
            'jurusan' => $this->filterJurusan ?: null,
            'tingkat' => $this->selectedTingkat ?: null,
        ];
    }
}