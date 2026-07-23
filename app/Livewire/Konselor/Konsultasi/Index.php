<?php

namespace App\Livewire\Konselor\Konsultasi;

use App\Livewire\KonsultasiIndexBase;
use App\Models\Pegawai;
use App\Services\KasusBkService;

class Index extends KonsultasiIndexBase
{
    protected function getFiltered(array $filters): \Illuminate\Support\Collection
    {
        $service = app(KasusBkService::class);
        $all = $service->getByGurubk();

        return $this->applyFilters($all, $filters);
    }

    protected function getFilterOptions(): array
    {
        $all = app(KasusBkService::class)->getByGurubk();

        return [
            'layananOptions' => $all->pluck('penanganan')->filter()->unique()->sort()->values()->toArray(),
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
        return 'konselor.konsultasi.detail';
    }

    private function applyFilters($data, array $filters): \Illuminate\Support\Collection
    {
        if (!empty($filters['search'])) {
            $needle = $filters['search'];
            $data = $data->filter(fn($item) => mb_stripos($item->siswa->nama ?? '', $needle) !== false
                || mb_stripos($item->penanganan ?? '', $needle) !== false
                || mb_stripos($item->uraian_masalah ?? '', $needle) !== false);
        }

        if (!empty($filters['penanganan'])) {
            $data = $data->filter(fn($item) => $item->penanganan === $filters['penanganan']);
        }

        if (!empty($filters['format'])) {
            $data = $data->filter(fn($item) => strtolower($item->penanganan ?? '') === strtolower($filters['format']));
        }

        if (!empty($filters['kelas'])) {
            $data = $data->filter(fn($item) => (string) ($item->siswa->kelas_label ?? '') === (string) $filters['kelas']);
        }

        if (!empty($filters['jurusan'])) {
            $data = $data->filter(fn($item) => strcasecmp(($item->siswa->jurusan_label ?? ''), $filters['jurusan']) === 0);
        }

        if (!empty($filters['jenis_kelamin'])) {
            $data = $data->filter(fn($item) => strcasecmp(($item->siswa->jenis_kelamin ?? ''), $filters['jenis_kelamin']) === 0);
        }

        return $data->values();
    }
}
