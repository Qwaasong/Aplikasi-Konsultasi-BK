<?php

use App\Livewire\Konselor\KehadiranSiswa\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full">

    {{-- Header --}}
    <x-organisms.header action="create">
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>

        Kehadiran Siswa
    </x-organisms.header>

    {{-- Toolbar --}}
    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="refreshData">

        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot:pagination>

        <x-slot:actions>
            <x-atoms.button
                wire:click="$dispatch('create-kehadiran')">
                Tambah Kehadiran Siswa
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
            wire:model.live="filterStatus"
            class="text-xs border rounded px-2 py-1 pr-6">

            <option value="">Semua Status</option>
            <option value="Hadir">Hadir</option>
            <option value="Izin">Izin</option>
            <option value="Sakit">Sakit</option>
            <option value="Alpha">Alpha</option>

        </select>

        <select
            wire:model.live="filterTahun"
            class="text-xs border rounded px-2 py-1 pr-6">

            <option value="">Semua Tahun</option>

            @foreach(collect($records)->pluck('tahun')->unique()->sort() as $tahun)
                <option value="{{ $tahun }}">
                    {{ $tahun }}
                </option>
            @endforeach

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
            'Tanggal Kehadiran',
            'Status',
            'Tahun Ajaran',
        ]"
        empty="Belum ada data kehadiran siswa."
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
                    {{ $record['tanggal'] }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600">
                    {{ $record['status'] }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600">
                    {{ $record['tahun'] }}
                </td>

            </tr>

        @empty

        @endforelse

    </x-organisms.data-table>

    <livewire:partials.kehadiran.kehadiran-modal />

</div>