<?php

namespace App\Livewire\Konselor\LayananKonseling;

use App\Models\BimbinganKelompok;
use App\Services\BimbinganKelompokService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

class KelompokDetail extends Component
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
            $this->record = app(BimbinganKelompokService::class)->findById($this->record->id);
        }
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return [];
        }

        return BimbinganKelompok::with('siswa.siswa.user')
            ->where('uraian_masalah', 'like', '%' . $this->search . '%')
            ->take(5)
            ->get();
    }

    public function mount(int $id): void
    {
        $this->record = app(BimbinganKelompokService::class)->findById($id);
    }

    public function goBack()
    {
        return redirect()->route('konselor.layanan-konseling.kelompok');
    }

    public function edit()
    {
        $this->dispatch('edit-bimbingan-kelompok', id: $this->record->id);
    }

    public function delete()
    {
        app(BimbinganKelompokService::class)->delete($this->record->id);
        session()->flash('success', 'Layanan konseling kelompok berhasil dihapus.');

        return redirect()->route('konselor.layanan-konseling.kelompok');
    }
}
