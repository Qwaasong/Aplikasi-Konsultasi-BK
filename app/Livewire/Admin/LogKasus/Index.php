<?php

namespace App\Livewire\Admin\LogKasus;

use App\Services\KasusBkService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
    public string $filterStatus = '';
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

        $filters = [
            'search' => $this->search ?: null,
            'status' => $this->filterStatus ?: null,
        ];

        $options = $service->getFilterOptions();

        return [
            'records' => $service->getFiltered($filters),
            'statusOptions' => $options['statusOptions'] ?? [],
        ];
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->with()['records']->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        $count = $this->with()['records']->count();
        $this->selectAll = count($this->selected) === $count && $count > 0;
    }

    public function filterAction()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->selected = [];
        $this->selectAll = false;
    }

    public function goToDetail($id)
    {
        $this->redirectRoute('admin.log-kasus.detail', ['id' => $id], navigate: true);
    }
}
