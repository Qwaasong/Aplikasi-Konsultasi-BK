<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\KonsultasiService;

new #[Layout('layouts.app')] class extends Component {

    public $search = '';

    public function with(): array
    {
        $service = app(KonsultasiService::class);
        $data = $service->getAll();

        if ($this->search) {
            $data = $data->filter(function ($item) {
                return str($item->siswa->nama_lengkap ?? 'Anonim')->contains($this->search, true) ||
                    str($item->jenis_layanan)->contains($this->search, true) ||
                    str($item->deskripsi_masalah)->contains($this->search, true);
            })->values();
        }

        return [
            'records' => $data
        ];
    }
    public function create()
    {
        // Placeholder for future create action
    }

    public function edit($id)
    {
        // Placeholder for future edit action
    }

    public function delete($id)
    {
        // Placeholder for future delete action
    }

    public function filterAction()
    {
        // Placeholder for filter
    }
}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full">

    <x-organisms.header action="create">
        <x-slot:search>
            <x-molecules.search-input model="search" />
            </x-slot>
            Konsultasi
    </x-organisms.header>

    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="$refresh">
        <x-slot:pagination>
            {{ count($records) }} data
            </x-slot>
    </x-organisms.table-toolbar>

    <div class="px-4 py-2">
        <x-shared.flash-message />
    </div>

    <x-organisms.data-table empty="Belum ada data konsultasi.">
        @foreach($records as $record)
            <tr wire:key="konsultasi-{{ $record->id }}"
                class="group border-b border-gray-100 bg-white transition-all duration-200 h-12 relative cursor-pointer hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] hover:z-10 hover:rounded-md">

                <td class="w-16 text-center align-middle rounded-l-md py-2" onclick="event.stopPropagation()">
                    <input type="checkbox"
                        class="w-4 h-4 rounded border-gray-300 text-brand-teal focus:ring-brand-teal accent-brand-teal cursor-pointer">
                </td>

                <td class="px-4 py-2 w-1/6 font-semibold text-gray-900 align-middle">
                    {{ $record->siswa->nama_lengkap ?? 'Anonim' }}</td>
                <td class="px-4 py-2 w-1/6 font-semibold text-gray-800 align-middle text-xs">{{ $record->jenis_layanan }}
                </td>
                <td class="px-4 py-2 text-gray-500 max-w-xs align-middle text-xs">
                    <div class="truncate w-full" title="{{ $record->deskripsi_masalah }}">{{ $record->deskripsi_masalah }}
                    </div>
                </td>

                <td class="px-4 py-2 w-40 text-right align-middle relative rounded-r-md">
                    <span class="group-hover:opacity-0 font-medium text-gray-900 pr-2 transition-opacity text-xs">
                        {{ \Carbon\Carbon::parse($record->tanggal)->format('d M y') }}
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