<?php

namespace App\Livewire\Konselor\Bk\PengunduranDiri;

use App\Services\Bk\PengunduranDiriService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

class Detail extends Component
{
    public $record;

    public string $search = '';

    public function __construct()
    {
        parent::__construct();
    }

    #[On('refreshTable')]
    public function refreshRecord()
    {
        if ($this->record) {
            $this->record = app(PengunduranDiriService::class)->findById($this->record->id);
        }
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return [];
        }

        return app(PengunduranDiriService::class)->search($this->search);
    }

    public function mount(int $id): void
    {
        $this->record = app(PengunduranDiriService::class)->findById($id);
    }

    public function goBack()
    {
        return redirect()->route('konselor.pengunduran-diri.index');
    }

    public function edit()
    {
        $this->dispatch('edit-pengunduran-diri', id: (int) $this->record->id);
    }

    public function delete()
    {
        app(PengunduranDiriService::class)->delete($this->record->id);
        session()->flash('success', 'Pengunduran diri berhasil dihapus.');

        return redirect()->route('konselor.pengunduran-diri.index');
    }
}
