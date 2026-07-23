<?php

namespace App\Livewire\Konselor\PengunduranDiri;

use App\Services\PengunduranDiriService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
    public string $filterKelas = '';
    public string $filterJurusan = '';
    public bool $showFilters = false;
    public array $selected = [];
    public bool $selectAll = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(PengunduranDiriService::class);

        $filters = [
            'search' => $this->search ?: null,
            'kelas' => $this->filterKelas ?: null,
            'jurusan' => $this->filterJurusan ?: null,
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

    public function resetFilters()
    {
        $this->search = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';
        $this->selected = [];
        $this->selectAll = false;
    }

    public function create()
    {
        $this->dispatch('create-pengunduran-diri');
    }

    #[On('refreshTable')]
    public function refreshTable($id = null) {}

    public function goToDetail($id)
    {
        $this->redirectRoute('konselor.pengunduran-diri.detail', ['id' => $id], navigate: true);
    }

    public function edit($id)
    {
        $this->dispatch('edit-pengunduran-diri', id: (int) $id);
    }

    public function delete($id)
    {
        app(PengunduranDiriService::class)->delete($id);
        $this->selected = array_diff($this->selected, [(string) $id]);
        session()->flash('success', 'Pengunduran diri berhasil dihapus!');
    }
}
