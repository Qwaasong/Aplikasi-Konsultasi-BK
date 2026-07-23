<?php

namespace App\Livewire\Konselor;

use App\Models\KasusBk;
use App\Models\Pegawai;
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

    public function mount(): void
    {
        $pegawaiId = Pegawai::where('user_id', auth()->id())->value('id');

        $this->countKelas10 = KasusBk::where('guru_bk_id', $pegawaiId)
            ->whereHas('siswa', fn($q) => $q->whereHas('kelas', fn($qk) => $qk->where('tingkat', 10)))
            ->distinct()
            ->count('siswa_id');

        $this->countKelas11 = KasusBk::where('guru_bk_id', $pegawaiId)
            ->whereHas('siswa', fn($q) => $q->whereHas('kelas', fn($qk) => $qk->where('tingkat', 11)))
            ->distinct()
            ->count('siswa_id');

        $this->countKelas12 = KasusBk::where('guru_bk_id', $pegawaiId)
            ->whereHas('siswa', fn($q) => $q->whereHas('kelas', fn($qk) => $qk->where('tingkat', 12)))
            ->distinct()
            ->count('siswa_id');
    }
}
