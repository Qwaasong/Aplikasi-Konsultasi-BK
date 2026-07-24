<?php

namespace App\Livewire\Konselor\KunjunganRumah;

use App\Services\HomeVisitService;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

class Detail extends Component
{
    public $record;

    public string $search = '';

    public function __construct()
    {
        parent::__construct();
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return [];
        }

        return app(HomeVisitService::class)->search($this->search);
    }

    public function mount(int $id): void
    {
        $service = app(HomeVisitService::class);
        $this->record = $service->findById($id);
    }

    public function goBack()
    {
        return redirect()->route('konselor.kunjungan-rumah.index');
    }

    public function edit()
    {
        $this->dispatch('edit-home-visit', id: $this->record->id);
    }

    public function delete()
    {
        app(HomeVisitService::class)->delete($this->record->id);
        session()->flash('success', 'Kunjungan rumah berhasil dihapus.');

        return redirect()->route('konselor.kunjungan-rumah.index');
    }
}
