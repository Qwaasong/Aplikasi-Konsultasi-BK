<?php

namespace App\Livewire\Konselor\KonferensiKasus;

use App\Services\e\KonferensiKasusService;
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
            $this->record = app(KonferensiKasusService::class)->findById($this->record->id);
        }
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return [];
        }

        return app(KonferensiKasusService::class)->search($this->search);
    }

    public function mount(int $id): void
    {
        $this->record = app(KonferensiKasusService::class)->findById($id);
    }

    public function goBack()
    {
        return redirect()->route('konselor.konferensi-kasus.index');
    }

    public function edit()
    {
        $this->dispatch('edit-konferensi-kasus', id: (int) $this->record->id);
    }

    public function delete()
    {
        app(KonferensiKasusService::class)->delete($this->record->id);
        session()->flash('success', 'Konferensi kasus berhasil dihapus.');

        return redirect()->route('konselor.konferensi-kasus.index');
    }
}
