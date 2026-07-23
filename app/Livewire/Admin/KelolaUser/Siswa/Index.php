<?php

namespace App\Livewire\Admin\KelolaUser\Siswa;

use App\Services\SiswaService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
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
        $service = app(SiswaService::class);
        $all = $service->getAll();

        $kelasOptions = $all->pluck('kelas_label')->filter()->unique()->sort()->values()->toArray();
        $jurusanOptions = $all->pluck('jurusan_label')->filter()->unique()->sort()->values()->toArray();
        $jenisKelaminOptions = $all->pluck('jenis_kelamin')->filter()->unique()->values()->toArray();

        $data = $all;

        if ($this->search) {
            $needle = strtolower($this->search);
            $data = $data->filter(function ($item) use ($needle) {
                return str_contains(strtolower($item->nama ?? ''), $needle)
                    || str_contains((string) $item->nis, $needle)
                    || str_contains(strtolower($item->kelas_label ?? ''), $needle)
                    || str_contains(strtolower($item->jurusan_label ?? ''), $needle);
            });
        }

        if ($this->filterKelas) {
            $data = $data->where('kelas_label', $this->filterKelas);
        }

        if ($this->filterJurusan) {
            $data = $data->where('jurusan_label', $this->filterJurusan);
        }

        if ($this->filterJenisKelamin) {
            $data = $data->where('jenis_kelamin', $this->filterJenisKelamin);
        }

        return [
            'records' => $data->values(),
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
        $this->selectAll = count($this->selected) === $recordsCount && $recordsCount > 0;
    }

    public function create()
    {
        $this->dispatch('create-siswa');
    }

    public function edit($id)
    {
        $this->dispatch('edit-siswa', id: $id);
    }

    public function delete($id)
    {
        $service = app(SiswaService::class);
        $service->delete($id);
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
        $this->filterKelas = '';
        $this->filterJurusan = '';
        $this->filterJenisKelamin = '';
        $this->selected = [];
        $this->selectAll = false;
    }
}
