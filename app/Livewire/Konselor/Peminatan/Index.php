<?php

namespace App\Livewire\Konselor\Peminatan;

use App\Services\PeminatanService;
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
        $all = $service->getAll();

        $kelasOptions = $all->pluck('siswa.kelas_label')->filter()->unique()->sort()->values()->toArray();
        $jurusanOptions = $all->pluck('siswa.jurusan_label')->filter()->unique()->sort()->values()->toArray();

        $records = $all;

        if ($this->search) {
            $keyword = strtolower($this->search);
            $records = $records->filter(fn($item) => str_contains(strtolower($item->siswa->nama ?? ''), $keyword)
                || str_contains(strtolower($item->hasil ?? ''), $keyword));
        }

        if ($this->filterKelas !== '') {
            $records = $records->filter(fn($item) => (string) ($item->siswa->kelas_label ?? '') === $this->filterKelas);
        }

        if ($this->filterJurusan !== '') {
            $records = $records->filter(fn($item) => ($item->siswa->jurusan_label ?? '') === $this->filterJurusan);
        }

        return [
            'records' => $records->values(),
            'kelasOptions' => $kelasOptions,
            'jurusanOptions' => $jurusanOptions,
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
