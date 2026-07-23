<?php

namespace App\Livewire\Admin\Konsultasi;

use App\Livewire\KonsultasiIndexBase;
use App\Services\KasusBkService;

class Index extends KonsultasiIndexBase
{
    protected function getData(): \Illuminate\Support\Collection
    {
        return app(KasusBkService::class)->all();
    }

    protected function deleteRecord(int $id): void
    {
        app(KasusBkService::class)->delete($id);
    }

    protected function getDetailRoute(): string
    {
        return 'admin.konsultasi.detail';
    }
}
