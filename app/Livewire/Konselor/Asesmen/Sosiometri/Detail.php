<?php

namespace App\Livewire\Konselor\Asesmen\Sosiometri;

use App\Models\Sosiometri;
use App\Services\Asesmen\SosiometriService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

class Detail extends Component
{
    public Sosiometri $sosiometri;

    public string $search = '';

    public function mount(int $id)
    {
        $sosiometri = app(SosiometriService::class)->findById($id);

        abort_if(!$sosiometri, 404);

        $this->sosiometri = $sosiometri;
    }

    #[Computed]
    public function questionGroups(): array
    {
        return $this->sosiometri->questionGroups();
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return collect();
        }

        return app(SosiometriService::class)->search($this->search);
    }

    public function goBack()
    {
        return redirect()->route('konselor.asesmen.sosiometri.index');
    }

    public function edit()
    {
        $this->dispatch('edit-sosiometri', id: $this->sosiometri->id);
    }

    #[On('refreshTable')]
    public function refreshRecord(): void
    {
        $this->sosiometri = app(SosiometriService::class)->findById($this->sosiometri->id);
    }

    public function delete()
    {
        app(SosiometriService::class)->delete($this->sosiometri->id);
        session()->flash('success', 'Data sosiometri berhasil dihapus.');
        return redirect()->route('konselor.asesmen.sosiometri.index');
    }
}
