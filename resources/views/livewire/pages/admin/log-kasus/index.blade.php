<?php

use App\Livewire\Admin\Bk\LogKasus\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app', ['title' => 'Log Kasus BK'])] class extends Index {}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full">

    <x-organisms.header>

        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot>

        Log Kasus

    </x-organisms.header>

    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="$refresh">

        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot>

    </x-organisms.table-toolbar>

    @if(count($selected) > 0)
        <div class="px-6 py-2 bg-teal-50 border-b border-teal-100 flex justify-between items-center text-sm">

            <span class="text-xs font-medium text-brand-teal">
                {{ count($selected) }} data dipilih
            </span>

            <button
                wire:click="$set('selected', [])"
                class="text-xs text-gray-500 hover:text-gray-700">

                Batal Pilih

            </button>

        </div>
    @endif

    @if($showFilters)
        <div class="px-6 sm:px-8 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-4">

            <span class="text-xs text-gray-500">
                Filter Data:
            </span>

            <select
                wire:model.live="filterStatus"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 pr-8 w-40 bg-white">

                <option value="">
                    Semua Status
                </option>

                @foreach($statusOptions as $status)

                    <option value="{{ $status }}">
                        {{ $status }}
                    </option>

                @endforeach

            </select>
        </div>
    @endif    

    <x-organisms.data-table
        :headers="['','Judul Kasus', 'Nama Siswa', 'Status']"
        empty="Belum ada data log kasus.">

        @forelse($records as $record)

        <tr
            wire:key="log-kasus-{{ $record->id }}"
            wire:click="goToDetail({{ $record->id }})"
            class="group border-b border-gray-100 transition-all duration-200 h-12 relative cursor-pointer
                hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,.1),0_4px_6px_-4px_rgba(0,0,0,.1)]
                hover:z-10 hover:rounded-md
                {{ in_array((string)$record->id, $selected) ? 'bg-teal-50/50' : 'bg-white' }}">

            <td
                class="w-16 text-center"
                onclick="event.stopPropagation()">

                <input
                    type="checkbox"
                    value="{{ $record->id }}"
                    wire:model.live="selected"
                    class="w-4 h-4 rounded border-gray-300 text-brand-teal focus:ring-brand-teal accent-brand-teal cursor-pointer">

            </td>

            <td class="px-4 py-2 font-medium">
                {{ $record->judul }}
            </td>

            <td class="px-4 py-2">
                {{ $record->siswa?->nama ?? '-' }}
            </td>

            <td class="px-4 py-2">
                {{ $record->status }}
            </td>

        </tr>

        @empty

            <tr>
                <td colspan="4" class="text-center py-4 text-sm text-gray-500">
                    Tidak ada data.
                </td>
            </tr>

        @endforelse


    </x-organisms.data-table>
</div>