<?php

namespace App\Livewire\Konselor\KasusBk;

use App\Livewire\KasusBkIndexBase;
use App\Services\KasusBkService;

class Index extends KasusBkIndexBase
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
        return 'konselor.kasus-bk.detail';
    }
}
