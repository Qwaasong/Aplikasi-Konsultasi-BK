<?php

namespace App\Livewire\Konselor\KasusBk;

use App\Livewire\KasusBkIndexBase;
use App\Models\Pegawai;
use App\Services\KasusBkService;

class Index extends KasusBkIndexBase
{
    protected function getFiltered(array $filters): \Illuminate\Support\Collection
    {
        $pegawaiId = Pegawai::where('user_id', auth()->id())->value('id');
        $filters['guru_bk_id'] = $pegawaiId;

        return app(KasusBkService::class)->getFiltered($filters);
    }

    protected function getFilterOptions(): array
    {
        $service = app(KasusBkService::class);
        $all = $service->getByGurubk();

        return [
            'statusOptions' => $all->pluck('status')->filter()->unique()->sort()->values()->toArray(),
            'prioritasOptions' => $all->pluck('prioritas')->filter()->unique()->sort()->values()->toArray(),
            'kelasOptions' => $all->pluck('siswa.kelas_label')->filter()->unique()->sort()->values()->toArray(),
            'jurusanOptions' => $all->pluck('siswa.jurusan_label')->filter()->unique()->map(fn($j) => (string) $j)->sort()->values()->toArray(),
            'jenisKelaminOptions' => $all->pluck('siswa.jenis_kelamin')->filter()->unique()->values()->toArray(),
        ];
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
