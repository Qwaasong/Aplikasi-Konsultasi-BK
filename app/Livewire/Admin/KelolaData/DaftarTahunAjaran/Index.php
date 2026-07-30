<?php

namespace App\Livewire\Admin\KelolaData\DaftarTahunAjaran;

use App\Services\Bk\A\TahunAjaranService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

class Index extends Component
{
    public string $search = '';
    public array $selected = [];
    public bool $selectAll = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function with(): array
    {
        $service = app(TahunAjaranService::class);

        $filters = [
            'search' => $this->search ?: null,
        ];

        return [
            'records' => $service->getFiltered($filters),
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

    public function create()
    {
        $this->dispatch('create-tahun-ajaran');
    }

    public function edit($id)
    {
        $this->dispatch('edit-tahun-ajaran', id: $id);
    }

    public function delete($id)
    {
        app(TahunAjaranService::class)->delete($id);
        session()->flash('success', 'Data tahun ajaran berhasil dihapus.');
        $this->selected = array_diff($this->selected, [(string) $id]);
    }
}
