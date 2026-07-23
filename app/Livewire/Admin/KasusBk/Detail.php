<?php

namespace App\Livewire\Admin\KasusBk;

use App\Services\KasusBkService;
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
        $this->record = app(KasusBkService::class)->findById($id);
    }

    public function goBack()
    {
        return redirect()->to('/admin/kasus-bk');
    }

    public function edit()
    {
        $this->dispatch('edit-kasus-bk', id: $this->record->id);
    }

    public function delete()
    {
        $service = app(KasusBkService::class);
        $service->deleteForCurrentUser($this->record->id);

        session()->flash('success', 'Kasus BK berhasil dihapus!');

        return redirect()->to('/admin/kasus-bk');
    }
}
