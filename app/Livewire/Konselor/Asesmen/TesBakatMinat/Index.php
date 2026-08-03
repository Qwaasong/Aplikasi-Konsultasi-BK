<?php

namespace App\Livewire\Konselor\Asesmen\TesBakatMinat;

use App\Models\DataSiswa;
use App\Models\Jurusan;
use App\Models\Peminatan;
use App\Services\Asesmen\PeminatanService;
use App\Services\ImportExportService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    use WithFileUploads;

    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    public Collection $records;

    public array $students = [];

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    public bool $showFilters = false;

    public array $selected = [];

    /*
    |--------------------------------------------------------------------------
    | MODAL
    |--------------------------------------------------------------------------
    */

    public bool $showStudentModal = false;

    public int $step = 1;

    public ?int $editingId = null;

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    public string $search = '';

    public string $searchSiswa = '';

    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

    public string $filterKelas = '';

    public string $filterJurusan = '';

    public ?string $selectedTingkat = null;

    public array $kelasOptions = [];

    public array $jurusanOptions = [];

    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public $siswa_id = '';

    public $tanggal = '';

    public $pilihan1 = '';

    public $pilihan2 = '';

    public $pilihan3 = '';

    public $hasil = '';

    public $catatan = '';

    public array $jawaban = [];

    public $files = [];

    public $newFiles = [];

    public $existingFiles = [];

    // ── IMPORT / EXPORT STATE ─────────────────────────
    public bool $showImportModal = false;
    #[Validate('required|file|mimes:csv,xlsx,xls|max:5120')]
    public $importFile = null;
    public int $importedCount = 0;
    public array $importErrors = [];
    public bool $showExportModal = false;
    public int $exportPreviewCount = 0;

    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {

        $this->records = collect();

        $this->students = DataSiswa::with([
            'user',
            'kelas.jurusan',
        ])
            ->get()
            ->sortBy(fn ($student) => $student->nama ?? '')
            ->values()
            ->all();

        $this->jurusanOptions = Jurusan::query()
            ->orderBy('nama_jurusan')
            ->pluck('nama_jurusan')
            ->toArray();
        $this->loadData();
        $this->loadFilterOptions();

    }

    /*
    |--------------------------------------------------------------------------
    | LOAD DATA
    |--------------------------------------------------------------------------
    */

    public function loadData(): void
    {

        $service = app(
            PeminatanService::class
        );

        $this->records = $service->getFiltered([
            'search' => $this->search,
            'kelas' => $this->filterKelas,
            'jurusan' => $this->filterJurusan,
            'tingkat' => $this->selectedTingkat,

        ]);

    }

    public function pilihTingkat(string $tingkat): void
    {
        if (! in_array($tingkat, ['X', 'XI', 'XII'], true)) {
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

    /*
    |--------------------------------------------------------------------------
    | FILTER OPTIONS
    |--------------------------------------------------------------------------
    */

    public function loadFilterOptions(): void
    {
        $service = app(
            PeminatanService::class
        );
        $options = $service->getFilterOptions();

        $this->kelasOptions =
            $options['kelasOptions'] ?? [];

        $this->jurusanOptions =
            $options['jurusanOptions'] ?? [];

    }

    /*
    |--------------------------------------------------------------------------
    | STUDENT
    |--------------------------------------------------------------------------
    */

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

        if (! $name) {
            return 'S';
        }

        $words = explode(
            ' ',
            trim($name)
        );

        if (count($words) >= 2) {
            return strtoupper(
                substr($words[0], 0, 1).
                substr($words[1], 0, 1)
            );
        }

        return strtoupper(
            substr($name, 0, 2)
        );

    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    #[On('create-peminatan')]
    public function createPeminatan(): void
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editingId = null;
        $this->tanggal =
            now()->format('Y-m-d');

        $this->dispatch(
            'open-modal',
            'form-peminatan'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT / LOAD
    |--------------------------------------------------------------------------
    */

    #[On('edit-peminatan')]
    public function loadPeminatan(
        int $id
    ): void {
        $service = app(
            PeminatanService::class
        );

        $this->resetValidation();

        $record =
            $service->findById($id);

        $this->editingId = $id;

        $this->siswa_id =
            $record->siswa_id;

        $this->tanggal =
            optional($record->tanggal)
                ->format('Y-m-d');

        $this->pilihan1 =
            $record->pilihan1;

        $this->pilihan2 =
            $record->pilihan2;

        $this->pilihan3 =
            $record->pilihan3;

        $this->hasil =
            $record->hasil;

        $this->catatan =
            $record->catatan;

        $this->jawaban =
            $record->jawaban ?? [];

        $this->step = 1;

        $this->dispatch(
            'open-modal',
            'form-peminatan'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE
    |--------------------------------------------------------------------------
    */

    public function save(
        PeminatanService $service
    ): void {
        $this->validate([

            'siswa_id' => 'required|integer',

            'tanggal' => 'required|date',

            'pilihan1' => 'required|string',

            'pilihan2' => 'nullable|string',

            'pilihan3' => 'nullable|string',

            'hasil' => 'nullable|string',

            'catatan' => 'nullable|string',

            'jawaban' => 'nullable|array',

        ]);

        $peminatan = new Peminatan;
        $peminatan->jawaban = $this->jawaban;
        $dominant = $peminatan->dominantIntelligences();

        $data = [

            'siswa_id' => $this->siswa_id,
            'tanggal' => $this->tanggal,
            'pilihan1' => $this->pilihan1,
            'pilihan2' => $this->pilihan2,
            'pilihan3' => $this->pilihan3,
            'jawaban' => $this->jawaban,
            'hasil' => $dominant[0] !== '' ? $dominant[0] : ($this->hasil ?: ''),
            'catatan' => $this->catatan,

        ];

        if ($this->editingId) {

            $service->update(
                $this->editingId,
                $data
            );

            session()->flash(
                'success',
                'Data peminatan berhasil diperbarui!'
            );

        } else {
            $service->create($data);

            session()->flash(
                'success',
                'Data peminatan berhasil ditambahkan!'
            );

        }

        $this->resetForm();

        $this->dispatch(
            'close-modal',
            'form-peminatan'
        );

        $this->dispatch(
            'refreshTable'
        );

    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $id,
        PeminatanService $service
    ): void {

        $service->delete($id);

        session()->flash(
            'success',
            'Data peminatan berhasil dihapus!'
        );

        $this->loadData();

    }

    /*
    |--------------------------------------------------------------------------
    | RESET FORM
    |--------------------------------------------------------------------------
    */

    public function resetForm(): void
    {

        $this->reset([

            'siswa_id',

            'pilihan1',

            'pilihan2',

            'pilihan3',

            'hasil',

            'catatan',

            'jawaban',

            'files',

            'newFiles',

            'existingFiles',

        ]);

        $this->tanggal =
            now()->format('Y-m-d');

        $this->step = 1;

        $this->editingId = null;

    }

    /*
    |--------------------------------------------------------------------------
    | FILTER EVENT
    |--------------------------------------------------------------------------
    */

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

    public function filterAction(): void
    {

        $this->showFilters =
            ! $this->showFilters;

    }

    public function resetFilters(): void
    {

        $this->search = '';

        $this->filterKelas = '';

        $this->filterJurusan = '';

        $this->loadData();

    }

    #[On('refreshTable')]
    public function refreshTable(): void
    {

        $this->loadData();

        $this->loadFilterOptions();

    }

    public function goToDetail($id)
    {
        return redirect()->route('konselor.asesmen.tes-bakat-minat.detail', $id);
    }

    /*
    |--------------------------------------------------------------------------
    | STEP
    |--------------------------------------------------------------------------
    */

    public function nextStep(): void
    {
        $this->validate([
            'siswa_id' => 'required|integer',
            'tanggal' => 'required|date',
            'pilihan1' => 'required|string',
            'jawaban' => 'nullable|array',
        ]);

        $this->step = 2;
    }

    public function previousStep(): void
    {
        $this->step = 1;
    }

    /*
    |--------------------------------------------------------------------------
    | FILE UPLOAD
    |--------------------------------------------------------------------------
    */

    public function updatedNewFiles(): void
    {
        $this->validate([
            'newFiles.*' => 'file|max:12288|mimes:pdf,jpg,jpeg,png,docx',
        ]);

        $this->files = array_merge($this->files ?? [], $this->newFiles);
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

    /*
    |--------------------------------------------------------------------------
    | COMPUTED
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function skorKecerdasan(): array
    {
        return collect(Peminatan::SECTIONS)
            ->map(fn (string $section) => [
                'section' => $section,
                'skor' => count($this->jawaban[$section] ?? []),
                'total' => count(Peminatan::QUESTION_GROUPS[$section] ?? []),
            ])
            ->all();
    }

    #[Computed]
    public function dominantKecerdasan(): string
    {
        $peminatan = new Peminatan;
        $peminatan->jawaban = $this->jawaban;

        return $peminatan->dominantIntelligences()[0] ?? '';
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

    public function processImport(PeminatanService $service): void
    {
        $this->validate();

        $result = $service->importFromFile($this->importFile);

        $this->importedCount = $result['imported'];
        $this->importErrors = $result['errors'];

        if (empty($this->importErrors)) {
            $this->showImportModal = false;
            session()->flash('success', "{$this->importedCount} data tes bakat minat berhasil diimport.");
            $this->loadData();
            $this->loadFilterOptions();
        }
    }

    // ── EXPORT ───────────────────────────────

    public function openExport(PeminatanService $service): void
    {
        $this->exportPreviewCount = $service->getExportCount($this->exportFilters());
        $this->showExportModal = true;
    }

    public function closeExport(): void
    {
        $this->showExportModal = false;
    }

    public function exportCsv(PeminatanService $service, ImportExportService $ies): StreamedResponse
    {
        $this->showExportModal = false;

        return $ies->streamCsv('data-peminatan-'.date('Ymd-His').'.csv', $service->getTemplateHeaders(), $service->exportRows($this->exportFilters()));
    }

    public function exportExcel(PeminatanService $service, ImportExportService $ies): StreamedResponse
    {
        $this->showExportModal = false;

        return $ies->streamExcelExport('data-peminatan-'.date('Ymd-His').'.xlsx', $service->getTemplateHeaders(), $service->exportRows($this->exportFilters()));
    }

    public function downloadTemplate(PeminatanService $service, ImportExportService $ies): StreamedResponse
    {
        return $ies->streamExcelTemplate('template-peminatan.xlsx', $service->getTemplateHeaders(), $service->getTemplateSampleRows());
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
