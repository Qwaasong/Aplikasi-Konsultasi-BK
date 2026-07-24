<?php

use App\Livewire\Konselor\LayananKonseling\Individu;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Individu {}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full" x-data="{ loading: false }"
    x-on:click="if ($event.target.closest('button[wire\\:click^=\'edit\'], button[wire\\:click=\'create\']')) loading = true"
    x-on:open-modal.window="loading = false" x-on:close-modal.window="loading = false">

    {{-- Header --}}
    <x-organisms.header action="create">
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>
        Layanan Konseling Individu
    </x-organisms.header>

    {{-- Toolbar --}}
    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="$refresh">
        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot:pagination>
    </x-organisms.table-toolbar>

    {{-- Advanced Filters --}}
    @if($showFilters)
        <div class="px-6 sm:px-8 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-4 text-sm text-gray-600 shrink-0 transition-all">
            <span class="text-gray-500 text-xs font-medium">Filter Data:</span>
            <select wire:model.live="filterKelas"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Kelas</option>
                @foreach($kelasOptions ?? [] as $k)
                    <option value="{{ $k }}">Kelas {{ $k }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterJurusan"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Jurusan</option>
                @foreach($jurusanOptions ?? [] as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterJenisKelamin"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
            @if($search !== '' || $filterKelas !== '' || $filterJurusan !== '' || $filterJenisKelamin !== '')
                <button wire:click="resetFilters"
                    class="text-xs text-brand-teal font-medium hover:text-teal-700 hover:underline transition-colors ml-auto">
                    Reset Semua
                </button>
            @endif
        </div>
    @endif

    {{-- Selected indicator --}}
    @if(count($selected) > 0)
        <div class="px-6 py-2 bg-teal-50 border-b border-teal-100 flex justify-between items-center text-sm">
            <span class="text-xs font-medium text-brand-teal">{{ count($selected) }} data dipilih</span>
            <button wire:click="$set('selected', [])" class="text-xs text-gray-500 hover:text-gray-700">Batal Pilih</button>
        </div>
    @endif

    {{-- Flash Message --}}
    <div class="px-4 py-2">
        <x-shared.flash-message />
    </div>

    {{-- Data Table --}}
    <x-organisms.data-table
        :headers="['', 'Tanggal', 'Siswa', 'Kelas', 'Uraian Masalah', 'Aksi']"
        empty="Belum ada data layanan konseling individu.">
        @forelse($records as $record)
            <tr wire:key="bi-{{ $record->id }}" wire:click="goToDetail({{ $record->id }})"
                class="group border-b border-gray-100 transition-all duration-200 h-12 relative cursor-pointer hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] hover:z-10 hover:rounded-md {{ in_array($record->id, $selected) ? 'bg-teal-50/50' : 'bg-white' }}">

                <td class="w-16 text-center align-middle rounded-l-md py-2" onclick="event.stopPropagation()">
                    <input type="checkbox" value="{{ $record->id }}" wire:model.live="selected"
                        class="w-4 h-4 rounded border-gray-300 text-brand-teal focus:ring-brand-teal accent-brand-teal cursor-pointer">
                </td>

                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap align-middle">
                    {{ \Carbon\Carbon::parse($record->tanggal_layanan)->isoFormat('D MMM Y') }}
                </td>

                <td class="px-4 py-2 font-semibold align-middle">
                    <span class="text-gray-900">{{ $record->kasus?->siswa?->nama ?? $record->siswa?->nama ?? '-' }}</span>
                    <p class="text-[11px] text-gray-400">NIS {{ $record->kasus?->siswa?->nis ?? $record->siswa?->nis ?? '-' }}</p>
                </td>

                <td class="px-4 py-2 text-sm text-gray-600 align-middle">
                    {{ $record->kasus?->siswa?->kelas_label ?? '-' }} - {{ $record->kasus?->siswa?->jurusan_label ?? '-' }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600 max-w-xs truncate align-middle">
                    {{ $record->uraian_masalah }}
                </td>

                <td class="px-4 py-2 text-right align-middle">
                    <div class="flex items-center justify-end gap-2" onclick="event.stopPropagation()">
                        <x-atoms.action-button color="blue" title="Edit" wire:click="edit({{ $record->id }})">
                            <x-atoms.icon variant="edit" size="sm" />
                        </x-atoms.action-button>
                        <x-atoms.action-button color="red" title="Hapus" wire:click="delete({{ $record->id }})"
                            wire:confirm="Yakin ingin menghapus layanan konseling individu ini?">
                            <x-atoms.icon variant="delete" size="sm" />
                        </x-atoms.action-button>
                    </div>
                </td>

            </tr>
        @empty
        @endforelse
    </x-organisms.data-table>

    <livewire:partials.layanan-konseling.layanan-konseling-individu-modal />

</div>
