<?php

namespace App\Livewire\Konselor\LayananKonseling;

use App\Services\BimbinganKelompokService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

class Kelompok extends Component
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
        $service = app(BimbinganKelompokService::class);
        $all = $service->getAll();

        $kelasOptions = $all->pluck('siswa')->flatten()->pluck('siswa.kelas_label')->filter()->unique()->sort()->values()->toArray();
        $jurusanOptions = $all->pluck('siswa')->flatten()->pluck('siswa.jurusan_label')->filter()->unique()->sort()->values()->toArray();
        $jenisKelaminOptions = $all->pluck('siswa')->flatten()->pluck('siswa.jenis_kelamin')->filter()->unique()->values()->toArray();

        $data = $all;

        if ($this->search) {
            $needle = (string) $this->search;
            $data = $data->filter(fn($item) => mb_stripos($item->uraian_masalah ?? '', $needle) !== false
                || mb_stripos($item->guruBk?->user?->nama ?? '', $needle) !== false);
        }

        if ($this->filterKelas !== '') {
            $data = $data->filter(function ($item) {
                return $item->siswa->contains(fn($peserta) => ($peserta->siswa->kelas_label ?? '') === $this->filterKelas);
            });
        }

        if ($this->filterJurusan !== '') {
            $data = $data->filter(function ($item) {
                return $item->siswa->contains(fn($peserta) => strcasecmp(($peserta->siswa->jurusan_label ?? ''), $this->filterJurusan) === 0);
            });
        }

        if ($this->filterJenisKelamin !== '') {
            $data = $data->filter(function ($item) {
                return $item->siswa->contains(fn($peserta) => strcasecmp(($peserta->siswa->jenis_kelamin ?? ''), $this->filterJenisKelamin) === 0);
            });
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
        $this->filterJenisKelamin = '';
        $this->selected = [];
        $this->selectAll = false;
    }

    public function create()
    {
        $this->dispatch('create-bimbingan-kelompok');
    }

    #[On('refreshTable')]
    public function refreshTable($id = null) {}

    public function goToDetail($id)
    {
        $this->redirectRoute('konselor.layanan-konseling.kelompok.detail', ['id' => $id], navigate: true);
    }

    public function edit($id)
    {
        $this->dispatch('edit-bimbingan-kelompok', id: (int) $id);
    }

    public function delete($id)
    {
        app(BimbinganKelompokService::class)->delete($id);
        $this->selected = array_diff($this->selected, [(string) $id]);
        session()->flash('success', 'Layanan konseling kelompok berhasil dihapus!');
    }
}
