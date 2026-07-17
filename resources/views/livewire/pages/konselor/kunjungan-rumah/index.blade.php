<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\HomeVisitService;

new #[Layout('layouts.app')] class extends Component {

    public string $search = '';
    public string $filterStatus = '';
    public string $filterPrioritas = '';
    public bool $showFilters = false;
    public array $records = [];

    public function mount()
    {
        $this->loadData();
    }

    public function create()
    {
        $this->dispatch('create-home-visit');
    }

    public function loadData()
    {
        $records = app(HomeVisitService::class)->getAll();

        if ($this->search !== '') {
            $records = $records->filter(function ($item) {
                return str_contains(
                    strtolower($item->siswa?->nama ?? ''),
                    strtolower($this->search)
                );
            });
        }

        if ($this->filterStatus !== '') {
            $records = $records->filter(fn($item) =>
                $item->status === $this->filterStatus
            );
        }

        if ($this->filterPrioritas !== '') {
            $records = $records->filter(fn($item) =>
                $item->prioritas === $this->filterPrioritas
            );
        }

        $this->records = $records->map(function ($item) {

        return [
            'id' => $item->id,
            'nama' => $item->siswa?->nama_lengkap ?? '-',
            'kelas' => $item->siswa?->kelas_label ?? '-',
            'guru_bk' => $item->gurubk?->user?->nama ?? '-',

            'judul' => $item->judul,

            'tanggal_konsultasi' => optional($item->tanggal_konsultasi)
                ->translatedFormat('d F Y'),

            'status' => $item->status,
            'prioritas' => $item->prioritas,
        ];

        })->toArray();
    }

    public function updated()
    {
        $this->loadData();
    }

    public function resetFilter()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterPrioritas = '';

        $this->loadData();
    }

    public function refreshData()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterPrioritas = '';

        $this->loadData();
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

        Kunjungan Rumah
    </x-organisms.header>

    {{-- Toolbar --}}
    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="refreshData">

        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot:pagination>

        <x-slot:actions>
            <x-atoms.button
                wire:click="$dispatch('create-home-visit')">
                Tambah Kunjungan Rumah
            </x-atoms.button>
        </x-slot:actions>
    </x-organisms.table-toolbar>
    
    @if($showFilters)

    <div class="px-6 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-4 text-sm">

        <span class="text-xs text-gray-500 font-medium">
            Filter Data:
        </span>

        <select
            wire:model.live="filterStatus"
            class="text-xs border rounded px-2 py-1 pr-6">

            <option value="">Semua Status</option>
            <option value="Open">Open</option>
            <option value="Diproses">Diproses</option>
            <option value="Selesai">Selesai</option>

        </select>

        <select
            wire:model.live="filterPrioritas"
            class="text-xs border rounded px-2 py-1 pr-6">

            <option value="">Semua Prioritas</option>
            <option value="Rendah">Rendah</option>
            <option value="Sedang">Sedang</option>
            <option value="Tinggi">Tinggi</option>

        </select>

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
            'Judul',
            'Guru BK',
            'Tanggal',
            'Status',
            'Prioritas',
        ]"
        empty="Belum ada data kunjungan rumah."
    >

        @forelse($records as $record)

            <tr
                 onclick="window.location='{{ route('konselor.home-visit.detail', $record['id']) }}'" class="group border-b border-gray-100 transition-all duration-200 h-12 relative cursor-pointer hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] hover:z-10 hover:rounded-md bg-white">

                <td class="w-16 text-center align-middle py-2">
                    <input
                        type="checkbox"
                        class="w-4 h-4 rounded border-gray-300 accent-brand-teal">
                </td>

                <td class="px-4 py-2 text-sm text-gray-600">
                    {{ $record['nama'] }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600">
                    {{ $record['kelas'] }}
                </td>

                <td class="px-4 py-2 text-sm">
                    {{ $record['judul'] }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600">
                    {{ $record['guru_bk'] }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600">
                    {{ $record['tanggal_konsultasi'] }}
                </td>

                <td class="px-4 py-2">
                    <span class="px-2 py-1 rounded-full text-xs
                        @if($record['status']=='Open')
                            bg-blue-100 text-blue-700
                        @elseif($record['status']=='Diproses')
                            bg-yellow-100 text-yellow-700
                        @else
                            bg-green-100 text-green-700
                        @endif">
                        {{ $record['status'] }}
                    </span>
                </td>

                <td class="px-4 py-2">
                    <span class="px-2 py-1 rounded-full text-xs
                        @if($record['prioritas']=='Tinggi')
                            bg-red-100 text-red-700
                        @elseif($record['prioritas']=='Sedang')
                            bg-yellow-100 text-yellow-700
                        @else
                            bg-green-100 text-green-700
                        @endif">
                        {{ $record['prioritas'] }}
                    </span>
                </td>

            </tr>

        @empty

        @endforelse

    </x-organisms.data-table>

    <livewire:partials.home-visit.home-visit-modal />

</div>