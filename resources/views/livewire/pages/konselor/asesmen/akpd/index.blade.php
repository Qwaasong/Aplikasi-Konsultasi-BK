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


    {{-- Pilih Tingkat --}}
    @if(!$selectedTingkat)

        <div class="px-6 sm:px-8 py-6">

            {{-- Judul --}}
            <div class="mb-5">

                <h2 class="text-base font-semibold text-gray-800">
                    Pilih Tingkat
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Pilih tingkat kelas untuk melihat data AKPD.
                </p>

            </div>


            {{-- Card Tingkat --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                @foreach(['X', 'XI', 'XII'] as $tingkat)

                    <button
                        type="button"
                        wire:key="tingkat-{{ $tingkat }}"
                        wire:click="pilihTingkat('{{ $tingkat }}')"
                        class="group text-left bg-white border border-gray-200 rounded-xl p-6
                               shadow-sm transition-all duration-200
                               hover:border-brand-teal hover:shadow-md
                               hover:-translate-y-0.5">

                        <div class="flex items-center justify-between">

                            <div>

                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                                    Tingkat
                                </p>

                                <h3 class="mt-2 text-lg font-semibold text-gray-800
                                           group-hover:text-brand-teal">
                                    AKPD Kelas {{ $tingkat }}
                                </h3>

                            </div>

                            <div class="w-11 h-11 rounded-lg bg-teal-50
                                        flex items-center justify-center
                                        text-brand-teal">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-5 h-5"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332-.477 4.5-1.253m0-10.494C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332-.477-4.5-1.253" />

                                </svg>

                            </div>

                        </div>

                        <div class="mt-5 flex items-center text-xs text-gray-400">

                            <span>
                                Lihat AKPD
                            </span>

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7" />

                            </svg>

                        </div>

                    </button>

                @endforeach

            </div>

        </div>


    {{-- Tabel data (setelah pilih tingkat) --}}
    @else

        {{-- Header Kelas --}}
        <div class="px-6 sm:px-8 py-5 border-b border-gray-100">

            <button
                type="button"
                wire:click="kembaliKeTingkat"
                class="inline-flex items-center text-xs text-gray-500
                       hover:text-brand-teal mb-2">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 mr-1"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7" />

                </svg>

                Kembali ke Daftar Kelas

            </button>

            <h2 class="text-lg font-semibold text-gray-800">
                AKPD Kelas {{ $selectedTingkat }}
            </h2>

        </div>


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
            'Tanggal',
            'Siswa',
            'Kelas',
            'Aksi'
        ]"
        empty="Belum ada data AKPD untuk kelas {{ $selectedTingkat }}.">

        @forelse($records as $record)

            <tr
                wire:key="akpd-{{ $record->id }}"
                wire:click="goToDetail({{ $record->id }})"
                class="group border-b border-gray-100 cursor-pointer
                       transition-all duration-200 h-12 relative
                       hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)]
                       hover:z-10 hover:rounded-md"
            >

                {{-- Tanggal --}}
                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap align-middle">
                    {{ $record->tanggal
                        ? \Carbon\Carbon::parse($record->tanggal)->isoFormat('D MMM Y')
                        : '-'
                    }}
                </td>


                {{-- Siswa --}}
                <td class="px-4 py-2 font-semibold align-middle">
                    <a
                        href="{{ route('konselor.asesmen.akpd.detail', $record->id) }}"
                        wire:navigate
                        onclick="event.stopPropagation()"
                        class="text-gray-900 hover:text-teal-600 hover:underline transition"
                    >
                        {{ $record->siswa?->nama ?? '-' }}
                    </a>

                    <p class="text-[11px] text-gray-400">
                        NIS {{ $record->siswa?->nis ?? '-' }}
                    </p>
                </td>


                {{-- Kelas --}}
                <td class="px-4 py-2 text-sm text-gray-700 align-middle">
                    {{ $record->siswa?->kelas_label ?? '-' }}
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

    @endif


    {{-- Modal AKPD --}}
@include('livewire.partials.asesmen.akpd.akpd-modal', [
    'editingId' => $editingId,
])

</div>