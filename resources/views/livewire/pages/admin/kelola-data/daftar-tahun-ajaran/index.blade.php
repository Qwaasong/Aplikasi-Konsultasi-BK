<?php

use App\Livewire\Admin\KelolaData\DaftarTahunAjaran\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app', ['title' => 'Kelola Tahun Ajaran'])] class extends Index {}; ?>
<div class="flex-1 flex flex-col min-w-0 bg-white h-full">
    
<x-organisms.header action="create">

    <x-slot:search>

        <x-molecules.search-input model="search"/>

    </x-slot>

    Tambah Tahun Ajaran

</x-organisms.header>

<x-organisms.table-toolbar onRefresh="$refresh">

    <x-slot:pagination>

        {{ count($records) }} data

    </x-slot>

</x-organisms.table-toolbar>

<x-organisms.data-table
    :headers="[
        '',
        'Tahun',
        'Semester',
        'Status',
        'Aksi'
    ]"
    empty="Belum ada data tahun ajaran."
>

@foreach($records as $record)

<tr
    wire:key="tahun-{{ $record->id }}"
    class="group border-b border-gray-100 transition-all duration-200
        {{ in_array($record->id,$selected) ? "bg-teal-50/50" : "bg-white" }}">

    <td class="w-16 text-center">

        <input
            type="checkbox"
            value="{{ $record->id }}"
            wire:model.live="selected"
            class="w-4 h-4 rounded border-gray-300">

    </td>

    <td class="px-4 py-2 font-medium">

        {{ $record->tahun }}

    </td>

    <td class="px-4 py-2">

        {{ $record->semester }}

    </td>

    <td class="px-4 py-2">

        @if($record->status_aktif)

            <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">

                Aktif

            </span>

        @else

            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">

                Tidak Aktif

            </span>

        @endif

    </td>

    <td class="relative px-4 py-2 text-right">

        <x-molecules.table-action :id="$record->id">

            <x-slot:edit>
                <span class="sr-only">Edit</span>
            </x-slot>

            <x-slot:delete>
                <span class="sr-only">Delete</span>
            </x-slot>

        </x-molecules.table-action>

    </td>

</tr>

@endforeach

</x-organisms.data-table>

<livewire:partials.admin.kelola-data.daftar-tahun-ajaran.daftar-tahun-ajaran-modal />

</div>