<?php

namespace App\Livewire\Admin\KelolaData\DaftarKelas;

use App\Services\KelasService;
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
        $all = $service->getAll();

        $sekolahOptions = $all->pluck('sekolah.nama_sekolah')->filter()->unique()->sort()->values()->toArray();
        $jurusanOptions = $all->pluck('jurusan.nama_jurusan')->filter()->unique()->sort()->values();
        $tingkatOptions = $all->pluck('tingkat')->filter()->unique()->sort()->values();

        $data = $all;

        if ($this->search) {
            $needle = strtolower($this->search);
            $data = $data->filter(function ($item) use ($needle) {
                return str_contains(strtolower($item->nama_kelas ?? ''), $needle)
                    || str_contains(strtolower($item->tingkat ?? ''), $needle)
                    || str_contains(strtolower($item->jurusan?->nama_jurusan ?? ''), $needle)
                    || str_contains(strtolower($item->jurusan?->sekolah?->nama_sekolah ?? ''), $needle)
                    || str_contains(strtolower($item->waliKelas?->user?->nama ?? ''), $needle);
            });
        }

        if ($this->filterSekolah) {
            $data = $data->filter(fn($item) => $item->jurusan?->sekolah?->nama_sekolah === $this->filterSekolah);
        }

        if ($this->filterJurusan) {
            $data = $data->filter(fn($item) => $item->jurusan?->nama_jurusan === $this->filterJurusan);
        }

        if ($this->filterTingkat) {
            $data = $data->filter(fn($item) => $item->tingkat === $this->filterTingkat);
        }

        return [
            'records' => $data->values(),
            'sekolahOptions' => $sekolahOptions,
            'jurusanOptions' => $jurusanOptions,
            'tingkatOptions' => $tingkatOptions,
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
