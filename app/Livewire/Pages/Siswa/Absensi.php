<?php

namespace App\Livewire\Pages\Siswa;

use App\Models\DataSiswa;
use App\Models\Kehadiran;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Absensi Siswa - Bimbingan Konseling'])]
class Absensi extends Component
{
    public ?DataSiswa $siswa = null;

    public string $tanggal = '';

    public string $status = 'Hadir';

    public array $statusOptions = ['Hadir', 'Sakit', 'Izin', 'Alpha'];

    public array $history = [];

    public function mount(): void
    {
        $user = Auth::user();

        $this->siswa = DataSiswa::with(['user', 'kelas'])->where('user_id', $user->id)->first();

        if (! $this->siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $this->tanggal = now()->format('Y-m-d');
        $this->loadHistory();
    }

    public function saveAbsensi(): void
    {
        $this->validate([
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'in:Hadir,Sakit,Izin,Alpha'],
        ]);

        $tahunAjaran = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::latest('id')->first();

        if (! $tahunAjaran) {
            session()->flash('status_absensi', 'Data tahun ajaran belum tersedia.');
            return;
        }

        Kehadiran::updateOrCreate(
            [
                'siswa_id' => $this->siswa->id,
                'tanggal_kehadiran' => $this->tanggal,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ],
            [
                'status' => $this->status,
            ]
        );

        session()->flash('status_absensi', 'Absensi berhasil dicatat.');
        $this->status = 'Hadir';
        $this->loadHistory();
    }

    protected function loadHistory(): void
    {
        $this->history = Kehadiran::query()
            ->where('siswa_id', $this->siswa?->id)
            ->orderByDesc('tanggal_kehadiran')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $hari = Carbon::parse($item->tanggal_kehadiran)->locale('id')->translatedFormat('l');

                return [
                    'tanggal' => $item->tanggal_kehadiran,
                    'status' => $item->status,
                    'hari' => $hari,
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.pages.siswa.absensi');
    }
}
