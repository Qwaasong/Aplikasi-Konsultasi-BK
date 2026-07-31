<?php

namespace App\Livewire\Konselor\Asesmen\TesBakatMinat;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Peminatan;
use App\Services\PeminatanService;

#[Layout('layouts.app')]
class Detail extends Component
{
    public Peminatan $record;

    public string $search = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    protected PeminatanService $service;

    public function boot(PeminatanService $service): void
    {
        $this->service = $service;
    }

    public function mount(int $id): void
    {
        $this->record = $this->service->findById($id);
    }

    public function getSearchResultsProperty()
    {
        if (strlen($this->search) < 2) {
            return collect();
        }

        return Peminatan::with('siswa')
            ->whereHas('siswa', function ($query) {
                $query->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('nis', 'like', "%{$this->search}%");
            })
            ->latest()
            ->limit(8)
            ->get();
    }

    public function edit(): void
    {
        $this->editingId = $this->record->id;

        $this->dispatch('loadTesBakatMinat', id: $this->record->id);

        $this->showModal = true;
    }

    public function delete(): void
    {
        $this->service->delete($this->record->id);

        session()->flash(
            'success',
            'Data tes bakat minat berhasil dihapus.'
        );

        $this->redirectRoute(
            'konselor.asesmen.tes-bakat-minat.index',
            navigate: true
        );
    }

    public function goBack(): void
    {
        $this->redirectRoute(
            'konselor.asesmen.tes-bakat-minat.index',
            navigate: true
        );
    }

    protected $listeners = [
        'refreshDetail' => '$refresh',
    ];

    public function render()
    {
        return view(
            'livewire.pages.konselor.asesmen.tes-bakat-minat.detail'
        );
    }
}