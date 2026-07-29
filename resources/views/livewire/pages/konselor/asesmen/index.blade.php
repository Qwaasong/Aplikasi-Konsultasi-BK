<?php

use App\Livewire\Konselor\Asesmen\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {};

?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full">

    {{-- Header --}}
    <x-organisms.header>

        <x-slot:search>
            @if($selectedKelas)
                <x-molecules.search-input model="search" />
            @endif
        </x-slot:search>

        Asesmen

    </x-organisms.header>


    {{-- ========================================================= --}}
    {{-- DAFTAR KELAS --}}
    {{-- ========================================================= --}}

    @if(!$selectedKelas)

        <div class="px-6 sm:px-8 py-6">

            <div class="mb-5">

                <h2 class="text-base font-semibold text-gray-800">
                    Pilih Kelas
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Pilih kelas untuk melihat data asesmen siswa.
                </p>

            </div>


            {{-- Card Kelas --}}
            @if(count($kelasOptions) > 0)

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

            @foreach($kelasOptions as $kelas)

                <button
                    type="button"
                    wire:click="pilihKelas(@js($kelas['nama']))"
                    class="group text-left bg-white border border-gray-200 rounded-xl p-6
                        shadow-sm transition-all duration-200
                        hover:border-brand-teal hover:shadow-md
                        hover:-translate-y-0.5">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                                Kelas
                            </p>

                            <h3 class="mt-2 text-lg font-semibold text-gray-800
                                    group-hover:text-brand-teal">
                                {{ $kelas['nama'] }}
                            </h3>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-teal-50
                        flex items-center justify-center
                        text-brand-teal">

                        <svg
                                        xmlns="http://www.w3.org/2000/svg"
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

                    <div class="mt-4 text-xs text-gray-400">
                        Lihat Asesmen →
                    </div>

                </button>

            @endforeach

                </div>

            @else

                <div class="border border-dashed border-gray-300 rounded-xl
                            py-12 text-center">

                    <p class="text-sm text-gray-500">
                        Belum ada data kelas.
                    </p>

                </div>

            @endif

        </div>


    {{-- ========================================================= --}}
    {{-- TABEL SISWA BERDASARKAN KELAS --}}
    {{-- ========================================================= --}}

    @else

        {{-- Header kelas --}}
        <div class="px-6 sm:px-8 py-5 border-b border-gray-100">

            <div>

                <button
                    type="button"
                    wire:click="kembaliKeKelas"
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
                    Asesmen {{ $selectedKelas }}
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Data siswa dan instrumen asesmen pada kelas {{ $selectedKelas }}.
                </p>

            </div>

        </div>


        {{-- Toolbar --}}
        <x-organisms.table-toolbar
            onRefresh="refreshData">

            <x-slot:pagination>
                {{ count($records) }} data
            </x-slot:pagination>

        </x-organisms.table-toolbar>


        {{-- Flash Message --}}
        <div class="px-4 py-2">
            <x-shared.flash-message />
        </div>


        {{-- Tabel --}}
        <x-organisms.data-table
            :headers="[
                '',
                'Nama Siswa',
                'NIS',
                'Kelas',
                'AKPD',
                'DCM',
                'Gaya Belajar',
                'Sosiometri',
                'Tes Bakat Minat',
            ]"
            empty="Belum ada data siswa."
        >

            @forelse($records as $record)

                <tr
                    wire:key="asesmen-{{ $record['id'] }}"
                    class="group border-b border-gray-100
                           transition-all duration-200 h-12 relative
                           hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)]
                           hover:z-10 hover:rounded-md bg-white">

                    {{-- Checkbox --}}
                    <td class="w-16 text-center align-middle py-2">

                        <input
                            type="checkbox"
                            class="w-4 h-4 rounded border-gray-300
                                   accent-brand-teal">

                    </td>


                    {{-- Nama --}}
                    <td class="px-4 py-2 font-semibold text-gray-800">
                        {{ $record['nama'] }}
                    </td>


                    {{-- NIS --}}
                    <td class="px-4 py-2 text-sm text-gray-600">
                        {{ $record['nis'] }}
                    </td>


                    {{-- Kelas --}}
                    <td class="px-4 py-2 text-sm text-gray-600">
                        {{ $record['kelas'] }}
                    </td>


                    {{-- AKPD --}}
                    <td class="px-4 py-2">

                        <a
                            href=""
                            class="text-xs text-brand-teal hover:underline">

                            AKPD

                        </a>

                    </td>


                    {{-- DCM --}}
                    <td class="px-4 py-2">

                        <a
                            href=""
                            class="text-xs text-brand-teal hover:underline">

                            DCM

                        </a>

                    </td>


                    {{-- Gaya Belajar --}}
                    <td class="px-4 py-2">

                        <a
                            href=""
                            class="text-xs text-brand-teal hover:underline">

                            Gaya Belajar

                        </a>

                    </td>


                    {{-- Sosiometri --}}
                    <td class="px-4 py-2">

                        <a
                            href=""
                            class="text-xs text-brand-teal hover:underline">

                            Sosiometri

                        </a>

                    </td>


                    {{-- Tes Bakat Minat --}}
                    <td class="px-4 py-2">

                        <a
                            href= ""
                            class="text-xs text-brand-teal hover:underline">

                            Tes Bakat Minat

                        </a>

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="9"
                        class="py-12 text-center text-sm text-gray-500">

                        Belum ada data siswa
                        untuk kelas {{ $selectedKelas }}.

                    </td>

                </tr>

            @endforelse

        </x-organisms.data-table>

    @endif

</div>