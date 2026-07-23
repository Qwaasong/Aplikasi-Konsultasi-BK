<?php

namespace App\Livewire\Admin\KelolaData\DaftarJurusan;

use App\Services\JurusanService;
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
        $service = app(JurusanService::class);
        $all = $service->getAll();
        $sekolahOptions = $all->pluck('sekolah.nama_sekolah')->filter()->unique()->sort()->values()->toArray();

        $data = $all;

        if ($this->search) {
            $needle = strtolower($this->search);
            $data = $data->filter(function ($item) use ($needle) {
                return str_contains(strtolower($item->kode_jurusan ?? ''), $needle)
                    || str_contains(strtolower($item->nama_jurusan ?? ''), $needle)
                    || str_contains(strtolower($item->sekolah?->nama_sekolah ?? ''), $needle);
            });
        }

        if ($this->filterSekolah) {
            $data = $data->filter(fn($item) => $item->sekolah?->nama_sekolah === $this->filterSekolah);
        }

        return [
            'records' => $data->values(),
            'sekolahOptions' => $sekolahOptions,
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
        $this->dispatch('create-jurusan');
    }

    public function edit($id)
    {
        $this->dispatch('edit-jurusan', id: $id);
    }

    public function delete($id)
    {
        app(JurusanService::class)->delete($id);
        session()->flash('success', 'Data jurusan berhasil dihapus.');
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
