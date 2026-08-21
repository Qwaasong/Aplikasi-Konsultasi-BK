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

    public $hasil = '';

    public $catatan = '';

    public array $jawaban = [];



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

    private function initializeJawaban(): array
    {
        $jawaban = [];
        foreach (Peminatan::SECTIONS as $section) {
            $jawaban[$section] = [];
        }
        return $jawaban;
    }

    public function mount(): void
    {
        $this->jawaban = $this->initializeJawaban();

        $this->records = collect();

        $this->students = DataSiswa::with([
            'user',
            'kelas.jurusan',
        ])
            ->get()
            ->sortBy(fn ($student) => $student->nama ?? '')
            ->values()
            ->map(fn ($student) => [
                'id'            => $student->id,
                'nama'          => $student->nama ?? '',
                'nis'           => $student->nis ?? '',
                'kelas_label'   => $student->kelas_label ?? '-',
                'jurusan_label' => $student->jurusan_label ?? '-',
            ])
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

        $this->siswa_id = (int) $record->siswa_id;

        $this->tanggal =
            optional($record->tanggal)
                ->format('Y-m-d');

        $this->hasil =
            $record->hasil;

        $this->catatan =
            $record->catatan;

        // Pastikan struktur jawaban lengkap per-section
        $loadedJawaban = $record->jawaban ?? [];
        $jawaban = $this->initializeJawaban();
        foreach (Peminatan::SECTIONS as $section) {
            if (isset($loadedJawaban[$section]) && is_array($loadedJawaban[$section])) {
                $jawaban[$section] = $loadedJawaban[$section];
            }
        }
        $this->jawaban = $jawaban;

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

            'hasil',

            'catatan',

        ]);

        $this->jawaban = $this->initializeJawaban();

        $this->tanggal =
            now()->format('Y-m-d');

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
