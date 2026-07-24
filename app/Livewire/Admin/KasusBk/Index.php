<?php

namespace App\Livewire\Admin\KasusBk;

use App\Livewire\Base\KasusBkIndexBase;
use App\Services\KasusBkService;

class Index extends KasusBkIndexBase
{
    protected function getFiltered(array $filters): \Illuminate\Support\Collection
    {
        return app(KasusBkService::class)->getFiltered($filters);
    }

    protected function getFilterOptions(): array
    {
        return app(KasusBkService::class)->getFilterOptions();
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
