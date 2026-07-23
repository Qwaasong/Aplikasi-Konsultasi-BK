<?php

namespace App\Livewire\Konselor\KonferensiKasus;

use App\Services\KonferensiKasusService;
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
        $service = app(KonferensiKasusService::class);

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

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';
        $this->selected = [];
        $this->selectAll = false;
    }

    public function create()
    {
        $this->dispatch('create-konferensi-kasus');
    }

    #[On('refreshTable')]
    public function refreshTable($id = null) {}

    public function goToDetail($id)
    {
        $this->redirectRoute('konselor.konferensi-kasus.detail', ['id' => $id], navigate: true);
    }

    public function edit($id)
    {
        $this->dispatch('edit-konferensi-kasus', id: (int) $id);
    }

    public function delete($id)
    {
        app(KonferensiKasusService::class)->delete($id);
        $this->selected = array_diff($this->selected, [(string) $id]);
        session()->flash('success', 'Konferensi kasus berhasil dihapus!');
    }
}
