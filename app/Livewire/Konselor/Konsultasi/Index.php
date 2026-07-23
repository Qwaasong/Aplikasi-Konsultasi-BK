<?php

namespace App\Livewire\Konselor\Konsultasi;

use App\Livewire\KonsultasiIndexBase;
use App\Services\KasusBkService;

class Index extends KonsultasiIndexBase
{
    protected function getData(): \Illuminate\Support\Collection
    {
        return app(KasusBkService::class)->getByGurubk();
    }

    protected function deleteRecord(int $id): void
    {
        app(KasusBkService::class)->deleteForCurrentUser($id);
    }

    protected function getDetailRoute(): string
    {
        return 'konselor.konsultasi.detail';
    }
}
