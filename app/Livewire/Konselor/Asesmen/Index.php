<?php

namespace App\Livewire\Konselor\Asesmen;

use App\Models\Kelas;
use App\Models\DataSiswa;
use Livewire\Volt\Component;

class Index extends Component
{
    public ?string $selectedKelas = null;

    public array $kelasOptions = [];
    public array $records = [];

    public string $search = '';

    public function mount(): void
    {
        $this->loadKelas();
    }

    public function loadKelas(): void
    {
        $kelas = Kelas::with('jurusan')
            ->orderBy('nama_kelas')
            ->get();

        $this->kelasOptions = $kelas
            ->groupBy(function ($kelas) {
                // XI RPL 1 -> XI RPL
                // XI MM 1  -> XI MM
                // XI TKJ 1 -> XI TKJ
                return preg_replace(
                    '/\s+\d+$/',
                    '',
                    trim($kelas->nama_kelas)
                );
            })
            ->map(function ($kelasGroup, $namaKelas) {
                return [
                    'nama' => $namaKelas,

                    'ids' => $kelasGroup
                        ->pluck('id')
                        ->values()
                        ->toArray(),

                    'jurusan' => $kelasGroup
                        ->map(fn ($kelas) => $kelas->jurusan?->nama_jurusan)
                        ->filter()
                        ->unique()
                        ->implode(', '),
                ];
            })
            ->values()
            ->toArray();
    }

    public function pilihKelas(string $namaKelas): void
    {
        $this->selectedKelas = $namaKelas;
        $this->search = '';

        $this->loadSiswa();
    }

    public function loadSiswa(): void
    {
        if (!$this->selectedKelas) {
            $this->records = [];
            return;
        }

        /*
         * Ambil semua ID kelas yang memiliki nama dasar yang sama.
         *
         * Contoh:
         * XI RPL
         * XI RPL 1
         *
         * keduanya dianggap sebagai XI RPL.
         */
        $kelasIds = Kelas::query()
            ->get()
            ->filter(function ($kelas) {
                $namaDasar = preg_replace(
                    '/\s+\d+$/',
                    '',
                    trim($kelas->nama_kelas)
                );

                return $namaDasar === $this->selectedKelas;
            })
            ->pluck('id');

        $this->records = DataSiswa::query()
            ->whereIn('kelas_id', $kelasIds)
            ->with('user')
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where(
                        'nama',
                        'like',
                        '%' . $this->search . '%'
                    );
                });
            })
            ->orderBy('id')
            ->get()
            ->map(fn ($siswa) => [
                'id' => $siswa->id,
                'siswa_id' => $siswa->id,
                'nama' => $siswa->nama
                    ?? $siswa->user?->nama
                    ?? '-',
                'nis' => $siswa->nis ?? '-',
                'kelas' => $siswa->kelas_label,
            ])
            ->toArray();
    }

    public function updatedSearch(): void
    {
        $this->loadSiswa();
    }

    public function kembaliKeKelas(): void
    {
        $this->selectedKelas = null;
        $this->records = [];
        $this->search = '';
    }
}