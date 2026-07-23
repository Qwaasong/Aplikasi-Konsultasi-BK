<?php

namespace App\Livewire\Konselor\KehadiranSiswa;

use App\Services\KehadiranService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
    public string $filterKelas = '';
    public string $filterStatus = '';
    public string $filterTanggal = '';
    public string $filterTahun = '';
    public bool $showFilters = false;
    public array $records = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function mount()
    {
        $this->loadData();
    }

    public function create()
    {
        $this->dispatch('create-kehadiran');
    }

    public function loadData()
    {
        $records = app(KehadiranService::class)->getAll();

        if ($this->search !== '') {
            $records = $records->filter(fn($item) => str_contains(strtolower($item->siswa?->nama ?? ''), strtolower($this->search)));
        }
        if ($this->filterKelas !== '') {
            $records = $records->filter(fn($item) => ($item->siswa?->kelas_label ?? '') === $this->filterKelas);
        }
        if ($this->filterStatus !== '') {
            $records = $records->filter(fn($item) => $item->status === $this->filterStatus);
        }
        if ($this->filterTanggal !== '') {
            $records = $records->filter(fn($item) => $item->tanggal_kehadiran == $this->filterTanggal);
        }
        if ($this->filterTahun !== '') {
            $records = $records->filter(fn($item) => (string) ($item->tahunAjaran?->tahun ?? '') === (string) $this->filterTahun);
        }

        $this->records = $records->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->siswa?->nama ?? '-',
                'kelas' => $item->siswa?->kelas_label ?? '-',
                'tanggal' => $item->tanggal_kehadiran,
                'status' => $item->status,
                'tahun' => $item->tahunAjaran?->tahun ?? '-',
            ];
        })->toArray();
    }

    public function updated()
    {
        $this->loadData();
    }

    public function resetFilter()
    {
        $this->search = '';
        $this->filterKelas = '';
        $this->filterStatus = '';
        $this->filterTanggal = '';
        $this->filterTahun = '';
        $this->loadData();
    }

    public function refreshData()
    {
        $this->search = '';
        $this->filterKelas = '';
        $this->filterStatus = '';
        $this->filterTanggal = '';
        $this->filterTahun = '';
        $this->loadData();
    }

    public function filterAction()
    {
        $this->showFilters = !$this->showFilters;
    }
}
