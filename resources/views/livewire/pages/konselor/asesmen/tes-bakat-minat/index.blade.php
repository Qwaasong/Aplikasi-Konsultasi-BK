<?php

use App\Livewire\Konselor\Asesmen\TesBakatMinat\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {};

?>


<div>


    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <x-organisms.header action="createTesBakatMinat">

        <x-slot:search>

            <x-molecules.search-input 
                model="search"
            />

        </x-slot:search>


        Tes Bakat Minat

    </x-organisms.header>





    {{-- =====================================================
        TOOLBAR
    ====================================================== --}}

    <x-organisms.table-toolbar

        onFilter="filterAction"

        onRefresh="$refresh"

    >

        <x-slot:pagination>

            {{ count($records) }} data

        </x-slot:pagination>


    </x-organisms.table-toolbar>






    {{-- =====================================================
        FILTER
    ====================================================== --}}


    @if($showFilters)

        <div

            class="px-6 sm:px-8 py-3 border-b border-gray-100
            bg-gray-50 flex items-center gap-4
            text-sm text-gray-600 shrink-0"

        >



            <span class="text-gray-500 text-xs font-medium">

                Filter Data:

            </span>





            {{-- FILTER KELAS --}}

            <select

                wire:model.live="filterKelas"

                class="text-xs border border-gray-200 rounded
                px-2 py-1.5 focus:outline-none
                focus:ring-1 focus:ring-brand-teal
                w-28 sm:w-36 bg-white"

            >

                <option value="">
                    Semua Kelas
                </option>


                @foreach($kelasOptions ?? [] as $kelas)

                    <option value="{{ $kelas }}">

                        Kelas {{ $kelas }}

                    </option>

                @endforeach


            </select>







            {{-- FILTER JURUSAN --}}

            <select

                wire:model.live="filterJurusan"

                class="text-xs border border-gray-200 rounded
                px-2 py-1.5 focus:outline-none
                focus:ring-1 focus:ring-brand-teal
                w-32 bg-white"

            >

                <option value="">
                    Semua Jurusan
                </option>



                @foreach($jurusanOptions ?? [] as $jurusan)

                    <option value="{{ $jurusan }}">

                        {{ $jurusan }}

                    </option>

                @endforeach


            </select>





            @if(
                $search !== '' ||
                $filterKelas !== '' ||
                $filterJurusan !== ''
            )

                <button

                    wire:click="resetFilters"

                    class="text-xs text-brand-teal
                    font-medium hover:underline ml-auto"

                >

                    Reset Semua

                </button>


            @endif




        </div>


    @endif

        {{-- =====================================================
        FLASH MESSAGE
    ====================================================== --}}

    <div class="px-4 py-2">

        <x-shared.flash-message />

    </div>





    {{-- =====================================================
        DATA TABLE
    ====================================================== --}}


    <x-organisms.data-table

        :headers="[

            '',

            'Tanggal',

            'Siswa',

            'Bakat',

            'Minat',

            'Bidang Rekomendasi',

            'Kesimpulan',

            'Aksi'

        ]"

        empty="Belum ada data tes bakat minat."

    >




        @forelse($records as $record)



            <tr

                wire:key="tes-bakat-minat-{{ $record->id }}"

                class="group border-b border-gray-100
                transition-all duration-200 h-12
                hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1)]"

            >




                {{-- CHECKBOX --}}

                <td

                    class="w-16 text-center"

                    onclick="event.stopPropagation()"

                >

                    <input

                        type="checkbox"

                        value="{{ $record->id }}"

                        wire:model.live="selected"

                        class="w-4 h-4 rounded
                        border-gray-300
                        text-brand-teal
                        accent-brand-teal"

                    >

                </td>





                {{-- TANGGAL --}}

                <td class="px-4 py-2 text-sm">

                    {{ 
                        $record->tanggal
                        ? \Carbon\Carbon::parse($record->tanggal)
                            ->isoFormat('D MMM Y')
                        : '-'
                    }}

                </td>






                {{-- SISWA --}}

                <td class="px-4 py-2">


                    <span class="font-semibold text-gray-900">

                        {{ $record->siswa?->nama ?? '-' }}

                    </span>


                    <p class="text-[11px] text-gray-400">

                        NIS {{ $record->siswa?->nis ?? '-' }}

                    </p>


                </td>

                                {{-- BAKAT --}}

                <td class="px-4 py-2 text-sm text-gray-600">

                    <div class="truncate max-w-[150px]">

                        {{ $record->bakat ?? '-' }}

                    </div>

                </td>





                {{-- MINAT --}}

                <td class="px-4 py-2 text-sm text-gray-600">


                    <div class="truncate max-w-[150px]">

                        {{ $record->minat ?? '-' }}

                    </div>


                </td>






                {{-- BIDANG --}}

                <td class="px-4 py-2 text-sm text-gray-600">


                    <div class="truncate max-w-[180px]">

                        {{ $record->bidang ?? '-' }}

                    </div>


                </td>





                {{-- KESIMPULAN --}}

                <td class="px-4 py-2 text-sm text-gray-600">


                    <div class="truncate max-w-[220px]">

                        {{ $record->kesimpulan ?? '-' }}

                    </div>


                </td>






                {{-- AKSI --}}

                <td

                    class="px-4 py-2 text-right"

                    onclick="event.stopPropagation()"

                >

                    <div class="flex justify-end gap-2">



                        <x-atoms.action-button

                            color="blue"

                            title="Edit"

                            wire:click="loadTesBakatMinat({{ $record->id }})"

                        >

                            <x-atoms.icon

                                variant="edit"

                                size="sm"

                            />

                        </x-atoms.action-button>





                        <x-atoms.action-button

                            color="red"

                            title="Hapus"

                            wire:click="delete({{ $record->id }})"

                            wire:confirm="Yakin ingin menghapus data tes bakat minat ini?"

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






    {{-- =====================================================
        MODAL TES BAKAT MINAT
    ====================================================== --}}


    @include(
        'livewire.partials.asesmen.tes-bakat-minat.tes-bakat-minat-modal',
        [
            'editingId'=>$editingId
        ]
    )


</div>