<?php

use App\Livewire\Admin\LogKasus\Detail;
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
                                <a href="{{ route('admin.log-kasus.detail', $result->id) }}" wire:navigate
                                    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors">
                                    <div class="font-semibold text-gray-800 text-sm truncate">
                                        {{ $result->siswa->nama ?? 'Siswa' }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ optional($result->tanggal_mulai)->format('d M Y') }}
                                        &bull;
                                        {{ $result->penanganan }}
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
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <button wire:click="goBack"
                class="w-10 h-10 rounded-full flex items-center justify-center border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </button>

            <div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                    Log Kasus
                </span>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">Detail Log Kasus</h1>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Kolom Kiri --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Data Siswa --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-purple-50 text-purple-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Data Siswa</h3>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-500/10 text-indigo-700 flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($record->siswa?->nama ?? 'S', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-base font-bold text-gray-900">{{ $record->siswa?->nama ?? '-' }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">
                            NIS {{ $record->siswa?->nis ?? '-' }}
                            &middot; Kelas {{ $record->siswa?->kelas_label ?? '-' }}
                            &middot; {{ $record->siswa?->jurusan_label ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Uraian Masalah --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Uraian Masalah</h3>
                </div>

                <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line text-justify">
                    {{ $record->uraian_masalah ?: 'Tidak ada uraian masalah yang dicantumkan.' }}
                </p>
            </div>

            {{-- Penanganan --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-teal-50 text-teal-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Penanganan</h3>
                </div>

                <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line text-justify">
                    {{ $record->penanganan ?: 'Tidak ada catatan penanganan.' }}
                </p>
            </div>

            {{-- Tindak Lanjut --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-sky-50 text-sky-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3-3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Tindak Lanjut</h3>
                </div>

                <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line text-justify">
                    {{ $record->tindak_lanjut ?: 'Belum ada catatan tindak lanjut.' }}
                </p>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Informasi Kasus --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 text-[15px] border-b border-gray-100 pb-4 mb-4">Informasi Kasus</h3>

                <div class="space-y-4 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500">Tanggal Mulai</span>
                        <span class="font-medium text-gray-900 text-right">
                            {{ optional($record->tanggal_mulai)->translatedFormat('d F Y') ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500">Tanggal Selesai</span>
                        <span class="font-medium text-gray-900 text-right">
                            {{ optional($record->tanggal_selesai)->translatedFormat('d F Y') ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center gap-4">
                        <span class="text-gray-500">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ match($record->status) {
                                'Open' => 'bg-green-100 text-green-700',
                                'Pending' => 'bg-yellow-100 text-yellow-700',
                                'Closed' => 'bg-blue-100 text-blue-700',
                                default => 'bg-gray-100 text-gray-700',
                            } }}">
                            {{ $record->status }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center gap-4">
                        <span class="text-gray-500">Prioritas</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ match($record->prioritas) {
                                'Tinggi' => 'bg-red-100 text-red-700',
                                'Sedang' => 'bg-yellow-100 text-yellow-700',
                                'Rendah' => 'bg-green-100 text-green-700',
                                default => 'bg-gray-100 text-gray-700',
                            } }}">
                            {{ $record->prioritas }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500">Kategori</span>
                        <span class="font-medium text-gray-900 text-right">{{ $record->kategori?->nama_kategori ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500">Guru BK</span>
                        <span class="font-medium text-gray-900 text-right">{{ $record->guruBk?->user?->nama ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Lampiran --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 text-[15px] border-b border-gray-100 pb-4 mb-4">Lampiran Pendukung</h3>

                @if($record->lampirans && $record->lampirans->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($record->lampirans as $lampiran)
                            @php
                                $fileUrl = asset('storage/' . $lampiran->path_file);
                                $ext = strtolower(pathinfo($lampiran->nama_file, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
                            @endphp
                            <a href="{{ $fileUrl }}" target="_blank"
                                class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-indigo-100 hover:bg-indigo-50/10 transition-all">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                                    {{ $isImage ? 'bg-purple-50 text-purple-600' : 'bg-orange-50 text-orange-600' }}">
                                    @if($isImage)
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5Z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="overflow-hidden">
                                    <p class="font-semibold text-sm text-gray-800 truncate" title="{{ $lampiran->nama_file }}">
                                        {{ $lampiran->nama_file }}
                                    </p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">Klik untuk melihat file</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="border border-dashed border-gray-200 rounded-xl p-8 text-center bg-gray-50/50">
                        <p class="text-sm text-gray-400 font-medium">Tidak ada lampiran pendukung.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Flash Message --}}
    <div class="mt-6">
        <x-shared.flash-message />
    </div>
</div>
