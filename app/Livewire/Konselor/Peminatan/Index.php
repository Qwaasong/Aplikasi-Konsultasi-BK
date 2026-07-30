<?php

namespace App\Livewire\Konselor\Peminatan;

use App\Services\e\PeminatanService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
    public bool $showFilters = false;
    public bool $selectAll = false;
    public string $filterKelas = '';
    public string $filterJurusan = '';
    public array $selected = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(PeminatanService::class);

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

    public function create()
    {
        $this->dispatch('create-peminatan');
    }

    public function edit($id)
    {
        $this->dispatch('edit-peminatan', id: $id);
    }

    public function delete($id)
    {
        app(PeminatanService::class)->delete($id);
        session()->flash('success', 'Data peminatan berhasil dihapus.');
    }

    public function goToDetail(int $id)
    {
        return $this->redirect(
            route('konselor.peminatan.detail', $id),
            navigate: true
        );
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
}
