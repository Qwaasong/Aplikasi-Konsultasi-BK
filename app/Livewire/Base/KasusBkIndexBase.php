<?php

namespace App\Livewire\Base;

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

    abstract protected function getFiltered(array $filters): \Illuminate\Support\Collection;
    abstract protected function getFilterOptions(): array;
    abstract protected function deleteRecord(int $id): void;
    abstract protected function getDetailRoute(): string;

    public function with(): array
    {
        $filters = [
            'search' => $this->search ?: null,
            'status' => $this->filterStatus ?: null,
            'prioritas' => $this->filterPrioritas ?: null,
            'kelas' => $this->filterKelas ?: null,
            'jurusan' => $this->filterJurusan ?: null,
            'jenis_kelamin' => $this->filterJenisKelamin ?: null,
        ];

        $options = $this->getFilterOptions();

        return [
            'records' => $this->getFiltered($filters),
            'statusOptions' => $options['statusOptions'] ?? [],
            'prioritasOptions' => $options['prioritasOptions'] ?? [],
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
