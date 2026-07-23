<?php

namespace App\Livewire\Konselor\KunjunganRumah;

use App\Services\HomeVisitService;
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
        $service = app(HomeVisitService::class);
        $all = $service->getAll();

        $kelasOptions = $all->pluck('kasus.siswa.kelas_label')->filter()->unique()->sort()->values()->toArray();
        $jurusanOptions = $all->pluck('kasus.siswa.jurusan_label')->filter()->unique()->sort()->values()->toArray();

        $data = $all;

        if ($this->search) {
            $needle = (string) $this->search;
            $data = $data->filter(fn($item) => mb_stripos($item->kasus?->siswa?->nama ?? '', $needle) !== false
                || mb_stripos($item->kasus?->siswa?->nis ?? '', $needle) !== false
                || mb_stripos($item->penanganan ?? '', $needle) !== false);
        }

        if ($this->filterKelas !== '') {
            $data = $data->filter(fn($item) => (string) ($item->kasus?->siswa?->kelas_label ?? '') === (string) $this->filterKelas);
        }

        if ($this->filterJurusan !== '') {
            $data = $data->filter(fn($item) => strcasecmp($item->kasus?->siswa?->jurusan_label ?? '', $this->filterJurusan) === 0);
        }

        return [
            'records' => $data->values(),
            'kelasOptions' => $kelasOptions,
            'jurusanOptions' => $jurusanOptions,
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
        $this->dispatch('create-home-visit');
    }

    #[On('refreshTable')]
    public function refreshTable($id = null) {}

    public function goToDetail($id)
    {
        $this->redirectRoute('konselor.home-visit.detail', ['id' => $id], navigate: true);
    }

    public function edit($id)
    {
        $this->dispatch('edit-home-visit', id: (int) $id);
    }

    public function delete($id)
    {
        app(HomeVisitService::class)->delete($id);
        $this->selected = array_diff($this->selected, [(string) $id]);
        session()->flash('success', 'Kunjungan rumah berhasil dihapus!');
    }
}
