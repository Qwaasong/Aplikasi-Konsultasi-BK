<?php

namespace App\Livewire\Konselor\Asesmen\Dcm;

use App\Services\DcmService;
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
    public function refreshRecord(): void
    {
        if ($this->record) {
            $this->record = app(DcmService::class)
                ->findById($this->record->id);
        }
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return [];
        }

        return app(DcmService::class)
            ->search($this->search);
    }

    public function mount(int $id): void
    {
        $this->record = app(DcmService::class)
            ->findById($id);

        abort_if(!$this->record, 404);
    }

    public function goBack()
    {
        return redirect()->route('konselor.asesmen.dcm.index');
    }

    public function edit()
    {
        $this->dispatch(
            'edit-dcm',
            id: (int) $this->record->id
        );
    }

    public function delete()
    {
        app(DcmService::class)
            ->delete($this->record->id);

        session()->flash(
            'success',
            'Data DCM berhasil dihapus.'
        );

        return redirect()
            ->route('konselor.asesmen.dcm.index');
    }
}