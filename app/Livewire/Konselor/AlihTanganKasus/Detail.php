<?php

namespace App\Livewire\Konselor\AlihTanganKasus;

use App\Models\AlihtanganKasus;
use App\Services\AlihTanganKasusService;
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
            $this->record = app(AlihTanganKasusService::class)->findById($this->record->id);
        }
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return [];
        }

        return AlihtanganKasus::with('kasus.siswa.user')
            ->whereHas('kasus.siswa.user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            })
            ->take(5)
            ->get();
    }

    public function mount(int $id): void
    {
        $this->record = app(AlihTanganKasusService::class)->findById($id);
    }

    public function goBack()
    {
        return redirect()->route('konselor.alih-tangan-kasus.index');
    }

    public function edit()
    {
        $this->dispatch('edit-alih-tangan-kasus', id: (int) $this->record->id);
    }

    public function delete()
    {
        app(AlihTanganKasusService::class)->delete($this->record->id);
        session()->flash('success', 'Alih tangan kasus berhasil dihapus.');

        return redirect()->route('konselor.alih-tangan-kasus.index');
    }
}
