<?php

namespace App\Livewire\Konselor\Asesmen\GayaBelajar;

use App\Models\DataSiswa;
use App\Services\GayaBelajarService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

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

    public array $kelasOptions = [];
    public array $jurusanOptions = [];

    public $siswa_id = '';
    public $tanggal = '';

    public $visual = '';
    public $auditori = '';
    public $kinestetik = '';
    public $hasil = '';
    public $catatan = '';

    public array $files = [];
    public array $existingFiles = [];
    public array $newFiles = [];

    public function mount(): void
    {
        $this->records = collect();

        $this->students = DataSiswa::with([
            'user',
            'kelas.jurusan'
        ])
            ->get()
            ->sortBy(fn($student) => $student->nama ?? '')
            ->values();

        $this->loadData();
        $this->loadFilterOptions();
    }

    public function loadData(): void
    {
        $service = app(GayaBelajarService::class);

        $this->records = $service->getFiltered([
            'search' => $this->search,
            'kelas' => $this->filterKelas,
            'jurusan' => $this->filterJurusan,
        ]);
    }

    public function loadFilterOptions(): void
    {
        $service = app(GayaBelajarService::class);

        $options = $service->getFilterOptions();

        $this->kelasOptions = $options['kelasOptions'] ?? [];
        $this->jurusanOptions = $options['jurusanOptions'] ?? [];
    }

    public function nextStep(): void
    {
        $this->validate([
            'siswa_id' => 'required|integer',
            'tanggal' => 'required|date',
            'visual' => 'required|integer|min:0|max:100',
            'auditori' => 'required|integer|min:0|max:100',
            'kinestetik' => 'required|integer|min:0|max:100',
            'hasil' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $this->step = 2;
    }

    public function previousStep(): void
    {
        $this->step = 1;
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

    #[On('create-gaya-belajar')]
    public function createGayaBelajar(): void
    {
        $this->resetValidation();

        $this->reset([
            'siswa_id',
            'visual',
            'auditori',
            'kinestetik',
            'hasil',
            'catatan',
            'files',
            'existingFiles',
            'newFiles',
        ]);

        $this->editingId = null;
        $this->tanggal = now()->format('Y-m-d');
        $this->step = 1;
        $this->showStudentModal = false;

        $this->dispatch('open-modal', 'form-gaya-belajar');
    }

    #[On('edit-gaya-belajar')]
    public function loadGayaBelajar(int $id): void
    {
        $service = app(GayaBelajarService::class);

        $this->resetValidation();

        $record = $service->findById($id);

        $this->editingId = $id;

        $this->siswa_id = $record->siswa_id;
        $this->tanggal = optional($record->tanggal)->format('Y-m-d');

        $this->visual = $record->visual;
        $this->auditori = $record->auditori;
        $this->kinestetik = $record->kinestetik;
        $this->hasil = $record->hasil;
        $this->catatan = $record->catatan;

        $this->step = 1;

        $this->dispatch('open-modal', 'form-gaya-belajar');
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

    public function delete(int $id, GayaBelajarService $service): void
    {
        $service->delete($id);

        session()->flash(
            'success',
            'Data gaya belajar berhasil dihapus!'
        );

        $this->loadData();
    }

    public function save(GayaBelajarService $service): void
    {
        $this->validate([
            'siswa_id' => 'required|integer',
            'tanggal' => 'required|date',
            'visual' => 'required|integer|min:0|max:100',
            'auditori' => 'required|integer|min:0|max:100',
            'kinestetik' => 'required|integer|min:0|max:100',
            'hasil' => 'nullable|string',
            'catatan' => 'nullable|string',
            'files' => 'array|max:5',
            'files.*' => 'file|max:12288|mimes:pdf,jpg,jpeg,png,docx',
        ]);

        $data = [
            'siswa_id'   => $this->siswa_id,
            'tanggal'    => $this->tanggal,
            'visual'     => $this->visual,
            'auditori'   => $this->auditori,
            'kinestetik' => $this->kinestetik,
            'hasil'      => $this->hasil,
            'catatan'    => $this->catatan,
        ];

        if ($this->editingId) {

            $service->update($this->editingId, $data);

            session()->flash(
                'success',
                'Data gaya belajar berhasil diperbarui!'
            );

        } else {

            $service->create($data);

            session()->flash(
                'success',
                'Data gaya belajar berhasil ditambahkan!'
            );
        }

        $this->reset([
            'siswa_id',
            'visual',
            'auditori',
            'kinestetik',
            'hasil',
            'catatan',
            'files',
            'existingFiles',
            'newFiles',
        ]);

        $this->tanggal = now()->format('Y-m-d');
        $this->editingId = null;
        $this->step = 1;

        $this->dispatch('close-modal', 'form-gaya-belajar');
        $this->dispatch('refreshTable');
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

    public function filterAction(): void
    {
        $this->showFilters = !$this->showFilters;
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
        return redirect()->route('konselor.asesmen.gaya-belajar.detail',$id);
    }
}