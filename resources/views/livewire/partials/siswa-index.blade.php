<?php

use App\Services\SiswaService;
use function Livewire\Volt\{state, computed};
use Livewire\WithPagination;

state([
    'search' => ''
]);

$students = computed(function () {

    $service = app(SiswaService::class);
    $data = $service->getAll();

    if ($this->search) {
        return $data->filter(
            fn($item) =>
            str($item->nama)->contains($this->search, true)
        )->values();
    }

    return $data;
});

?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full">

    <x-organisms.header action="create">

        <x-slot:search>
            <x-molecules.search-input model="search" />
            </x-slot>

            Konsultasi

    </x-organisms.header>

    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="$refresh">

        <x-slot:pagination>
            {{ count($this->students) }} data
            </x-slot>

    </x-organisms.table-toolbar>

    <x-organisms.data-table empty="Belum ada data siswa.">
        @foreach($this->students as $siswa)
            <tr wire:key="siswa-{{ $siswa->id }}"
                class="group border-b border-gray-100 bg-white transition-all duration-200 h-12 relative cursor-pointer hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] hover:z-10 hover:rounded-md">

                <td class="w-16 text-center align-middle rounded-l-md py-2" onclick="event.stopPropagation()">
                    <input type="checkbox"
                        class="w-4 h-4 rounded border-gray-300 text-brand-teal focus:ring-brand-teal accent-brand-teal cursor-pointer">
                </td>

                <td class="px-4 py-2 w-1/6 font-semibold text-gray-900 align-middle">{{ $siswa->nama }}</td>
                <td class="px-4 py-2 w-1/6 font-semibold text-gray-800 align-middle text-xs">{{ $siswa->nis }}</td>
                <td class="px-4 py-2 text-gray-500 max-w-xs align-middle text-xs">
                    <div class="truncate w-full">{{ $siswa->kelas }} - {{ $siswa->jurusan }}</div>
                </td>

                <td class="px-4 py-2 w-40 text-right align-middle relative rounded-r-md">
                    <span class="group-hover:opacity-0 font-medium text-gray-900 pr-2 transition-opacity text-xs">
                    </span>

                    <x-molecules.table-action>
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
</div>