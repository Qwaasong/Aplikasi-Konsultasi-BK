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
        $service = app(KehadiranService::class);

        $filters = [
            'search' => $this->search ?: null,
            'kelas' => $this->filterKelas ?: null,
            'status' => $this->filterStatus ?: null,
            'tanggal' => $this->filterTanggal ?: null,
            'tahun' => $this->filterTahun ?: null,
        ];

        $records = $service->getFiltered($filters);

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
