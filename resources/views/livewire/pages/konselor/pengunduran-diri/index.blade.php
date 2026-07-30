<?php

use App\Livewire\Konselor\Bk\PengunduranDiri\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full" x-data="{ loading: false }"
    x-on:click="if ($event.target.closest('button[wire\\:click^=\'edit\'], button[wire\\:click=\'create\']')) loading = true"
    x-on:open-modal.window="loading = false" x-on:close-modal.window="loading = false">

    <x-organisms.header action="create">
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>
        Pengunduran Diri
    </x-organisms.header>

    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="$refresh">
        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot:pagination>
    </x-organisms.table-toolbar>

    @if($showFilters)
        <div class="px-6 sm:px-8 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-4 text-sm text-gray-600 shrink-0 transition-all">
            <span class="text-gray-500 text-xs font-medium">Filter Data:</span>
            <select wire:model.live="filterKelas"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-8 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Kelas</option>
                @foreach($kelasOptions as $k)
                    <option value="{{ $k }}">Kelas {{ $k }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterJurusan"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-32 pr-8 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Jurusan</option>
                @foreach($jurusanOptions as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterJenisKelamin"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-8 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
            @if($search !== '' || $filterKelas !== '' || $filterJurusan !== '' || $filterJenisKelamin !== '')
                <button wire:click="resetFilters" class="text-xs text-brand-teal font-medium hover:text-teal-700 hover:underline transition-colors ml-auto">Reset Semua</button>
            @endif
        </div>
    @endif

    @if(count($selected) > 0)
        <div class="px-6 py-2 bg-teal-50 border-b border-teal-100 flex justify-between items-center text-sm">
            <span class="text-xs font-medium text-brand-teal">{{ count($selected) }} data dipilih</span>
            <button wire:click="$set('selected', [])" class="text-xs text-gray-500 hover:text-gray-700">Batal Pilih</button>
        </div>
    @endif

    <div class="px-4 py-2">
        <x-shared.flash-message />
    </div>

    <x-organisms.data-table
        :headers="['', 'Tanggal', 'Siswa', 'J. Kelamin', 'Kelas', 'Orang Tua / Wali', 'Alasan', 'Aksi']"
        empty="Belum ada data pengunduran diri.">
        @forelse($records as $record)
            <tr wire:key="pd-{{ $record->id }}" wire:click="goToDetail({{ $record->id }})"
                class="group border-b border-gray-100 transition-all duration-200 h-12 relative cursor-pointer hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] hover:z-10 hover:rounded-md {{ in_array($record->id, $selected) ? 'bg-teal-50/50' : 'bg-white' }}">

                <td class="w-16 text-center align-middle rounded-l-md py-2" onclick="event.stopPropagation()">
                    <input type="checkbox" value="{{ $record->id }}" wire:model.live="selected"
                        class="w-4 h-4 rounded border-gray-300 text-brand-teal focus:ring-brand-teal accent-brand-teal cursor-pointer">
                </td>

                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap align-middle">
                    {{ \Carbon\Carbon::parse($record->tanggal_pengunduran)->isoFormat('D MMM Y') }}
                </td>

                <td class="px-4 py-2 font-semibold align-middle">
                    <span class="text-gray-900">{{ $record->siswa->nama ?? '-' }}</span>
                    <p class="text-[11px] text-gray-400">NIS {{ $record->siswa->nis ?? '-' }}</p>
                </td>

                @php
                    $jenisKelamin = $record->siswa?->jenis_kelamin ?? null;
                @endphp
                <td class="px-4 py-2 align-middle">
                    @if($jenisKelamin === 'L')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                            Laki-laki
                        </span>
                    @elseif($jenisKelamin === 'P')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-700">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                            Perempuan
                        </span>
                    @else
                        <span class="text-xs text-gray-400">-</span>
                    @endif
                </td>

                <td class="px-4 py-2 text-sm text-gray-600 align-middle">
                    {{ $record->siswa->kelas_label ?? '-' }} - {{ $record->siswa->jurusan_label ?? '-' }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600 align-middle">
                    {{ $record->nama_ortu_wali ?? '-' }}
                </td>

                <td class="px-4 py-2 text-sm text-gray-600 max-w-xs truncate align-middle">
                    {{ $record->alasan_pengunduran }}
                </td>

                <td class="px-4 py-2 text-right align-middle">
                    <div class="flex items-center justify-end gap-2" onclick="event.stopPropagation()">
                        <x-atoms.action-button color="blue" title="Edit" wire:click="edit({{ $record->id }})">
                            <x-atoms.icon variant="edit" size="sm" />
                        </x-atoms.action-button>
                        <x-atoms.action-button color="red" title="Hapus" wire:click="delete({{ $record->id }})"
                            wire:confirm="Yakin ingin menghapus pengunduran diri ini?">
                            <x-atoms.icon variant="delete" size="sm" />
                        </x-atoms.action-button>
                    </div>
                </td>
            </tr>
        @empty
        @endforelse
    </x-organisms.data-table>

    <livewire:partials.pengunduran-diri.pengunduran-diri-modal />
</div>