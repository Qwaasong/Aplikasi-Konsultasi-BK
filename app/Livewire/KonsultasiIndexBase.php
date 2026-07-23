<?php

namespace App\Livewire;

use Livewire\Volt\Component;

abstract class KonsultasiIndexBase extends Component
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

    abstract protected function getFiltered(array $filters): \Illuminate\Support\Collection;
    abstract protected function getFilterOptions(): array;
    abstract protected function deleteRecord(int $id): void;
    abstract protected function getDetailRoute(): string;

    public function with(): array
    {
        $filters = [
            'search' => $this->search ?: null,
            'penanganan' => $this->filterJenisLayanan ?: null,
            'format' => $this->filterFormat ?: null,
            'kelas' => $this->filterKelas ?: null,
            'jurusan' => $this->filterJurusan ?: null,
            'jenis_kelamin' => $this->filterJenisKelamin ?: null,
        ];

        $options = $this->getFilterOptions();

        return [
            'records' => $this->getFiltered($filters),
            'layananOptions' => $options['layananOptions'] ?? [],
            'kelasOptions' => $options['kelasOptions'] ?? [],
            'jurusanOptions' => $options['jurusanOptions'] ?? [],
            'jenisKelaminOptions' => $options['jenisKelaminOptions'] ?? [],
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
        $this->deleteRecord($id);
        session()->flash('success', 'Konsultasi berhasil dihapus!');
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
        $this->filterJenisLayanan = '';
        $this->filterFormat = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';
        $this->filterJenisKelamin = '';
        $this->selected = [];
        $this->selectAll = false;
    }
}
