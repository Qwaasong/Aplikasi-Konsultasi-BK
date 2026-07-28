<?php

namespace App\Livewire\Konselor\KehadiranSiswa;

use App\Services\KehadiranService;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';

    public string $filterKelas = '';
    public string $filterStatus = '';
    public string $filterTanggal = '';
    public string $filterTahun = '';

    public bool $showFilters = false;

    public ?string $selectedKelas = null;

    public array $records = [];
    public array $kelasOptions = [];

    public function mount(): void
    {
        $this->loadKelas();
    }

    /**
     * Mengambil daftar kelas
     */
    public function loadKelas(): void
    {
        $service = app(KehadiranService::class);

        $records = $service->getFiltered([
            'search' => null,
            'kelas' => null,
            'status' => null,
            'tanggal' => null,
            'tahun' => null,
        ]);

        $this->kelasOptions = $records
            ->map(fn ($item) => $item->siswa?->kelas_label)
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Mengambil data kehadiran berdasarkan kelas
     */
    public function loadData(): void
    {
        if (!$this->selectedKelas) {
            $this->records = [];
            return;
        }

        $service = app(KehadiranService::class);

        $filters = [
            'search' => $this->search ?: null,
            'kelas' => $this->selectedKelas,
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

    /**
     * Membuka data kehadiran berdasarkan kelas
     */
    public function pilihKelas(string $kelas): void
    {
        $this->selectedKelas = $kelas;

        $this->search = '';
        $this->filterKelas = '';
        $this->filterStatus = '';
        $this->filterTanggal = '';
        $this->filterTahun = '';
        $this->showFilters = false;

        $this->loadData();
    }

    /**
     * Kembali ke daftar kelas
     */
    public function kembaliKeKelas(): void
    {
        $this->selectedKelas = null;

        $this->search = '';
        $this->filterKelas = '';
        $this->filterStatus = '';
        $this->filterTanggal = '';
        $this->filterTahun = '';

        $this->records = [];
        $this->showFilters = false;

        $this->loadKelas();
    }

    public function create(): void
    {
        $this->dispatch('create-kehadiran');
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterTanggal = '';
        $this->filterTahun = '';

        $this->loadData();
    }

    public function refreshData(): void
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterTanggal = '';
        $this->filterTahun = '';

        $this->loadKelas();

        if ($this->selectedKelas) {
            $this->loadData();
        }
    }

    public function filterAction(): void
    {
        $this->showFilters = !$this->showFilters;
    }
}