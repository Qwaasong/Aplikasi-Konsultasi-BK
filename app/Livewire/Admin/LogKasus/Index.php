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
        $all = $service->all();

        $statusOptions = $all->pluck('status')->filter()->unique()->sort()->values()->toArray();

        $data = $all;

        if ($this->search) {
            $needle = strtolower($this->search);
            $data = $data->filter(function ($item) use ($needle) {
                return str_contains(strtolower($item->judul ?? ''), $needle)
                    || str_contains(strtolower($item->siswa?->nama ?? ''), $needle);
            });
        }

        if ($this->filterStatus) {
            $data = $data->where('status', $this->filterStatus);
        }

        return [
            'records' => $data->values(),
            'statusOptions' => $statusOptions,
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
}
