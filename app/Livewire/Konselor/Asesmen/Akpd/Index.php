<?php

namespace App\Livewire\Konselor\Asesmen\Akpd;

use App\Services\Asesmen\AkpdService;
use App\Services\ImportExportService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use App\Models\DataSiswa;
use Livewire\WithFileUploads;
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

    #[Url]
    public ?int $edit = null;

    public string $search = '';
    public string $searchSiswa = '';

    public string $filterKelas = '';
    public string $filterJurusan = '';

    public ?string $selectedTingkat = null;

    public array $kelasOptions = [];
    public array $jurusanOptions = [];

    public $siswa_id = '';
    public $tanggal = '';
    public string $tahun_pelajaran = '';

    public array $jawaban = [];

    public int $aspekStep = 1;

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
        $this->records = collect();

        $this->students = DataSiswa::with(['user', 'kelas.jurusan'])
            ->get()
            ->sortBy(fn ($student) => $student->nama ?? '')
            ->values();

        $this->loadData();
        $this->loadFilterOptions();

        if ($this->edit) {
            $record = app(AkpdService::class)->findById($this->edit);
            if ($record) {
                // Determine 'tingkat' from 'kelas_label'
                $kelas = explode(' ', $record->siswa?->kelas_label ?? '')[0];
                if (in_array($kelas, ['X', 'XI', 'XII'])) {
                    $this->pilihTingkat($kelas);
                }
                $this->loadAkpd($this->edit);
            }
            $this->edit = null;
        }
    }

    public function loadData(): void
    {
        $service = app(AkpdService::class);

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

    public function goToDetail(int $id)
    {
        return redirect()->route('konselor.asesmen.akpd.detail', $id);
    }

    public function loadFilterOptions(): void
    {
        $service = app(AkpdService::class);

        $options = $service->getFilterOptions();

        $this->kelasOptions = $options['kelasOptions'] ?? [];
        $this->jurusanOptions = $options['jurusanOptions'] ?? [];
    }

    public function nextStep(): void
    {
        $this->validate([
            'siswa_id' => 'required|integer',
            'tanggal' => 'required|date',
            'tahun_pelajaran' => 'nullable|string|max:20',
        ]);

        $this->step = 2;
        $this->aspekStep = 1;
    }

    public function previousStep(): void
    {
        $this->step = 1;
    }

    public function nextAspect(): void
    {
        if ($this->aspekStep < 5) {
            $this->aspekStep++;
        }
    }

    public function previousAspect(): void
    {
        if ($this->aspekStep > 1) {
            $this->aspekStep--;
        }
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

    #[On('create-akpd')]
    public function createAkpd(): void
    {
        $this->resetValidation();

        $this->reset([
            'siswa_id',
            'tahun_pelajaran',
            'jawaban',
            'aspekStep',
            'files',
            'existingFiles',
            'newFiles',
        ]);

        $this->editingId = null;
        $this->tanggal = now()->format('Y-m-d');
        $this->step = 1;
        $this->aspekStep = 1;
        $this->showStudentModal = false;

        $this->dispatch('open-modal', 'form-akpd');
    }

    #[On('edit-akpd')]
    public function loadAkpd(int $id): void
    {
        $service = app(AkpdService::class);

        $this->resetValidation();

        $record = $service->findById($id);

        $this->editingId = $id;

        $this->siswa_id = $record->siswa_id;

        $this->tanggal = optional($record->tanggal)->format('Y-m-d');
        $this->tahun_pelajaran = $record->tahun_pelajaran;

        // Map q01..q50 -> jawaban[1..50]
        $this->jawaban = [];
        foreach (range(1, 50) as $no) {
            $key = 'q' . str_pad((string) $no, 2, '0', STR_PAD_LEFT);
            $this->jawaban[$no] = $record->{$key} ?? null;
        }

        $this->step = 1;
        $this->aspekStep = 1;

        $this->dispatch('open-modal', 'form-akpd');
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

    public function delete(int $id, AkpdService $service): void
    {
        $service->delete($id);

        session()->flash(
            'success',
            'AKPD berhasil dihapus!'
        );

        $this->loadData();
    }

    public function save(AkpdService $service): void
    {
        $this->validate([
            'siswa_id' => 'required|integer',
            'tanggal' => 'required|date',
            'tahun_pelajaran' => 'nullable|string|max:20',
            'jawaban' => 'nullable|array',
            'files' => 'array|max:5',
            'files.*' => 'file|max:12288|mimes:pdf,jpg,jpeg,png,docx',
        ]);

        $data = [
            'siswa_id' => $this->siswa_id,
            'tanggal' => $this->tanggal,
            'tahun_pelajaran' => $this->tahun_pelajaran,
        ];

        // Map jawaban[1..50] -> q01..q50
        foreach (range(1, 50) as $no) {
            $key = 'q' . str_pad((string) $no, 2, '0', STR_PAD_LEFT);
            $data[$key] = in_array($this->jawaban[$no] ?? null, ['Ya', 'Tidak'], true)
                ? $this->jawaban[$no]
                : null;
        }

        if ($this->editingId) {

            $service->update($this->editingId, $data);

            session()->flash(
                'success',
                'AKPD berhasil diperbarui!'
            );

        } else {

            $service->create($data);

            session()->flash(
                'success',
                'AKPD berhasil ditambahkan!'
            );
        }

        $this->reset([
            'siswa_id',
            'tahun_pelajaran',
            'jawaban',
            'aspekStep',
            'files',
            'existingFiles',
            'newFiles',
        ]);

        $this->tanggal = now()->format('Y-m-d');
        $this->editingId = null;
        $this->step = 1;

        $this->dispatch('close-modal', 'form-akpd');
        $this->dispatch('refreshTable');
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

    public function processImport(AkpdService $service): void
    {
        $this->validate(['importFile' => 'required|file|mimes:csv,xlsx,xls|max:5120']);

        $result = $service->importFromFile($this->importFile);

        $this->importedCount = $result['imported'];
        $this->importErrors = $result['errors'];

        if (empty($this->importErrors)) {
            $this->showImportModal = false;
            session()->flash('success', "{$this->importedCount} data AKPD berhasil diimport.");
            $this->loadData();
            $this->loadFilterOptions();
        }
    }

    // ── EXPORT ───────────────────────────────

    public function openExport(AkpdService $service): void
    {
        $this->exportPreviewCount = $service->getExportCount($this->exportFilters());
        $this->showExportModal = true;
    }

    public function closeExport(): void
    {
        $this->showExportModal = false;
    }

    public function exportCsv(AkpdService $service, ImportExportService $ies): StreamedResponse
    {
        $this->showExportModal = false;

        return $ies->streamCsv('data-akpd-'.date('Ymd-His').'.csv', $service->getTemplateHeaders(), $service->exportRows($this->exportFilters()));
    }

    public function exportExcel(AkpdService $service, ImportExportService $ies): StreamedResponse
    {
        $this->showExportModal = false;

        return $ies->streamExcelExport('data-akpd-'.date('Ymd-His').'.xlsx', $service->getTemplateHeaders(), $service->exportRows($this->exportFilters()));
    }

    public function downloadTemplate(AkpdService $service, ImportExportService $ies): StreamedResponse
    {
        return $ies->streamExcelTemplate('template-akpd.xlsx', $service->getTemplateHeaders(), $service->getTemplateSampleRows());
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
};
?>