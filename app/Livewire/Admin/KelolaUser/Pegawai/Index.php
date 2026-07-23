<?php

namespace App\Livewire\Admin\KelolaUser\Pegawai;

use App\Services\PegawaiService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
    public string $filterJabatan = '';
    public bool $showFilters = false;
    public array $selected = [];
    public bool $selectAll = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(PegawaiService::class);
        $all = $service->getAll();
        $jabatanOptions = $all->pluck('jabatan')->filter()->unique()->sort()->values()->toArray();

        $data = $all;

        if ($this->search) {
            $needle = strtolower($this->search);
            $data = $data->filter(function ($item) use ($needle) {
                return str_contains(strtolower($item->user?->nama ?? ''), $needle)
                    || str_contains(strtolower($item->nip ?? ''), $needle)
                    || str_contains(strtolower($item->jabatan ?? ''), $needle);
            });
        }

        if ($this->filterJabatan) {
            $data = $data->where('jabatan', $this->filterJabatan);
        }

        return [
            'records' => $data->values(),
            'jabatanOptions' => $jabatanOptions,
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
        $this->dispatch('create-pegawai');
    }

    public function edit($id)
    {
        $this->dispatch('edit-pegawai', id: $id);
    }

    public function delete($id)
    {
        app(PegawaiService::class)->delete($id);
        session()->flash('success', 'Data pegawai berhasil dihapus.');
        $this->selected = array_diff($this->selected, [(string) $id]);
    }

    public function filterAction()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterJabatan = '';
        $this->selected = [];
        $this->selectAll = false;
    }
}
