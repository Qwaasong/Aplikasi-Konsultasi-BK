<?php

namespace App\Livewire\Konselor\Asesmen\GayaBelajar;

use App\Services\GayaBelajarService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

class Detail extends Component
{
    /**
     * Record gaya belajar yang sedang dibuka
     */
    public $record;

    /**
     * Search pada header
     */
    public string $search = '';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Refresh data setelah edit
     */
    #[On('refreshTable')]
    public function refreshRecord(): void
    {
        if ($this->record) {

            $this->record = app(GayaBelajarService::class)
                ->findById($this->record->id);

        }
    }

    /**
     * Live Search
     */
    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return [];
        }

        return app(GayaBelajarService::class)
            ->search($this->search);
    }

    /**
     * Load pertama
     */
    public function mount(int $id): void
    {
        $this->record = app(GayaBelajarService::class)
            ->findById($id);

        abort_if(!$this->record, 404);
    }

    /**
     * Kembali ke halaman index
     */
    public function goBack()
    {
        return redirect()->route(
            'konselor.asesmen.gaya-belajar.index'
        );
    }

    /**
     * Edit data
     */
    public function edit()
    {
        $this->dispatch(
            'edit-gaya-belajar',
            id: (int) $this->record->id
        );
    }

    /**
     * Hapus data
     */
    public function delete()
    {
        app(GayaBelajarService::class)
            ->delete($this->record->id);

        session()->flash(
            'success',
            'Data gaya belajar berhasil dihapus.'
        );

        return redirect()->route(
            'konselor.asesmen.gaya-belajar.index'
        );
    }
}