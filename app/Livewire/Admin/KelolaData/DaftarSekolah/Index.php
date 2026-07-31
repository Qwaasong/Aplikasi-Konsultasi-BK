<?php

namespace App\Livewire\Admin\KelolaData\DaftarSekolah;

use App\Services\SekolahService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
    public string $filterSekolah = '';
    public bool $showFilters = false;
    public array $selected = [];
    public bool $selectAll = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(SekolahService::class);

        $filters = [
            'search'       => $this->search ?: null,
            'nama_sekolah' => $this->filterSekolah ?: null,
        ];

        return [
            'records'        => $service->getFiltered($filters),
            'sekolahOptions' => $service->getSekolahOptions(),
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
        $this->dispatch('create-sekolah');
    }

    public function edit($id)
    {
        $this->dispatch('edit-sekolah', id: $id);
    }

    public function delete($id)
    {
        app(SekolahService::class)->delete($id);
        session()->flash('success', 'Data sekolah berhasil dihapus.');
        $this->selected = array_diff($this->selected, [(string) $id]);
    }

    public function filterAction()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function updatedSearch(): void
    {
        $this->selected = [];
    }

    public function updatedFilterSekolah(): void
    {
        $this->selected = [];
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterSekolah = '';
        $this->selected = [];
        $this->selectAll = false;
    }
}
