<?php

use App\Livewire\Konselor\PengunduranDiri\Detail;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Detail {}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 min-h-screen p-6 lg:p-10">

    {{-- Top Navigation Header Wrapper --}}
    <div class="-mt-6 lg:-mt-10 -mx-6 lg:-mx-10 mb-8">
        <x-organisms.header>
            <x-slot:search>
                <div class="relative w-full max-w-md z-50">
                    <x-molecules.search-input model="search" />

                    @if(strlen($search) >= 2)
                        <div class="absolute top-full left-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden w-full">
                            @forelse($this->searchResults as $result)
                                <a href="{{ route('konselor.pengunduran-diri.detail', $result->id) }}" wire:navigate
                                    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors">
                                    <div class="text-sm font-semibold text-gray-800 truncate">{{ $result->siswa->nama ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $result->nama_ortu_wali }} &middot; {{ \Carbon\Carbon::parse($result->tanggal_pengunduran)->format('d M Y') }}</div>
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
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <button wire:click="goBack" class="w-10 h-10 rounded-full flex items-center justify-center border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </button>
            <div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">Pengunduran Diri</span>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $record->siswa->nama ?? '-' }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">NIS {{ $record->siswa->nis ?? '-' }} &middot; Kelas {{ $record->siswa->kelas_label ?? '-' }} - {{ $record->siswa->jurusan_label ?? '-' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 self-end sm:self-auto">
            <button wire:click="edit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-teal-700 bg-teal-50 hover:bg-teal-100 rounded-xl transition border border-teal-100/50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.112l-3.154 1.054a.75.75 0 01-.94-.94l1.054-3.154a4.5 4.5 0 011.112-1.89l13.416-13.416z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125L16.875 4.5" />
                </svg>
                Edit
            </button>
            <button wire:click="delete" wire:confirm="Yakin ingin menghapus pengunduran diri ini?"
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

        {{-- Kolom Kiri --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Alasan Pengunduran --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-red-50 text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Alasan Pengunduran Diri</h3>
                </div>
                <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line text-justify">
                    {{ $record->alasan_pengunduran ?: 'Tidak ada alasan yang dicantumkan.' }}
                </p>
            </div>

            {{-- Alamat Orang Tua / Wali --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-blue-50 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Alamat Orang Tua / Wali</h3>
                </div>
                <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line text-justify">
                    {{ $record->alamat_ortu_wali ?: 'Tidak ada alamat yang dicantumkan.' }}
                </p>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Informasi Pengunduran --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 text-[15px] border-b border-gray-100 pb-4 mb-4">Informasi Pengunduran</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Tanggal Pengunduran</span>
                        <span class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($record->tanggal_pengunduran)->locale('id')->translatedFormat('d F Y') }}
                        </span>
                    </div>
                    <div class="border-t border-gray-50 pt-4 mt-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-2">Orang Tua / Wali</span>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">{{ $record->nama_ortu_wali ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Siswa --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-purple-50 text-purple-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Data Siswa</h3>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-500/10 text-indigo-700 flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($record->siswa->nama ?? 'S', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-base font-bold text-gray-900">{{ $record->siswa->nama ?? '-' }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">
                            NIS {{ $record->siswa->nis ?? '-' }}
                            &middot; Kelas {{ $record->siswa->kelas_label ?? '-' }}
                            &middot; {{ $record->siswa->jurusan_label ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Dicatat --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 text-[15px] border-b border-gray-100 pb-4 mb-4">Dicatat</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Tanggal Input</span>
                        <span class="font-semibold text-gray-800">{{ optional($record->created_at)->translatedFormat('d F Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Terakhir Diperbarui</span>
                        <span class="font-semibold text-gray-800">{{ optional($record->updated_at)->translatedFormat('d F Y H:i') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Flash Message & Modal --}}
    <div class="mt-6">
        <x-shared.flash-message />
    </div>

    <livewire:partials.pengunduran-diri.pengunduran-diri-modal />
</div>