<?php

namespace App\Livewire\Konselor;

use App\Services\e\KasusBkService;
use App\Services\e\PegawaiService;
use Livewire\Volt\Component;

class Dashboard extends Component
{
    public function __construct()
    {
        parent::__construct();
    }
    public int $countKelas10 = 0;
    public int $countKelas11 = 0;
    public int $countKelas12 = 0;

    public function mount(KasusBkService $service): void
    {
        $pegawai = app(PegawaiService::class)->getCurrentPegawai();
        $counts = $service->getCaseCountsByGuruBk($pegawai->id);

        $this->countKelas10 = $counts['kelas_10'];
        $this->countKelas11 = $counts['kelas_11'];
        $this->countKelas12 = $counts['kelas_12'];
    }
}
