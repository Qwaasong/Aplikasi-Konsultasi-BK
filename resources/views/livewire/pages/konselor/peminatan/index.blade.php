<?php

use App\Livewire\Konselor\Peminatan\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {}; ?>

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
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-8 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Kelas</option>
                @foreach($kelasOptions as $kelas)
                    <option value="{{ $kelas }}">{{ $kelas }}</option>
                @endforeach
            </select>

            <select
                wire:model.live="filterJurusan"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-32 pr-8 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Jurusan</option>
                @foreach($jurusanOptions as $jurusan)
                    <option value="{{ $jurusan }}">{{ $jurusan }}</option>
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