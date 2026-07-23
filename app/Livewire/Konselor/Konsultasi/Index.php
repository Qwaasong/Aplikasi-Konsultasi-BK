<?php

namespace App\Livewire\Konselor\Konsultasi;

use App\Services\KasusBkService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
    public string $filterJenisLayanan = '';
    public string $filterFormat = '';
    public string $filterKelas = '';
    public string $filterJurusan = '';
    public string $filterJenisKelamin = '';
    public bool $showFilters = false;
    public array $selected = [];
    public bool $selectAll = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(KasusBkService::class);
        $all = $service->getByGurubk();

        $layananOptions = $all->pluck('penanganan')->filter()->unique()->sort()->values()->toArray();
        $kelasOptions = $all->pluck('siswa.kelas_label')->filter()->unique()->sort()->values()->toArray();
        $jurusanOptions = $all->pluck('siswa.jurusan_label')->filter()->unique()->map(fn($j) => (string) $j)->sort()->values()->toArray();
        $jenisKelaminOptions = $all->pluck('siswa.jenis_kelamin')->filter()->unique()->values()->toArray();

        $data = $all;

        if ($this->search) {
            $needle = (string) $this->search;
            $data = $data->filter(function ($item) use ($needle) {
                $name = (string) ($item->siswa->nama ?? 'Anonim');
                $jenis = (string) ($item->penanganan ?? '');
                $desc = (string) ($item->uraian_masalah ?? '');
                return (mb_stripos($name, $needle) !== false)
                    || (mb_stripos($jenis, $needle) !== false)
                    || (mb_stripos($desc, $needle) !== false);
            });
        }

        if ($this->filterJenisLayanan) {
            $data = $data->filter(fn($item) => $item->penanganan === $this->filterJenisLayanan);
        }

        if ($this->filterFormat) {
            $data = $data->filter(fn($item) => strtolower($item->penanganan ?? '') === strtolower($this->filterFormat));
        }

        if ($this->filterKelas !== '') {
            $data = $data->filter(fn($item) => (string) ($item->siswa->kelas_label ?? '') === (string) $this->filterKelas);
        }

        if ($this->filterJurusan !== '') {
            $data = $data->filter(fn($item) => strcasecmp(($item->siswa->jurusan_label ?? ''), $this->filterJurusan) === 0);
        }

        if ($this->filterJenisKelamin !== '') {
            $data = $data->filter(fn($item) => strcasecmp(($item->siswa->jenis_kelamin ?? ''), $this->filterJenisKelamin) === 0);
        }

        return [
            'records' => $data->values(),
            'layananOptions' => $layananOptions,
            'kelasOptions' => $kelasOptions,
            'jurusanOptions' => $jurusanOptions,
            'jenisKelaminOptions' => $jenisKelaminOptions,
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

    public function create()
    {
        $this->dispatch('create-konsultasi');
    }

    public function edit($id)
    {
        $this->dispatch('edit-konsultasi', id: $id);
    }

    public function delete($id)
    {
        $service = app(KasusBkService::class);
        $service->deleteForCurrentUser($id);
        session()->flash('success', 'data berhasil dihapus!');
        $this->selected = array_diff($this->selected, [(string) $id]);
    }

    public function filterAction()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterJenisLayanan = '';
        $this->filterFormat = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';
        $this->filterJenisKelamin = '';
        $this->selected = [];
        $this->selectAll = false;
    }
}
