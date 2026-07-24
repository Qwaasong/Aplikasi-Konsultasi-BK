<?php

use App\Livewire\Konselor\KunjunganRumah\Detail;
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
                                <a href="{{ route('konselor.home-visit.detail',$result->id) }}" wire:navigate
                                    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors">
                                    <div class="font-semibold text-gray-800 text-sm truncate">{{ $result->siswa->nama ?? 'Siswa' }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $result->judul }} &bull; {{ optional($result->tanggal_konsultasi)->format('d M Y') }}</div>
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
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-100">Kunjungan Rumah (Home Visit)</span>
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
            <button wire:click="delete" wire:confirm="Yakin ingin menghapus data kunjungan rumah ini?"
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

        {{-- Kolom Kiri: Detil Kunjungan --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Judul Kunjungan --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Judul Kunjungan</h3>
                <p class="text-lg font-bold text-gray-900 leading-snug">
                    {{ $record->judul }}
                </p>
            </div>

            {{-- Hasil Kunjungan --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Uraian Masalah & Hasil Kunjungan</h3>
                </div>
                <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line text-justify">
                    {{ $record->isi_konsultasi ?: 'Belum ada catatan hasil kunjungan.' }}
                </p>
            </div>

            {{-- Hasil Tindak Lanjut --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-sky-50 text-sky-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Tindakan & Hasil Tindak Lanjut</h3>
                </div>
                <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line text-justify">
                    {{ $record->hasil_tindak_lanjut ?: 'Belum ada hasil tindak lanjut.' }}
                </p>
            </div>

            {{-- Ringkasan --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 text-[15px] border-b border-gray-100 pb-4 mb-4">Metadata Kunjungan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1 p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Format Layanan</span>
                        <span class="text-sm font-semibold text-gray-800">Kunjungan Rumah (Home Visit)</span>
                    </div>
                    <div class="flex flex-col gap-1 p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Dicatat Pada</span>
                        <span class="text-sm font-semibold text-gray-800">{{ optional($record->created_at)->translatedFormat('d F Y, H:i') }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar: Layanan Info & Lampiran --}}
        <div class="space-y-6">

            {{-- Status & Prioritas --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm space-y-4">
                <div>
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-2">Status Kunjungan</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                        @if($record->status == 'Open' || $record->status == 'diproses')
                            bg-blue-50 text-blue-700 border border-blue-100
                        @elseif($record->status == 'Diproses' || $record->status == 'ditunda')
                            bg-amber-50 text-amber-700 border border-amber-100
                        @else
                            bg-emerald-50 text-emerald-700 border border-emerald-100
                        @endif
                    ">
                        {{ ucfirst($record->status) }}
                    </span>
                </div>
                <div class="border-t border-gray-50 pt-4">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-2">Tingkat Prioritas</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                        @if($record->prioritas == 'Tinggi')
                            bg-red-50 text-red-700 border border-red-100
                        @elseif($record->prioritas == 'Sedang')
                            bg-amber-50 text-amber-700 border border-amber-100
                        @else
                            bg-emerald-50 text-emerald-700 border border-emerald-100
                        @endif
                    ">
                        {{ $record->prioritas }}
                    </span>
                </div>
            </div>

            {{-- Metadata Layanan --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 text-[15px] border-b border-gray-100 pb-4 mb-4">Informasi Tambahan</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Tanggal Kunjungan</span>
                        <span class="font-semibold text-gray-800">
                            {{ optional($record->tanggal_konsultasi)->locale('id')->translatedFormat('d F Y') }}
                        </span>
                    </div>
                    <div class="border-t border-gray-50 pt-4">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-2">Guru BK Pelaksana</span>
                        <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($record->guruBk->user->nama ?? 'G', 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $record->guruBk->user->nama ?? '-' }}</p>
                                <p class="text-[11px] text-gray-500">NIP. {{ $record->guruBk->nip ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lampiran --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 text-[15px] border-b border-gray-100 pb-4 mb-4">Lampiran Pendukung</h3>

                @if($record->lampirans && $record->lampirans->count())
                    <div class="space-y-3">
                        @foreach($record->lampirans as $lampiran)
                            @php
                                $url = asset('storage/'.$lampiran->path_file);
                                $ext = strtolower(pathinfo($lampiran->nama_file, PATHINFO_EXTENSION));
                                $image = in_array($ext,['jpg','jpeg','png']);
                            @endphp
                            <a href="{{ $url }}" target="_blank"
                                class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-teal-100 hover:bg-teal-50/10 transition-all">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                                    {{ $image ? 'bg-purple-50 text-purple-600' : 'bg-orange-50 text-orange-600' }}">
                                    @if($image)
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
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

    {{-- Flash Message & Modal --}}
    <div class="mt-6">
        <x-shared.flash-message />
    </div>

    <livewire:partials.home-visit.home-visit-modal />
</div>