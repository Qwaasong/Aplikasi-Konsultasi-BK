<?php

namespace App\Livewire\Konselor\Asesmen\Akpd;

use App\Services\Asesmen\AkpdService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use App\Models\DataSiswa;
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

    public ?string $selectedTingkat = null;

    public array $kelasOptions = [];
    public array $jurusanOptions = [];

    public $siswa_id = '';
    public $tanggal = '';

    public $pribadi = '';
    public $sosial = '';
    public $belajar = '';
    public $karir = '';
    public $kesimpulan = '';
    public $catatan = '';

    public array $files = [];
    public array $existingFiles = [];
    public array $newFiles = [];


    public function mount(): void
    {
        $this->records = collect();

        $this->students = DataSiswa::with(['user', 'kelas.jurusan'])
            ->get()
            ->sortBy(fn ($student) => $student->nama ?? '')
            ->values();

        $this->loadData();
        $this->loadFilterOptions();
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
            'pribadi' => 'nullable|string',
            'sosial' => 'nullable|string',
            'belajar' => 'nullable|string',
            'karir' => 'nullable|string',
            'kesimpulan' => 'nullable|string',
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

    #[On('create-akpd')]
    public function createAkpd(): void
    {
        $this->resetValidation();

        $this->reset([
            'siswa_id',
            'pribadi',
            'sosial',
            'belajar',
            'karir',
            'kesimpulan',
            'catatan',
            'files',
            'existingFiles',
            'newFiles',
        ]);

        $this->editingId = null;
        $this->tanggal = now()->format('Y-m-d');
        $this->step = 1;
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

        $this->pribadi = $record->pribadi;
        $this->sosial = $record->sosial;
        $this->belajar = $record->belajar;
        $this->karir = $record->karir;
        $this->kesimpulan = $record->kesimpulan;
        $this->catatan = $record->catatan;

        $this->step = 1;

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
            'pribadi' => 'nullable|string',
            'sosial' => 'nullable|string',
            'belajar' => 'nullable|string',
            'karir' => 'nullable|string',
            'kesimpulan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'files' => 'array|max:5',
            'files.*' => 'file|max:12288|mimes:pdf,jpg,jpeg,png,docx',
        ]);

        $data = [
            'siswa_id' => $this->siswa_id,
            'tanggal' => $this->tanggal,
            'pribadi' => $this->pribadi,
            'sosial' => $this->sosial,
            'belajar' => $this->belajar,
            'karir' => $this->karir,
            'kesimpulan' => $this->kesimpulan,
            'catatan' => $this->catatan,
        ];

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
            'pribadi',
            'sosial',
            'belajar',
            'karir',
            'kesimpulan',
            'catatan',
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
};
?>