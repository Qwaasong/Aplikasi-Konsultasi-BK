<?php

use App\Livewire\Konselor\Peminatan\Detail;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Detail {}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 min-h-screen p-6 lg:p-10">

    {{-- Top Navigation Header Wrapper --}}
    <div class="-mt-6 lg:-mt-10 -mx-6 lg:-mx-10 mb-8">
        <x-organisms.header>
            <x-slot:search>
                <div class="relative w-full max-w-md z-50">
                    <x-molecules.search-input model="search"/>

                    @if(strlen($search) >= 2)
                        <div class="absolute top-full left-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden w-full">
                            @forelse($this->searchResults as $result)
                                <a href="{{ route('konselor.peminatan.detail',$result->id) }}" wire:navigate
                                    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors">
                                    <div class="font-semibold text-gray-800 text-sm truncate">{{ $result->siswa->nama ?? 'Siswa' }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ optional($result->tanggal)->format('d M Y') }}
                                        &bull;
                                        {{ $result->hasil }}
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-3 text-sm text-gray-500">Tidak ada data ditemukan.</div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </x-slot:search>
        </x-organisms.header>
    </div>

    {{-- Header Detail --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <button wire:click="goBack" class="w-10 h-10 rounded-full flex items-center justify-center border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </button>
            <div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-100">Peminatan</span>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $record->siswa->nama ?? 'Siswa' }}</h1>
                <p class="text-xs text-gray-500 mt-0.5">NIS {{ $record->siswa->nis ?? '-' }} &middot; Kelas {{ $record->siswa->kelas_label }} - {{ $record->siswa->jurusan_label }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 self-end md:self-auto">
            {{-- Edit --}}
            <button wire:click="edit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-teal-700 bg-teal-50 hover:bg-teal-100 rounded-xl transition border border-teal-100/50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.112l-3.154 1.054a.75.75 0 01-.94-.94l1.054-3.154a4.5 4.5 0 011.112-1.89l13.416-13.416z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125L16.875 4.5" />
                </svg>
                Edit
            </button>
            {{-- Delete --}}
            <button wire:click="delete" wire:confirm="Yakin ingin menghapus data peminatan ini?"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition border border-red-100/50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                Hapus
            </button>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Kolom Kiri: Detail Peminatan --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Pilihan Jurusan --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-4">
                    Pilihan Jurusan
                </h3>

                <div class="space-y-3">

                    <div class="flex justify-between">
                        <span class="text-gray-500">Pilihan 1</span>
                        <span class="font-semibold">{{ $record->pilihan1 }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Pilihan 2</span>
                        <span class="font-semibold">{{ $record->pilihan2 }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Pilihan 3</span>
                        <span class="font-semibold">{{ $record->pilihan3 }}</span>
                    </div>

                </div>
            </div>

            {{-- Hasil Peminatan --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">

                <div class="p-2 rounded-xl bg-green-50 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="w-5 h-5">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75"/>
                    </svg>
                </div>

                    <h3 class="font-bold text-gray-900">
                        Hasil Peminatan
                    </h3>

                </div>

                <p class="text-lg font-semibold text-gray-900">
                    {{ $record->hasil }}
                </p>

            </div>

            {{-- Catatan Guru BK --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                <div class="p-2 rounded-xl bg-sky-50 text-sky-600">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="w-5 h-5">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19.5 21l-7.5-4.5L4.5 21V5.25A2.25 2.25 0 016.75 3h10.5A2.25 2.25 0 0119.5 5.25V21z"/>
                    </svg>
                </div>

                    <h3 class="font-bold text-gray-900">
                        Catatan Guru BK
                    </h3>

                </div>

                <p class="text-sm leading-relaxed whitespace-pre-line">

                    {{ $record->catatan ?: 'Belum ada catatan.' }}

                </p>

            </div>

            {{-- Informasi Data --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <h3 class="font-bold text-gray-900 border-b pb-4 mb-4">

                    Informasi Data

                </h3>

                <div class="grid grid-cols-2 gap-4">

                    <div class="p-4 rounded-xl bg-gray-50">

                        <span class="text-xs text-gray-400">

                            Tanggal Peminatan

                        </span>

                        <p class="font-semibold">

                            {{ optional($record->tanggal)->translatedFormat('d F Y') }}

                        </p>

                    </div>

                    <div class="p-4 rounded-xl bg-gray-50">

                        <span class="text-xs text-gray-400">

                            Dicatat Pada

                        </span>

                        <p class="font-semibold">

                            {{ optional($record->created_at)->translatedFormat('d F Y H:i') }}

                        </p>

                    </div>

                </div>

            </div>

        </div>

        {{-- Sidebar: Layanan Info & Lampiran --}}
        <div class="space-y-6">

            {{-- Status & Prioritas --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

            <h3 class="font-bold text-gray-900 border-b pb-4 mb-4">

                Informasi Siswa

            </h3>

            <div class="space-y-4">

                <div>

                    <p class="text-xs text-gray-400">

                        Nama

                    </p>

                    <p class="font-semibold">

                        {{ $record->siswa->nama }}

                    </p>

                </div>

                <div>

                    <p class="text-xs text-gray-400">

                        NIS

                    </p>

                    <p class="font-semibold">

                        {{ $record->siswa->nis }}

                    </p>

                </div>

                <div>

                    <p class="text-xs text-gray-400">

                        Kelas

                    </p>

                    <p class="font-semibold">

                        {{ $record->siswa->kelas_label }}

                    </p>

                </div>

                <div>

                    <p class="text-xs text-gray-400">

                        Jurusan

                    </p>

                    <p class="font-semibold">

                        {{ $record->siswa->jurusan_label }}

                    </p>

                </div>

            </div>

        </div>

        </div>
    </div>

    {{-- Flash Message & Modal --}}
    <div class="mt-6">
        <x-shared.flash-message />
    </div>

    <livewire:partials.peminatan.peminatan-modal />
</div>