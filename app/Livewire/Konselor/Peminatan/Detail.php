<?php

namespace App\Livewire\Konselor\Peminatan;

use App\Services\Bk\PeminatanService;
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

        return app(PeminatanService::class)->search($this->search);
    }

    public function mount(int $id): void
    {
        $service = app(PeminatanService::class);
        $this->record = $service->findById($id);
    }

    public function goBack()
    {
        return redirect()->route('konselor.peminatan.index');
    }

    public function edit()
    {
        $this->dispatch('edit-peminatan', id: $this->record->id);
    }

    public function delete()
    {
        app(PeminatanService::class)->delete($this->record->id);
        session()->flash('success', 'Peminatan berhasil dihapus.');

        return redirect()->route('konselor.peminatan.index');
    }
}
