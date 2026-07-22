<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Services\PeminatanService;

new #[Layout('layouts.app')] class extends Component {

    public string $search = '';

    public bool $showFilters = false;
    public bool $selectAll = false;

    public string $filterKelas = '';
    public string $filterJurusan = '';

    public array $selected = [];

    public function with(): array
    {
        $service = app(PeminatanService::class);

        $all = $service->getAll();

        $kelasOptions = $all->pluck('siswa.kelas_label')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $jurusanOptions = $all->pluck('siswa.jurusan_label')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $records = $all;

        if ($this->search) {

            $keyword = strtolower($this->search);

            $records = $records->filter(function ($item) use ($keyword) {

                return str_contains(strtolower($item->siswa->nama ?? ''), $keyword)
                    || str_contains(strtolower($item->hasil ?? ''), $keyword);

            });

        }

        if ($this->filterKelas !== '') {

            $records = $records->filter(fn($item) =>
                (string) ($item->siswa->kelas_label ?? '') === $this->filterKelas
            );

        }

        if ($this->filterJurusan !== '') {

            $records = $records->filter(fn($item) =>
                ($item->siswa->jurusan_label ?? '') === $this->filterJurusan
            );

        }

        return [
            'records' => $records->values(),
            'kelasOptions' => $kelasOptions,
            'jurusanOptions' => $jurusanOptions,
        ];
    }

    public function create()
    {
        $this->dispatch('create-peminatan');
    }

    public function edit($id)
    {
        $this->dispatch('edit-peminatan', id: $id);
    }

    public function delete($id)
    {
        app(PeminatanService::class)->delete($id);

        session()->flash('success', 'Data peminatan berhasil dihapus.');
    }

    public function filterAction()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';
    }

    public function goToDetail($id)
    {
        $this->redirectRoute('konselor.peminatan.detail', ['id' => $id], navigate: true);
    }

    #[On('refreshTable')]
    public function refreshTable() {}

};

?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full">

    {{-- Header --}}
    <x-organisms.header action="create">
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>

        Peminatan
    </x-organisms.header>

    {{-- Toolbar --}}
    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="$refresh">
        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot:pagination>

        <x-slot:actions>
            <x-atoms.button
                wire:click="$dispatch('create-peminatan')">
                Tambah Peminatan Siswa
            </x-atoms.button>
        </x-slot:actions>
    </x-organisms.table-toolbar>

    @if($showFilters)

        <div class="px-6 sm:px-8 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-4">

            <span class="text-xs text-gray-500">
                Filter Data
            </span>

            <select
                wire:model.live="filterKelas"
                class="border rounded px-2 py-1 text-sm">

                <option value="">Semua Kelas</option>

                @foreach($kelasOptions as $kelas)

                    <option value="{{ $kelas }}">
                        {{ $kelas }}
                    </option>

                @endforeach

            </select>

            <select
                wire:model.live="filterJurusan"
                class="border rounded px-2 py-1 text-sm">

                <option value="">Semua Jurusan</option>

                @foreach($jurusanOptions as $jurusan)

                    <option value="{{ $jurusan }}">
                        {{ $jurusan }}
                    </option>

                @endforeach

            </select>

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
        'Tanggal',
        'Siswa',
        'Pilihan 1',
        'Pilihan 2',
        'Pilihan 3',
        'Hasil',
        'Aksi'
    ]"
    empty="Belum ada data peminatan.">

        @forelse($records as $record)

            <tr
                wire:key="peminatan-{{ $record->id }}"
                wire:click="goToDetail({{ $record->id }})"

                class="group border-b border-gray-100 transition-all duration-200 h-12 relative cursor-pointer hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] hover:z-10 hover:rounded-md bg-white">

                <td
                    class="w-16 text-center py-2"
                    onclick="event.stopPropagation()">

                    <input
                        type="checkbox"
                        value="{{ $record->id }}"
                        wire:model.live="selected"
                        class="w-4 h-4 rounded border-gray-300 accent-brand-teal">

                </td>

                <td class="px-4 py-2 text-sm whitespace-nowrap">

                    {{ $record->tanggal?->format('d M Y') }}

                </td>

                <td class="px-4 py-2 font-semibold align-middle">
                    <span class="text-gray-900">{{ $record->siswa->nama ?? '-' }}</span>
                    <p class="text-[11px] text-gray-400">NIS {{ $record->siswa->nis ?? '-' }}</p>
                </td>

                <td class="px-4 py-2">

                    {{ $record->pilihan1 }}

                </td>

                <td class="px-4 py-2">

                    {{ $record->pilihan2 }}

                </td>

                <td class="px-4 py-2">

                    {{ $record->pilihan3 }}

                </td>

                <td class="px-4 py-2">

                    {{ $record->hasil }}

                </td>

                <td
                    class="px-4 py-2 text-right"
                    onclick="event.stopPropagation()">

                    <div class="flex justify-end gap-2">

                        <x-atoms.action-button
                            color="blue"
                            wire:click="edit({{ $record->id }})">

                            <x-atoms.icon
                                variant="edit"
                                size="sm" />

                        </x-atoms.action-button>

                        <x-atoms.action-button
                            color="red"
                            wire:click="delete({{ $record->id }})"
                            wire:confirm="Yakin ingin menghapus data peminatan ini?">

                            <x-atoms.icon
                                variant="delete"
                                size="sm" />

                        </x-atoms.action-button>

                    </div>

                </td>

            </tr>

        @empty

        @endforelse

    </x-organisms.data-table>

    <livewire:partials.peminatan.peminatan-modal />
</div>