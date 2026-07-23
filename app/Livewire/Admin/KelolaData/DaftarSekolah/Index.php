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
        $data = app(SekolahService::class)->getAll();

        if ($this->search) {
            $needle = strtolower($this->search);
            $data = $data->filter(function ($item) use ($needle) {
                return str_contains(strtolower($item->nama_sekolah ?? ''), $needle)
                    || str_contains(strtolower($item->alamat ?? ''), $needle)
                    || str_contains(strtolower($item->telepon ?? ''), $needle)
                    || str_contains(strtolower($item->email ?? ''), $needle);
            });
        }

        return [
            'records' => $data->values(),
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

    public function resetFilters()
    {
        $this->search = '';
        $this->filterSekolah = '';
        $this->selected = [];
        $this->selectAll = false;
    }
}
