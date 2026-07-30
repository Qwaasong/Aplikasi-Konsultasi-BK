<?php

use App\Livewire\Konselor\Asesmen\Sosiometri\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {};

?>

<div>

    {{-- =========================
        HEADER
    ========================== --}}
    <x-organisms.header action="createSosiometri">

        <x-slot:search>
            <x-molecules.search-input model="search"/>
        </x-slot:search>

        Asesmen Sosiometri

    </x-organisms.header>


    {{-- =========================
        TOOLBAR
    ========================== --}}
    <x-organisms.table-toolbar
        onFilter="filterAction"
        onRefresh="$refresh">

        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot:pagination>

    </x-organisms.table-toolbar>


    {{-- =========================
        FILTER
    ========================== --}}
    @if($showFilters)

        <div
            class="px-6 sm:px-8 py-3 border-b border-gray-100 bg-gray-50
                   flex items-center gap-4 text-sm text-gray-600 shrink-0">

            <span class="text-gray-500 text-xs font-medium">
                Filter Data:
            </span>

            {{-- Filter Kelas --}}
            <select
                wire:model.live="filterKelas"
                class="text-xs border border-gray-200 rounded px-2 py-1.5
                       focus:outline-none focus:ring-1 focus:ring-brand-teal
                       w-28 sm:w-36 pr-8 flex-shrink-0 bg-white cursor-pointer">

                <option value="">
                    Semua Kelas
                </option>

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
                       w-36 pr-8 flex-shrink-0 bg-white cursor-pointer">

                <option value="">
                    Semua Jurusan
                </option>

                @foreach($jurusanOptions ?? [] as $jurusan)

                    <option value="{{ $jurusan }}">
                        {{ $jurusan }}
                    </option>

                @endforeach

            </select>


            {{-- Reset --}}
            @if(
                $search !== '' ||
                $filterKelas !== '' ||
                $filterJurusan !== ''
            )

                <button
                    wire:click="resetFilters"
                    class="text-xs text-brand-teal font-medium
                           hover:text-teal-700 hover:underline
                           transition-colors ml-auto">

                    Reset Semua

                </button>

            @endif

        </div>

    @endif


    {{-- =========================
        SELECTED
    ========================== --}}
    @if(count($selected) > 0)

        <div
            class="px-6 py-2 bg-teal-50 border-b border-teal-100
                   flex justify-between items-center text-sm">

            <span class="text-xs font-medium text-brand-teal">

                {{ count($selected) }} data dipilih

            </span>

            <button
                wire:click="$set('selected',[])"
                class="text-xs text-gray-500 hover:text-gray-700">

                Batal Pilih

            </button>

        </div>

    @endif


    {{-- =========================
        FLASH MESSAGE
    ========================== --}}
    <div class="px-4 py-2">

        <x-shared.flash-message/>

    </div>

        {{-- =====================================================
        DATA TABLE
    ====================================================== --}}
    <x-organisms.data-table
        :headers="[
            '',
            'Judul',
            'Siswa',
            'Instruksi',
            'Jumlah Pilihan',
            'Jumlah Respon',
            'Aksi'
        ]"
        empty="Belum ada data asesmen sosiometri."
    >

        @forelse($records as $record)

            <tr
                wire:key="sosiometri-{{ $record->id }}"
                class="group border-b border-gray-100
                       transition-all duration-200 h-12 relative
                       hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)]
                       hover:z-10 hover:rounded-md
                       {{ in_array($record->id, $selected)
                            ? 'bg-teal-50/50'
                            : 'bg-white'
                       }}"
            >

                {{-- =================================================
                    CHECKBOX
                ================================================== --}}
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


                {{-- =================================================
                    JUDUL
                ================================================== --}}
                <td class="px-4 py-2 align-middle">

                    <div class="font-semibold text-gray-900">

                        {{ $record->judul ?: '-' }}

                    </div>

                    <div class="text-[11px] text-gray-400 mt-1">

                        ID #{{ $record->id }}

                    </div>

                </td>


                {{-- =================================================
                    SISWA
                ================================================== --}}
                <td class="px-4 py-2 align-middle">

                    <span class="font-semibold text-gray-900">

                        {{ $record->siswa?->nama ?? '-' }}

                    </span>

                    <p class="text-[11px] text-gray-400">

                        NIS {{ $record->siswa?->nis ?? '-' }}

                    </p>

                </td>


                {{-- =================================================
                    INSTRUKSI
                ================================================== --}}
                <td class="px-4 py-2 text-sm text-gray-600 max-w-sm align-middle">

                    <div
                        class="truncate max-w-[280px]"
                        title="{{ $record->instruksi }}"
                    >

                        {{ $record->instruksi ?: '-' }}

                    </div>

                </td>


                {{-- =================================================
                    JUMLAH PILIHAN
                ================================================== --}}
                <td class="px-4 py-2 align-middle">

                    <span
                        class="inline-flex items-center
                            px-3 py-1 rounded-full
                            bg-indigo-50
                            text-indigo-700
                            text-xs font-semibold"
                    >

                        {{ $record->jumlah_pilihan }}

                        Pilihan

                    </span>

                </td>


                {{-- =================================================
                    JUMLAH RESPON
                ================================================== --}}
                <td class="px-4 py-2 align-middle">

                    @php
                        $totalRespon = $record->respons?->count() ?? 0;
                    @endphp

                    @if($totalRespon > 0)

                        <span
                            class="inline-flex items-center
                                px-3 py-1 rounded-full
                                bg-emerald-50
                                text-emerald-700
                                text-xs font-semibold"
                        >

                            {{ $totalRespon }}

                            Respon

                        </span>

                    @else

                        <span
                            class="inline-flex items-center
                                px-3 py-1 rounded-full
                                bg-gray-100
                                text-gray-500
                                text-xs font-semibold"
                        >

                            Belum Ada

                        </span>

                    @endif

                </td>


                {{-- =================================================
                    AKSI
                ================================================== --}}
                <td
                    class="px-4 py-2 text-right align-middle"
                    onclick="event.stopPropagation()"
                >

                    <div class="flex items-center justify-end gap-2">

                        {{-- Detail --}}
                        <x-atoms.action-button
                            color="green"
                            title="Detail"
                            wire:click="goToDetail({{ $record->id }})"
                        >

                            <x-atoms.icon
                                variant="eye"
                                size="sm"
                            />

                        </x-atoms.action-button>


                        {{-- Edit --}}
                        <x-atoms.action-button
                            color="blue"
                            title="Edit"
                            wire:click="loadSosiometri({{ $record->id }})"
                        >

                            <x-atoms.icon
                                variant="edit"
                                size="sm"
                            />

                        </x-atoms.action-button>


                        {{-- Delete --}}
                        <x-atoms.action-button
                            color="red"
                            title="Hapus"
                            wire:click="delete({{ $record->id }})"
                            wire:confirm="Yakin ingin menghapus data sosiometri ini?"
                        >

                            <x-atoms.icon
                                variant="delete"
                                size="sm"
                            />

                        </x-atoms.action-button>

                    </div>

                </td>

            </tr>

        @empty

        @endforelse

    </x-organisms.data-table>
    
    {{-- Modal Sosiometri --}}
    @include('livewire.partials.asesmen.sosiometri.sosiometri-modal', [
        'editingId' => $editingId,
    ])

</div>