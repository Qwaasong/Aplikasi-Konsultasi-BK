<?php

namespace App\Livewire\Konselor\Bk\Konsultasi;

use App\Services\Bk\KasusBkService;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

class Detail extends Component
{
    public function __construct()
    {
        parent::__construct();
    }
    public $record;

    public string $search = '';

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return [];
        }

        return app(KasusBkService::class)->search($this->search);
    }

    public function mount(int $id): void
    {
        $service = app(KasusBkService::class);
        $this->record = $service->findByIdForCurrentUser($id);
    }

    public function goBack()
    {
        return redirect()->to('/konselor/konsultasi');
    }

    public function edit()
    {
        $this->dispatch('edit-konsultasi', id: $this->record->id);
    }

    public function delete()
    {
        $service = app(KasusBkService::class);
        $service->deleteForCurrentUser($this->record->id);

        session()->flash('success', 'Konsultasi berhasil dihapus!');

        return redirect()->to('/konselor/konsultasi');
    }
}
