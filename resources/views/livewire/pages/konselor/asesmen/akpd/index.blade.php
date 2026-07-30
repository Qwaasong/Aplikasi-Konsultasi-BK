<?php

use App\Livewire\Konselor\Asesmen\Akpd\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {};

?>

<div>

    {{-- Header --}}
    <x-organisms.header action="createAkpd">
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>

        Asesmen Kebutuhan Peserta Didik (AKPD)
    </x-organisms.header>


    {{-- Toolbar --}}
    <x-organisms.table-toolbar
        onFilter="filterAction"
        onRefresh="$refresh"
    >
        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot:pagination>
    </x-organisms.table-toolbar>


    {{-- Filter --}}
    @if($showFilters)
        <div
            class="px-6 sm:px-8 py-3 border-b border-gray-100 bg-gray-50
                   flex items-center gap-4 text-sm text-gray-600 shrink-0
                   transition-all"
        >

            <span class="text-gray-500 text-xs font-medium">
                Filter Data:
            </span>

            {{-- Filter Kelas --}}
            <select
                wire:model.live="filterKelas"
                class="text-xs border border-gray-200 rounded px-2 py-1.5
                       focus:outline-none focus:ring-1 focus:ring-brand-teal
                       w-28 sm:w-36 pr-8 flex-shrink-0 bg-white cursor-pointer"
            >
                <option value="">Semua Kelas</option>

                @foreach($kelasOptions ?? [] as $kelas)
                    <option value="{{ $kelas }}">
                        Kelas {{ $kelas }}
                    </option>
                @endforeach
            </select>


            {{-- Filter Jurusan --}}
            <select
                wire:model.live="filterJurusan"
                class="text-xs border border-gray-200 rounded px-2 py-1.5
                       focus:outline-none focus:ring-1 focus:ring-brand-teal
                       w-32 pr-8 flex-shrink-0 bg-white cursor-pointer"
            >
                <option value="">Semua Jurusan</option>

                @foreach($jurusanOptions ?? [] as $jurusan)
                    <option value="{{ $jurusan }}">
                        {{ $jurusan }}
                    </option>
                @endforeach
            </select>


            {{-- Reset Filter --}}
            @if($search !== '' || $filterKelas !== '' || $filterJurusan !== '')
                <button
                    wire:click="resetFilters"
                    class="text-xs text-brand-teal font-medium
                           hover:text-teal-700 hover:underline
                           transition-colors ml-auto"
                >
                    Reset Semua
                </button>
            @endif

        </div>
    @endif


    {{-- Selected --}}
    @if(count($selected) > 0)
        <div
            class="px-6 py-2 bg-teal-50 border-b border-teal-100
                   flex justify-between items-center text-sm"
        >

            <span class="text-xs font-medium text-brand-teal">
                {{ count($selected) }} data dipilih
            </span>

            <button
                wire:click="$set('selected', [])"
                class="text-xs text-gray-500 hover:text-gray-700"
            >
                Batal Pilih
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
            'Tanggal',
            'Siswa',
            'Pribadi',
            'Sosial',
            'Belajar',
            'Karir',
            'Kesimpulan',
            'Aksi'
        ]"
        empty="Belum ada data asesmen kebutuhan peserta didik.">

        @forelse($records as $record)

            <tr
                wire:key="akpd-{{ $record->id }}"
                class="group border-b border-gray-100
                       transition-all duration-200 h-12 relative
                       hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)]
                       hover:z-10 hover:rounded-md
                       {{ in_array($record->id, $selected)
                            ? 'bg-teal-50/50'
                            : 'bg-white' }}"
            >

                {{-- Checkbox --}}
                <td
                    class="w-16 text-center align-middle rounded-l-md py-2"
                    onclick="event.stopPropagation()"
                >
                    <input
                        type="checkbox"
                        value="{{ $record->id }}"
                        wire:model.live="selected"
                        class="w-4 h-4 rounded border-gray-300
                               text-brand-teal focus:ring-brand-teal
                               accent-brand-teal cursor-pointer"
                    >
                </td>


                {{-- Tanggal --}}
                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap align-middle">
                    {{ $record->tanggal
                        ? \Carbon\Carbon::parse($record->tanggal)->isoFormat('D MMM Y')
                        : '-'
                    }}
                </td>


                {{-- Siswa --}}
                <td class="px-4 py-2 font-semibold align-middle">
                    <span class="text-gray-900">
                        {{ $record->siswa?->nama ?? '-' }}
                    </span>

                    <p class="text-[11px] text-gray-400">
                        NIS {{ $record->siswa?->nis ?? '-' }}
                    </p>
                </td>


                {{-- Pribadi --}}
                <td class="px-4 py-2 text-sm text-gray-600 max-w-xs align-middle">
                    <div class="truncate max-w-[180px]" title="{{ $record->pribadi }}">
                        {{ $record->pribadi ?: '-' }}
                    </div>
                </td>


                {{-- Sosial --}}
                <td class="px-4 py-2 text-sm text-gray-600 max-w-xs align-middle">
                    <div class="truncate max-w-[180px]" title="{{ $record->sosial }}">
                        {{ $record->sosial ?: '-' }}
                    </div>
                </td>


                {{-- Belajar --}}
                <td class="px-4 py-2 text-sm text-gray-600 max-w-xs align-middle">
                    <div class="truncate max-w-[180px]" title="{{ $record->belajar }}">
                        {{ $record->belajar ?: '-' }}
                    </div>
                </td>


                {{-- Karir --}}
                <td class="px-4 py-2 text-sm text-gray-600 max-w-xs align-middle">
                    <div class="truncate max-w-[180px]" title="{{ $record->karir }}">
                        {{ $record->karir ?: '-' }}
                    </div>
                </td>


                {{-- Kesimpulan --}}
                <td class="px-4 py-2 text-sm text-gray-600 max-w-xs align-middle">
                    <div
                        class="truncate max-w-[220px]"
                        title="{{ $record->kesimpulan }}"
                    >
                        {{ $record->kesimpulan ?: '-' }}
                    </div>
                </td>


                {{-- Aksi --}}
                <td
                    class="px-4 py-2 text-right align-middle"
                    onclick="event.stopPropagation()"
                >
                    <div class="flex items-center justify-end gap-2">

                        <x-atoms.action-button
                            color="blue"
                            title="Edit"
                            wire:click="loadAkpd({{ $record->id }})">
                            <x-atoms.icon variant="edit" size="sm"/>
                        </x-atoms.action-button>

                        <x-atoms.action-button
                            color="red"
                            title="Hapus"
                            wire:click="delete({{ $record->id }})"
                            wire:confirm="Yakin ingin menghapus data AKPD ini?">
                            <x-atoms.icon variant="delete" size="sm" />
                        </x-atoms.action-button>

                    </div>
                </td>

            </tr>

        @empty
        @endforelse

    </x-organisms.data-table>


    {{-- Modal AKPD --}}
@include('livewire.partials.asesmen.akpd.akpd-modal', [
    'editingId' => $editingId,
])

</div>