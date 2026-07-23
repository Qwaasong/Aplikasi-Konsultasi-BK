<?php

namespace App\Livewire\Admin\Konsultasi;

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
        $this->record = KasusBk::with(['siswa.kelas.jurusan', 'lampirans', 'guruBk.user'])
            ->findOrFail($id);
    }

    public function goBack()
    {
        return redirect()->to('/admin/konsultasi');
    }

    public function edit()
    {
        $this->dispatch('edit-konsultasi', id: $this->record->id);
    }

    public function delete()
    {
        $service = app(KasusBkService::class);
        $service->delete($this->record->id);

        session()->flash('success', 'Konsultasi berhasil dihapus!');

        return redirect()->to('/admin/konsultasi');
    }
}
