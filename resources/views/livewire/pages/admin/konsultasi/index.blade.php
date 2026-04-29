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
                return str($item->siswa->nama ?? 'Anonim')->contains($this->search, true) ||
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
        $this->dispatch('open-modal', 'tambah-konsultasi');
    }

    public function edit($id)
    {
        $this->dispatch('edit-konsultasi', id: $id);
    }

    public function delete($id)
    {
        $service = app(KonsultasiService::class);
        $service->delete($id);

        session()->flash('success', 'Konsultasi berhasil dihapus!');
    }

    public function filterAction()
    {
        // Placeholder for filter
    }
}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full" x-data="{ loading: false }"
    x-on:click="if ($event.target.closest('button[wire\\:click^=\'edit\'], button[wire\\:click=\'create\']')) loading = true"
    x-on:open-modal.window="loading = false" x-on:close-modal.window="loading = false">

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
                    {{ $record->siswa->nama ?? 'Anonim' }}
                </td>
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

    <livewire:partials.konsultasi.konsultasi-modal />

    <!-- Skeleton Loading Modal Overlay -->
    <div x-show="loading" style="display: none;" x-transition:leave="transition-opacity duration-300 ease-in"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>

        <!-- Skeleton Modal Panel -->
        <div
            class="bg-white rounded-xl shadow-2xl flex flex-col w-full sm:max-w-lg h-full max-h-[80vh] overflow-hidden relative z-10 transition-all">
            <!-- Header Skeleton -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 shrink-0 flex flex-col gap-2">
                <div class="h-5 bg-gray-200 rounded-md w-1/3 animate-pulse"></div>
                <div class="h-3 bg-gray-200 rounded-md w-1/4 animate-pulse"></div>
            </div>

            <!-- Body Skeleton (Scrollable like original) -->
            <div class="px-6 py-6 overflow-y-auto modal-scroll grow flex flex-col gap-5" style="scrollbar-width: thin;">
                <!-- Siswa Progress Bar -->
                <div>
                    <div class="h-4 bg-gray-200 rounded w-1/4 mb-3 animate-pulse"></div>
                    <div class="flex gap-2.5">
                        <div class="h-2.5 w-1/2 bg-gray-200 rounded-full animate-pulse"></div>
                        <div class="h-2.5 w-1/2 bg-gray-100 rounded-full animate-pulse"></div>
                    </div>
                </div>

                <!-- Input Skeletons -->
                <div>
                    <div class="h-4 bg-gray-200 rounded w-1/5 mb-2 animate-pulse"></div>
                    <div class="h-[74px] bg-gray-100 rounded-lg w-full animate-pulse border border-gray-100"></div>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <div class="h-4 bg-gray-200 rounded w-1/3 mb-2 animate-pulse"></div>
                        <div class="h-10 bg-gray-100 rounded border border-gray-100 animate-pulse"></div>
                    </div>
                    <div>
                        <div class="h-4 bg-gray-200 rounded w-1/3 mb-2 animate-pulse"></div>
                        <div class="h-10 bg-gray-100 rounded border border-gray-100 animate-pulse"></div>
                    </div>
                </div>
            </div>

            <!-- Footer Skeleton -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-3">
                <div class="h-10 w-24 bg-gray-200 rounded-md animate-pulse"></div>
                <div class="h-10 w-32 bg-gray-300 rounded-md animate-pulse"></div>
            </div>
        </div>
    </div>
</div>