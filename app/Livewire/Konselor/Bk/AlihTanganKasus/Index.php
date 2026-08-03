<?php

namespace App\Livewire\Konselor\Bk\AlihTanganKasus;

use App\Services\Bk\AlihTanganKasusService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
    public string $filterKelas = '';
    public string $filterJurusan = '';
    public string $filterTanggal = '';
    public bool $showFilters = false;
    public array $selected = [];
    public bool $selectAll = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(AlihTanganKasusService::class);

        $filters = [
            'search' => $this->search ?: null,
            'kelas' => $this->filterKelas ?: null,
            'jurusan' => $this->filterJurusan ?: null,
            'tanggal' => $this->filterTanggal ?: null,
        ];

        $options = $service->getFilterOptions();

        return [
            'records' => $service->getFiltered($filters),
            'kelasOptions' => $options['kelasOptions'] ?? [],
            'jurusanOptions' => $options['jurusanOptions'] ?? [],
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
        $this->selectAll = (count($this->selected) === $recordsCount && $recordsCount > 0);
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
        $this->filterTanggal = '';
        $this->selected = [];
        $this->selectAll = false;
    }

    public function create()
    {
        $this->dispatch('create-alih-tangan-kasus');
    }

    #[On('refreshTable')]
    public function refreshTable($id = null) {}

    public function goToDetail($id)
    {
        $this->redirectRoute('konselor.alih-tangan-kasus.detail', ['id' => $id], navigate: true);
    }

    public function edit($id)
    {
        $this->dispatch('edit-alih-tangan-kasus', id: (int) $id);
    }

    public function delete($id)
    {
        app(AlihTanganKasusService::class)->delete($id);
        $this->selected = array_diff($this->selected, [(string) $id]);
        session()->flash('success', 'Alih tangan kasus berhasil dihapus!');
    }
}
