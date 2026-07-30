<?php

namespace App\Livewire\Admin\Bk\LogKasus;

use App\Services\Bk\KasusBkService;
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
            $this->record = app(KasusBkService::class)->findById($this->record->id);
        }
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return [];
        }

        return app(KasusBkService::class)->search($this->search);
    }

    public function mount(int $id): void
    {
        $this->record = app(KasusBkService::class)->findById($id);
    }

    public function goBack()
    {
        return redirect()->route('admin.log-kasus.index');
    }
}
