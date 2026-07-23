<?php

namespace App\Livewire;

use App\Services\KasusBkService;
use Livewire\Volt\Component;

abstract class KasusBkIndexBase extends Component
{
    public string $search = '';
    public string $filterStatus = '';
    public string $filterPrioritas = '';
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

    abstract protected function getData(): \Illuminate\Support\Collection;
    abstract protected function deleteRecord(int $id): void;
    abstract protected function getDetailRoute(): string;

    public function with(): array
    {
        $all = $this->getData();

        $statusOptions = $all->pluck('status')->filter()->unique()->sort()->values()->toArray();
        $prioritasOptions = $all->pluck('prioritas')->filter()->unique()->sort()->values()->toArray();
        $kelasOptions = $all->pluck('siswa.kelas_label')->filter()->unique()->sort()->values()->toArray();
        $jurusanOptions = $all->pluck('siswa.jurusan_label')->filter()->unique()->map(fn($j) => (string) $j)->sort()->values()->toArray();
        $jenisKelaminOptions = $all->pluck('siswa.jenis_kelamin')->filter()->unique()->values()->toArray();

        $data = $all;

        if ($this->search) {
            $needle = (string) $this->search;
            $data = $data->filter(function ($item) use ($needle) {
                $name = (string) ($item->siswa->nama ?? 'Anonim');
                $judul = (string) ($item->judul ?? '');
                $desc = (string) ($item->deskripsi ?? '');
                return (mb_stripos($name, $needle) !== false)
                    || (mb_stripos($judul, $needle) !== false)
                    || (mb_stripos($desc, $needle) !== false);
            });
        }

        if ($this->filterStatus) {
            $data = $data->filter(fn($item) => $item->status === $this->filterStatus);
        }

        if ($this->filterPrioritas) {
            $data = $data->filter(fn($item) => $item->prioritas === $this->filterPrioritas);
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
            'statusOptions' => $statusOptions,
            'prioritasOptions' => $prioritasOptions,
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
        $this->dispatch('edit-kasus-bk', id: $id);
    }

    public function delete($id)
    {
        $this->deleteRecord($id);
        session()->flash('success', 'Kasus BK berhasil dihapus!');
        $this->selected = array_diff($this->selected, [(string) $id]);
    }

    public function goToDetail($id)
    {
        $this->redirectRoute($this->getDetailRoute(), ['id' => $id], navigate: true);
    }

    public function filterAction()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterPrioritas = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';
        $this->filterJenisKelamin = '';
        $this->selected = [];
        $this->selectAll = false;
    }
}
