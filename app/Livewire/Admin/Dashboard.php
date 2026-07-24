<?php

namespace App\Livewire\Admin;

use App\Services\KasusBkService;
use App\Services\SiswaService;
use App\Services\UserService;
use Livewire\Volt\Component;

class Dashboard extends Component
{
    public int $totalUsers = 0;
    public int $totalSiswa = 0;
    public int $totalKasus = 0;
    public int $totalKonselor = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function mount(): void
    {
        $userService = app(UserService::class);
        $siswaService = app(SiswaService::class);
        $kasusService = app(KasusBkService::class);

        $stats = $userService->getStats();
        $this->totalUsers = $stats['total'] ?? 0;
        $this->totalKonselor = $stats['konselor'] ?? 0;
        $this->totalSiswa = $siswaService->getTotalSiswa();
        $this->totalKasus = $kasusService->countKasus();
    }
}
