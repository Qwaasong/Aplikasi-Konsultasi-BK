<?php

namespace App\Livewire\Konselor\Asesmen\Dcm;

use App\Models\DataSiswa;
use App\Services\DcmService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

class Index extends Component
{
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
    public string $masalah_teridentifikasi_text = '';
    public string $filterKelas = '';
    public string $filterJurusan = '';

    public array $kelasOptions = [];
    public array $jurusanOptions = [];

    public $siswa_id = '';
    public $tanggal = '';

    public array $masalah_teridentifikasi = [];

    public $kesimpulan = '';
    public $catatan = '';

    public array $files = [];
    public array $existingFiles = [];
    public array $newFiles = [];

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
        ]);
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
            'masalah_teridentifikasi',
            'kesimpulan',
            'catatan',
            'masalah_teridentifikasi_text',
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

        $this->masalah_teridentifikasi =
            $record->masalah_teridentifikasi ?? [];

        $this->masalah_teridentifikasi_text = implode("\n", $this->masalah_teridentifikasi);
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
            'masalah_teridentifikasi' => 'nullable|array',
            'kesimpulan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $data = [
            'siswa_id' => $this->siswa_id,
            'tanggal' => $this->tanggal,
            'masalah_teridentifikasi' =>
                $this->masalah_teridentifikasi,
            'kesimpulan' => $this->kesimpulan,
            'catatan' => $this->catatan,
        ];

        $data['masalah_teridentifikasi'] = array_values(
            array_filter(
                array_map(
                    'trim',
                    preg_split('/\r\n|\r|\n/', $this->masalah_teridentifikasi_text)
                )
            )
        );

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
            'masalah_teridentifikasi',
            'masalah_teridentifikasi_text',
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
}