<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Models\AlihtanganKasus;

new #[Layout('layouts.app')] class extends Component {

    public string $search = '';

    public string $filterKelas = '';
    public string $filterPihak = '';
    public string $filterTanggal = '';

    public bool $showFilters = false;

    public array $records = [];

    public function create()
    {
        $this->dispatch('create-alih-tangan-kasus');
    }

    public function mount()
    {
        $this->loadData();
    }

    #[On('refreshTable')]
    public function loadData()
    {
        $records = AlihtanganKasus::with([
            'konsultasi.siswa.kelas.jurusan'
        ])->latest()->get();

        if ($this->search !== '') {
            $records = $records->filter(function ($item) {
                return str_contains(
                    strtolower($item->konsultasi?->siswa?->nama_lengkap ?? ''),
                    strtolower($this->search)
                );
            });
        }

        if ($this->filterKelas !== '') {
            $records = $records->filter(function ($item) {
                return ($item->konsultasi?->siswa?->kelas_label ?? '') === $this->filterKelas;
            });
        }

        if ($this->filterPihak !== '') {
            $records = $records->filter(function ($item) {
                return $item->pihak_penerima === $this->filterPihak;
            });
        }

        if ($this->filterTanggal !== '') {
            $records = $records->filter(function ($item) {
                return $item->tanggal_alih == $this->filterTanggal;
            });
        }

        $this->records = $records
            ->map(function ($item) {

                $siswa = $item->konsultasi?->siswa;

                return [
                    'id' => $item->id,
                    'nama' => $siswa?->nama_lengkap ?? '-',
                    'kelas' => $siswa?->kelas_label ?? '-',
                    'pihak_penerima' => $item->pihak_penerima,
                    'tanggal' => \Carbon\Carbon::parse($item->tanggal_alih)
                        ->format('d-m-Y'),
                ];
            })
            ->toArray();
    }

    public function updated()
    {
        $this->loadData();
    }

    public function resetFilter()
    {
        $this->search = '';
        $this->filterKelas = '';
        $this->filterPihak = '';
        $this->filterTanggal = '';

        $this->loadData();
    }

    public function refreshData()
    {
        $this->resetFilter();
    }

    public function filterAction()
    {
        $this->showFilters = ! $this->showFilters;
    }

};

?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full">

    {{-- Header --}}
    <x-organisms.header action="create">
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>

        Alih Tangan Kasus
    </x-organisms.header>

    {{-- Toolbar --}}
    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="refreshData">
       
        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot:pagination>

        <x-slot:actions>
            <x-atoms.button
                wire:click="$dispatch('create-alih-tangan-kasus')">
                Tambah Alih Tangan Kasus
            </x-atoms.button>
        </x-slot:actions>

    </x-organisms.table-toolbar>

    @if($showFilters)

        <div class="px-6 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-4 text-sm">

            <span class="text-xs text-gray-500 font-medium">
                Filter Data:
            </span>

            <select
                wire:model.live="filterKelas"
                class="text-xs border rounded px-2 py-1 pr-6">

                <option value="">Semua Kelas</option>

                @foreach(collect($records)->pluck('kelas')->unique()->sort() as $kelas)
                    <option value="{{ $kelas }}">
                        {{ $kelas }}
                    </option>
                @endforeach

            </select>

            <select
                wire:model.live="filterPihak"
                class="text-xs border rounded px-2 py-1 pr-6">

                <option value="">Semua Pihak</option>

                @foreach(collect($records)->pluck('pihak_penerima')->unique()->sort() as $pihak)
                    <option value="{{ $pihak }}">
                        {{ $pihak }}
                    </option>
                @endforeach

            </select>

            <input
                type="date"
                wire:model.live="filterTanggal"
                class="text-xs border rounded px-2 py-1">

            <button
                wire:click="resetFilter"
                class="ml-auto text-xs text-brand-teal hover:underline">

                Reset Semua

            </button>
        </div>
    @endif

    {{-- Flash Message --}}
    <div class="px-4 py-2">
        <x-shared.flash-message />
    </div>

    {{-- Data Table --}}
    <x-organisms.data-table 
        :headers="[
            '',
            'Nama Siswa',
            'Kelas',
            'Pihak Penerima',
            'Tanggal Alih',
        ]"
        empty="Belum ada data alih tangan kasus."
    >

        @forelse($records as $record)

            <tr
                class="group border-b border-gray-100 transition-all duration-200 h-12 relative cursor-pointer hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] hover:z-10 hover:rounded-md bg-white">

                <td class="w-16 text-center align-middle py-2">
                    <input
                        type="checkbox"
                        class="w-4 h-4 rounded border-gray-300 accent-brand-teal">
                </td>

                <td class="px-4 py-2 font-semibold">
                    {{ $record['nama'] }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600">
                    {{ $record['kelas'] }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600">
                    {{ $record['pihak_penerima'] }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600">
                    {{ $record['tanggal'] }}
                </td>

            </tr>

        @empty

        @endforelse

    </x-organisms.data-table>

    <livewire:partials.alih-tangan-kasus.alih-tangan-kasus-modal />

</div>