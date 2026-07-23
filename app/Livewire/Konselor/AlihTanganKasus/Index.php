<?php

namespace App\Livewire\Konselor\AlihTanganKasus;

use App\Services\AlihTanganKasusService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
    public string $filterKelas = '';
    public string $filterJurusan = '';
    public string $filterTanggal = '';
    public bool $showFilters = false;
    public array $selected = [];
    public bool $selectAll = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(AlihTanganKasusService::class);
        $all = $service->getAll();

        $kelasOptions = $all->pluck('kasus.siswa.kelas_label')->filter()->unique()->sort()->values()->toArray();
        $jurusanOptions = $all->pluck('kasus.siswa.jurusan_label')->filter()->unique()->sort()->values()->toArray();

        $data = $all;

        if ($this->search) {
            $needle = (string) $this->search;
            $data = $data->filter(fn($item) => mb_stripos($item->kasus?->siswa?->nama ?? '', $needle) !== false
                || mb_stripos($item->alasan_alih ?? '', $needle) !== false);
        }

        if ($this->filterKelas !== '') {
            $data = $data->filter(fn($item) => (string) ($item->kasus?->siswa?->kelas_label ?? '') === (string) $this->filterKelas);
        }

        if ($this->filterJurusan !== '') {
            $data = $data->filter(fn($item) => strcasecmp(($item->kasus?->siswa?->jurusan_label ?? ''), $this->filterJurusan) === 0);
        }

        if ($this->filterTanggal !== '') {
            $data = $data->filter(fn($item) => \Carbon\Carbon::parse($item->tanggal_alih)->toDateString() === $this->filterTanggal);
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

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';
        $this->filterTanggal = '';
        $this->selected = [];
        $this->selectAll = false;
    }

    public function create()
    {
        $this->dispatch('create-alih-tangan-kasus');
    }

    #[On('refreshTable')]
    public function refreshTable($id = null) {}

    public function goToDetail($id)
    {
        $this->redirectRoute('konselor.alih-tangan-kasus.detail', ['id' => $id], navigate: true);
    }

    public function edit($id)
    {
        $this->dispatch('edit-alih-tangan-kasus', id: (int) $id);
    }

    public function delete($id)
    {
        app(AlihTanganKasusService::class)->delete($id);
        $this->selected = array_diff($this->selected, [(string) $id]);
        session()->flash('success', 'Alih tangan kasus berhasil dihapus!');
    }
}
