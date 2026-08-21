<?php

namespace App\Livewire\Konselor\KehadiranSiswa;

use App\Models\DataSiswa;
use App\Models\TahunAjaran;
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
    public string $filterStatus = '';

    public ?string $selectedKelas = null;
    public string $selectedTanggal = '';
    public ?int $selectedTahunAjaranId = null;

    public bool $showFilters = false;

    public array $records = [];
    public array $kelasOptions = [];
    public array $tahunOptions = [];
    public array $attendance = [];

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
        $this->selectedTanggal = date('Y-m-d');
        $this->loadKelas();
        $this->loadTahunAjaranOptions();

        if (empty($this->selectedTahunAjaranId)) {
            $activeYear = TahunAjaran::where('status_aktif', true)->first();
            $this->selectedTahunAjaranId = $activeYear?->id;
        }
    }

    public function loadTahunAjaranOptions(): void
    {
        $this->tahunOptions = TahunAjaran::orderBy('tahun', 'desc')->get()->toArray();
    }

    /**
     * Mengambil daftar kelas
     */
    public function loadKelas(): void
    {
        $service = app(KehadiranService::class);

        // Fetch options using existing service logic or distinct from students
        $records = $service->getFiltered([
            'search' => null,
            'kelas' => null,
            'status' => null,
            'tanggal' => null,
            'tahun' => null,
        ]);

        $optionsFromKehadiran = $records
            ->map(fn ($item) => $item->siswa?->kelas_label)
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Also fetch from DataSiswa to show all classes even if no attendance
        $optionsFromSiswa = DataSiswa::with('kelas')->get()
            ->map(fn ($item) => $item->kelas_label)
            ->filter(fn ($label) => $label !== '-')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $this->kelasOptions = array_values(array_unique(array_merge($optionsFromKehadiran, $optionsFromSiswa)));
    }

    /**
     * Mengambil data siswa berdasarkan kelas untuk form kehadiran
     */
    public function loadData(): void
    {
        if (!$this->selectedKelas || !$this->selectedTahunAjaranId || !$this->selectedTanggal) {
            $this->records = [];
            return;
        }

        $query = DataSiswa::with(['user', 'kelas'])
            ->whereHas('kelas', fn($q) => $q->where('nama_kelas', $this->selectedKelas));

        if ($this->search) {
            $keyword = $this->search;
            $query->where(function($q) use ($keyword) {
                $q->whereHas('user', fn($q2) => $q2->where('nama', 'like', "%{$keyword}%"))
                  ->orWhere('nis', 'like', "%{$keyword}%");
            });
        }

        // Fetch students and their attendance for the specific date and year
        $students = $query->with(['kehadiran' => function($q) {
            $q->whereDate('tanggal_kehadiran', $this->selectedTanggal)
              ->where('tahun_ajaran_id', $this->selectedTahunAjaranId);
        }])->get();

        $this->records = [];
        $this->attendance = [];

        foreach ($students as $siswa) {
            $kehadiran = $siswa->kehadiran->first();
            $currentStatus = $kehadiran ? $kehadiran->status : '';

            // Skip jika ada filter status dan siswa tidak cocok
            if ($this->filterStatus && $currentStatus !== $this->filterStatus) {
                continue;
            }

            $this->records[] = [
                'id' => $siswa->id,
                'nis' => $siswa->nis,
                'nama' => $siswa->nama ?? '-',
                'kelas' => $siswa->kelas_label ?? '-',
            ];

            $this->attendance[$siswa->id] = $currentStatus;
        }
    }

    public function saveAttendance(int $siswaId, string $status): void
    {
        if (!$this->selectedTahunAjaranId || !$this->selectedTanggal) {
            return;
        }

        $service = app(KehadiranService::class);
        $service->upsertKehadiran($siswaId, $this->selectedTahunAjaranId, $this->selectedTanggal, $status);
        
        // Update local state
        $this->attendance[$siswaId] = $status;
    }

    public function updatedSelectedTanggal(): void
    {
        $this->loadData();
    }

    public function updatedSelectedTahunAjaranId(): void
    {
        $this->loadData();
    }

    public function updatedFilterStatus(): void
    {
        $this->loadData();
    }

    public function updatedSearch(): void
    {
        $this->loadData();
    }

    /**
     * Membuka data kehadiran berdasarkan kelas
     */
    public function pilihKelas(string $kelas): void
    {
        $this->selectedKelas = $kelas;
        $this->search = '';
        $this->loadData();
    }

    /**
     * Kembali ke daftar kelas
     */
    public function kembaliKeKelas(): void
    {
        $this->reset(['selectedKelas', 'records', 'attendance', 'search', 'filterStatus', 'showFilters']);
        $this->loadKelas();
    }

    public function filterAction(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->selectedTanggal = date('Y-m-d');
        $this->loadData();
    }

    // ── IMPORT METHODS ───────────────────────────────

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
            if ($this->selectedKelas) {
                $this->loadData();
            }
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
        ]);
    }

    public function exportCsv(KehadiranService $service, ImportExportService $ies): StreamedResponse
    {
        $rows = $service->exportRows([
            'kelas' => $this->selectedKelas,
        ]);

        $this->showExportModal = false;

        return $ies->streamCsv('kehadiran-' . date('Ymd-His') . '.csv', $service->getTemplateHeaders(), $rows);
    }

    public function exportExcel(KehadiranService $service, ImportExportService $ies): StreamedResponse
    {
        $rows = $service->exportRows([
            'kelas' => $this->selectedKelas,
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