<?php

namespace App\Livewire\Admin\KelolaUser\Siswa;

use App\Services\Siswa\SiswaService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
    public string $filterKelas = '';
    public string $filterJurusan = '';
    public string $filterJenisKelamin = '';
    public bool $showFilters = false;
    public array $selected = [];
    public bool $selectAll = false;

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
}
