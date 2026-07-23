<?php

namespace App\Livewire\Konselor\KasusBk;

use App\Models\KasusBk;
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

        return KasusBk::with('siswa')
            ->where(function ($query) {
                $query->whereHas('siswa.user', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('penanganan', 'like', '%' . $this->search . '%');
            })
            ->take(5)
            ->get();
    }

    public function mount(int $id): void
    {
        $service = app(KasusBkService::class);
        $this->record = $service->findByIdForCurrentUser($id);
    }

    public function goBack()
    {
        return redirect()->to('/konselor/kasus-bk');
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

        return redirect()->to('/konselor/kasus-bk');
    }
}
