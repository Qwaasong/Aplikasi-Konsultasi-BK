<?php

namespace App\Livewire\Admin\KasusBk;

use App\Livewire\KasusBkIndexBase;
use App\Services\KasusBkService;

class Index extends KasusBkIndexBase
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
        return 'admin.kasus-bk.detail';
    }
}
