<?php

namespace App\Livewire\Admin\Siswa;

use App\Constants\GlobalMessages;
use App\Services\SiswaService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterKelas = '';
    public string $filterJurusan = '';
    public string $filterJenisKelamin = '';
    public int $perPage = 35;

    public ?int $editingId = null;
    public bool $showForm = false;

    #[Validate('required|integer|min:1')]
    public string $nis = '';

    #[Validate('required|string|max:255')]
    public string $nama = '';

    #[Validate('required|integer|exists:kelas,id')]
    public string $kelas = '';

    #[Validate('required|in:L,P')]
    public string $jenis_kelamin = 'L';

    #[Validate('required|string|max:500')]
    public string $alamat = '';

    public bool $showImportModal = false;
    public $importFile = null;
    public array $importErrors = [];
    public ?int $importedCount = null;

    public bool $showExportModal = false;
    public string $exportKelas = '';
    public string $exportJurusan = '';
    public string $exportPeriode = '';
    public ?int $exportPreviewCount = null;

    public bool $showDetail = false;
    public ?object $detailSiswa = null;

    public array $jenisKelaminOptions = [
        ['value' => 'L', 'label' => 'Laki-laki'],
        ['value' => 'P', 'label' => 'Perempuan'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(SiswaService::class);
        $filters = [
            'search' => $this->search,
            'kelas' => $this->filterKelas ?: null,
            'jurusan' => $this->filterJurusan ?: null,
            'jenis_kelamin' => $this->filterJenisKelamin ?: null,
            'per_page' => $this->perPage,
        ];
        return [
            'records' => $service->getPaginated($filters),
            'filterOptions' => $service->getFilterOptions(),
            'stats' => $service->getStats(),
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $siswa = app(SiswaService::class)->findById($id);
        $this->editingId = $id;
        $this->nis = (string) $siswa->nis;
        $this->nama = $siswa->nama;
        $this->kelas = (string) $siswa->kelas_id;
        $this->jenis_kelamin = $siswa->jenis_kelamin;
        $this->alamat = $siswa->alamat ?? '';
        $this->showForm = true;
    }

    public function save(SiswaService $service): void
    {
        $this->validate();
        $data = [
            'nis' => (int) $this->nis,
            'nama' => $this->nama,
            'kelas' => (int) $this->kelas,
            'jenis_kelamin' => $this->jenis_kelamin,
            'alamat' => $this->alamat,
        ];
        try {
            if ($this->editingId) {
                $service->update($this->editingId, $data);
                session()->flash('success', GlobalMessages::SUCCESS_UPDATE);
            } else {
                $service->create($data);
                session()->flash('success', GlobalMessages::SUCCESS_SAVE);
            }
            $this->showForm = false;
            $this->resetForm();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError('nis', $e->errors()['nis'][0] ?? GlobalMessages::ERROR_VALIDATION);
        }
    }

    public function delete(int $id, SiswaService $service): void
    {
        $service->delete($id);
        session()->flash('success', GlobalMessages::SUCCESS_DELETE);
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function openDetail(int $id): void
    {
        $this->detailSiswa = app(SiswaService::class)->findById($id);
        $this->showDetail = true;
        $this->dispatch('open-modal', 'detail-siswa');
    }

    public function closeDetail(): void
    {
        $this->detailSiswa = null;
        $this->showDetail = false;
        $this->dispatch('close-modal', 'detail-siswa');
    }

    public function openImport(): void
    {
        $this->importFile = null;
        $this->importErrors = [];
        $this->importedCount = null;
        $this->showImportModal = true;
    }

    public function processImport(SiswaService $service): void
    {
        $this->validate(['importFile' => 'required|file|mimes:csv,xlsx,xls|max:5120']);
        try {
            $result = $service->importFromFile($this->importFile);
            $this->importedCount = $result['imported'];
            $this->importErrors = $result['errors'];
            if ($result['imported'] > 0) {
                session()->flash('success', "Berhasil mengimpor {$result['imported']} data siswa.");
            }
            if (empty($result['errors'])) {
                $this->showImportModal = false;
            }
        } catch (\InvalidArgumentException $e) {
            $this->addError('importFile', $e->getMessage());
        }
    }

    public function openExport(SiswaService $service): void
    {
        $this->exportKelas = '';
        $this->exportJurusan = '';
        $this->exportPeriode = '';
        $this->exportPreviewCount = $service->getStats()['total'];
        $this->showExportModal = true;
    }

    public function updatingExportKelas(string $value): void
    {
        $this->refreshExportPreview($value, $this->exportJurusan, $this->exportPeriode);
    }

    public function updatingExportJurusan(string $value): void
    {
        $this->refreshExportPreview($this->exportKelas, $value, $this->exportPeriode);
    }

    public function updatingExportPeriode(string $value): void
    {
        $this->refreshExportPreview($this->exportKelas, $this->exportJurusan, $value);
    }

    public function exportCsv(SiswaService $service): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filters = [
            'kelas' => $this->exportKelas ?: null,
            'jurusan' => $this->exportJurusan ?: null,
            'periode_ajaran' => $this->exportPeriode ?: null,
        ];
        $csv = $service->exportToCsv($filters);
        $filename = 'data-siswa-' . now()->format('Ymd-His') . '.csv';
        $this->showExportModal = false;
        return response()->streamDownload(fn () => print($csv), $filename, ['Content-Type' => 'text/csv']);
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';
        $this->filterJenisKelamin = '';
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    private function refreshExportPreview(string $kelas, string $jurusan, string $periode): void
    {
        $service = app(SiswaService::class);
        $filters = [
            'kelas' => $kelas ?: null,
            'jurusan' => $jurusan ?: null,
            'periode_ajaran' => $periode ?: null,
            'per_page' => 99999,
        ];
        $this->exportPreviewCount = $service->getPaginated($filters)->total();
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->nis = '';
        $this->nama = '';
        $this->kelas = '';
        $this->jenis_kelamin = 'L';
        $this->editingId = null;
    }

    private function defaultPeriode(): string
    {
        $year = (int) now()->format('Y');
        $month = (int) now()->format('m');
        $startYear = $month >= 7 ? $year : $year - 1;
        return $startYear . '/' . ($startYear + 1);
    }
}
