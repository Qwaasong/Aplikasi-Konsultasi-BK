<?php

namespace App\Livewire\Konselor\KunjunganRumah;

use App\Models\HomeVisit;
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

        // FIX: Gunakan HomeVisit alih-alih Konsultasi yang tidak ada
        return HomeVisit::with(['kasus.siswa.user', 'guruBk.user'])
            ->whereHas('kasus.siswa.user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            })
            ->take(5)
            ->get();
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
