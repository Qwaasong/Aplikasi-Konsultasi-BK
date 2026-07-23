<?php

namespace App\Livewire\Admin\LogKasus;

use App\Models\KasusBk;
use App\Services\KasusBkService;
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

        return KasusBk::with(['siswa.user', 'guruBk.user', 'kategori', 'lampirans'])
            ->where('uraian_masalah', 'like', '%' . $this->search . '%')
            ->take(5)
            ->get();
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
