<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {

    public string $search = '';
    public bool $selectAll = false;

    public array $records = [];

    public function create()
    {
        $this->dispatch('create-alih-tangan-kasus');
    }

};

?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full">

    {{-- Header --}}
    <x-organisms.header action="create">
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>

        Alih Tangan Kasus
    </x-organisms.header>

    {{-- Toolbar --}}
    <x-organisms.table-toolbar>
        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot:pagination>

        <x-slot:actions>
            <x-atoms.button
                wire:click="$dispatch('create-alih-tangan-kasus')">
                Tambah Alih Tangan Kasus
            </x-atoms.button>
        </x-slot:actions>

    </x-organisms.table-toolbar>

    {{-- Flash Message --}}
    <div class="px-4 py-2">
        <x-shared.flash-message />
    </div>

    {{-- Data Table --}}
    <x-organisms.data-table empty="Belum ada data alih tangan kasus.">

        @forelse($records as $record)

            <tr
                class="group border-b border-gray-100 transition-all duration-200 h-12 relative cursor-pointer hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] hover:z-10 hover:rounded-md bg-white">

                <td class="w-16 text-center align-middle py-2">
                    <input
                        type="checkbox"
                        class="w-4 h-4 rounded border-gray-300 accent-brand-teal">
                </td>

                <td class="px-4 py-2 font-semibold">
                    {{ $record['nama'] }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600">
                    {{ $record['kelas'] }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600">
                    {{ $record['orang_tua'] }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600">
                    {{ $record['tanggal'] }}
                </td>

                <td class="px-4 py-2 text-right">
                    -
                </td>

            </tr>

        @empty

        @endforelse

    </x-organisms.data-table>

    <livewire:partials.alih-tangan-kasus.alih-tangan-kasus-modal />

</div>