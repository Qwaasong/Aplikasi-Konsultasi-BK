<?php

namespace App\Livewire\Konselor;

use App\Models\Pegawai;
use App\Services\KasusBkService;
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
        $pegawaiId = Pegawai::where('user_id', auth()->id())->value('id');
        $counts = $service->getCaseCountsByGuruBk($pegawaiId);

        $this->countKelas10 = $counts['kelas_10'];
        $this->countKelas11 = $counts['kelas_11'];
        $this->countKelas12 = $counts['kelas_12'];
    }
}
