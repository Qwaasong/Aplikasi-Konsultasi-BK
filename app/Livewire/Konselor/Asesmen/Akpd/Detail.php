<?php

namespace App\Livewire\Konselor\Asesmen\Akpd;

use App\Services\Asesmen\AkpdService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

class Detail extends Component
{
    public $record;

    #[On('refreshTable')]
    public function refreshRecord(): void
    {
        if ($this->record) {
            $this->record = app(AkpdService::class)->findById($this->record->id);
        }
    }

    #[Computed]
    public function aspectGroups(): array
    {
        return $this->record->aspectAnswers();
    }

    public function mount(int $id): void
    {
        $this->record = app(AkpdService::class)->findById($id);

        abort_if(!$this->record, 404);
    }

    public function goBack()
    {
        return redirect()->route('konselor.asesmen.akpd.index');
    }
}
