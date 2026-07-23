<?php

namespace App\Livewire\Konselor\LayananKonseling;

use App\Services\BimbinganIndividuService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

class IndividuDetail extends Component
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

        return app(BimbinganIndividuService::class)->search($this->search);
    }

    public function mount(int $id): void
    {
        $this->record = app(BimbinganIndividuService::class)->findById($id);
    }

    #[On('refreshTable')]
    public function refreshRecord()
    {
        if ($this->record) {
            $this->record = app(BimbinganIndividuService::class)->findById($this->record->id);
        }
    }

    public function goBack()
    {
        return redirect()->route('konselor.layanan-konseling.individu');
    }

    public function edit()
    {
        $this->dispatch('edit-bimbingan-individu', id: $this->record->id);
    }

    public function delete()
    {
        app(BimbinganIndividuService::class)->delete($this->record->id);
        session()->flash('success', 'Layanan konseling individu berhasil dihapus.');

        return redirect()->route('konselor.layanan-konseling.individu');
    }
}
