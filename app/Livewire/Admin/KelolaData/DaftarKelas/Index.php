<?php

namespace App\Livewire\Admin\KelolaData\DaftarKelas;

use App\Services\Bk\KelasService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
    public string $filterSekolah = '';
    public string $filterJurusan = '';
    public string $filterTingkat = '';
    public bool $showFilters = false;
    public array $selected = [];
    public bool $selectAll = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(KelasService::class);

        $filters = [
            'search' => $this->search ?: null,
            'sekolah' => $this->filterSekolah ?: null,
            'jurusan' => $this->filterJurusan ?: null,
            'tingkat' => $this->filterTingkat ?: null,
        ];

        $options = $service->getFilterOptions();

        return [
            'records' => $service->getFiltered($filters),
            'sekolahOptions' => $options['sekolahOptions'] ?? [],
            'jurusanOptions' => $options['jurusanOptions'] ?? [],
            'tingkatOptions' => $options['tingkatOptions'] ?? [],
        ];
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->with()['records']->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        $count = $this->with()['records']->count();
        $this->selectAll = count($this->selected) === $count && $count > 0;
    }

    public function create()
    {
        $this->dispatch('create-kelas');
    }

    public function edit($id)
    {
        $this->dispatch('edit-kelas', id: $id);
    }

    public function delete($id)
    {
        app(KelasService::class)->delete($id);
        session()->flash('success', 'Data kelas berhasil dihapus.');
        $this->selected = array_diff($this->selected, [(string) $id]);
    }

    public function filterAction()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterSekolah = '';
        $this->filterJurusan = '';
        $this->filterTingkat = '';
        $this->selected = [];
        $this->selectAll = false;
    }
}
