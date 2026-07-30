<?php

use App\Livewire\Konselor\Asesmen\Dcm\Detail;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Detail {};

?>

<div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 min-h-screen p-6 lg:p-10">

    {{-- ================= HEADER ================= --}}
    <div class="-mt-6 lg:-mt-10 -mx-6 lg:-mx-10 mb-8">
        <x-organisms.header>
            <x-slot:search>

                <div class="relative w-full max-w-md z-50">

                    <x-molecules.search-input model="search" />

                    @if(strlen($search) >= 2)

                        <div
                            class="absolute top-full left-0 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">

                            @forelse($this->searchResults as $result)

                                <a
                                    href="{{ route('konselor.asesmen.dcm.detail',$result->id) }}"
                                    wire:navigate
                                    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition">

                                    <div class="text-sm font-semibold text-gray-800">

                                        {{ $result->siswa?->nama ?? '-' }}

                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">

                                        NIS {{ $result->siswa?->nis ?? '-' }}

                                        &middot;

                                        {{ \Carbon\Carbon::parse($result->tanggal)->format('d M Y') }}

                                    </div>

                                </a>

                            @empty

                                <div class="px-4 py-3 text-sm text-gray-500">

                                    Tidak ada data ditemukan.

                                </div>

                            @endforelse

                        </div>

                    @endif

                </div>

            </x-slot:search>
        </x-organisms.header>
    </div>

    {{-- ================= HEADER DETAIL ================= --}}

    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">

        <div class="flex items-center gap-4">

            <button
                wire:click="goBack"
                class="w-10 h-10 rounded-full flex items-center justify-center border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition">

                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="2.5"
                     stroke="currentColor"
                     class="w-5 h-5">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>

                </svg>

            </button>

            <div>

                <span
                    class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">

                    Daftar Cek Masalah

                </span>

                <h1 class="text-2xl font-bold text-gray-900 mt-1">

                    Detail DCM

                </h1>

            </div>

        </div>

        <div class="flex items-center gap-3">

            {{-- Edit --}}
            <button
                wire:click="edit"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-teal-50 text-teal-700 border border-teal-100 hover:bg-teal-100">

                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="2"
                     stroke="currentColor"
                     class="w-4 h-4">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.112l-3.154 1.054a.75.75 0 01-.94-.94l1.054-3.154a4.5 4.5 0 011.112-1.89l13.416-13.416z"/>

                </svg>

                Edit

            </button>

            {{-- Delete --}}
            <button
                wire:click="delete"
                wire:confirm="Yakin ingin menghapus data DCM ini ?"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-red-50 text-red-700 border border-red-100 hover:bg-red-100">

                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="2"
                     stroke="currentColor"
                     class="w-4 h-4">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166"/>

                </svg>

                Hapus

            </button>

        </div>

    </div>

    {{-- ================= CONTENT ================= --}}

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- LEFT CONTENT --}}
        <div class="lg:col-span-2 space-y-6">
                    {{-- ================= DATA SISWA ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">

                    <div class="p-2 rounded-xl bg-purple-50 text-purple-600">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="2"
                             stroke="currentColor"
                             class="w-5 h-5">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>

                        </svg>

                    </div>

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Data Siswa

                    </h3>

                </div>

                <div class="flex items-center gap-4">

                    <div
                        class="w-14 h-14 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-lg">

                        {{ $record->siswa?->initials ?? 'S' }}

                    </div>

                    <div>

                        <p class="text-lg font-bold text-gray-900">

                            {{ $record->siswa?->nama ?? '-' }}

                        </p>

                        <p class="text-sm text-gray-500 mt-1">

                            NIS {{ $record->siswa?->nis ?? '-' }}

                            &middot;

                            Kelas {{ $record->siswa?->kelas_label ?? '-' }}

                            &middot;

                            {{ $record->siswa?->jurusan_label ?? '-' }}

                        </p>

                    </div>

                </div>

            </div>

            {{-- ================= MASALAH TERIDENTIFIKASI ================= --}}

            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-5">

                    <div class="p-2 rounded-xl bg-red-50 text-red-600">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="2"
                             stroke="currentColor"
                             class="w-5 h-5">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M12 9v3.75m0 3.75h.008v.008H12v-.008ZM21 12a9 9 0 11-18 0 9 9 0 0118 0Z"/>

                        </svg>

                    </div>

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Masalah Teridentifikasi

                    </h3>

                </div>

                @if(!empty($record->masalah_teridentifikasi))

                    <div class="space-y-3">

                        @foreach($record->masalah_teridentifikasi as $masalah)

                            <div
                                class="flex items-start gap-3 p-4 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/30 transition-all">

                                <div
                                    class="w-8 h-8 rounded-full bg-red-100 text-red-700 flex items-center justify-center shrink-0 font-bold">

                                    ✓

                                </div>

                                <div class="flex-1">

                                    <p class="text-sm font-medium text-gray-800">

                                        {{ $masalah }}

                                    </p>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div
                        class="border border-dashed border-gray-200 rounded-xl p-8 text-center bg-gray-50">

                        <p class="text-sm text-gray-400">

                            Tidak ada masalah yang dipilih.

                        </p>

                    </div>

                @endif

            </div>
                        {{-- ================= KESIMPULAN ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">

                    <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="2"
                             stroke="currentColor"
                             class="w-5 h-5">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0Z"/>

                        </svg>

                    </div>

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Kesimpulan

                    </h3>

                </div>

                <div
                    class="rounded-xl bg-gray-50 border border-gray-100 p-5">

                    <p
                        class="text-sm leading-7 text-gray-700 whitespace-pre-line text-justify">

                        {{ $record->kesimpulan ?: 'Belum ada kesimpulan yang ditambahkan.' }}

                    </p>

                </div>

            </div>

            {{-- ================= CATATAN KONSELOR ================= --}}
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
                                  d="M16.862 4.487a2.25 2.25 0 113.182 3.182L7.5 20.213 3 21l.787-4.5L16.862 4.487Z"/>

                        </svg>

                    </div>

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Catatan Konselor

                    </h3>

                </div>

                <div
                    class="rounded-xl bg-gray-50 border border-gray-100 p-5">

                    <p
                        class="text-sm leading-7 text-gray-700 whitespace-pre-line text-justify">

                        {{ $record->catatan ?: 'Belum ada catatan konselor.' }}

                    </p>

                </div>

            </div>

        </div>

        {{-- ================= SIDEBAR ================= --}}
        <div class="space-y-6">
                    {{-- ================= INFORMASI DCM ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <h3 class="font-bold text-gray-900 text-[15px] border-b border-gray-100 pb-4 mb-4">

                    Informasi DCM

                </h3>

                <div class="space-y-4">

                    <div class="flex justify-between items-center text-sm">

                        <span class="text-gray-500">
                            Tanggal Pengisian
                        </span>

                        <span class="font-semibold text-gray-800">

                            {{ $record->tanggal
                                ? \Carbon\Carbon::parse($record->tanggal)->locale('id')->translatedFormat('d F Y')
                                : '-' }}

                        </span>

                    </div>

                    <div class="flex justify-between items-center text-sm">

                        <span class="text-gray-500">
                            Dibuat
                        </span>

                        <span class="font-semibold text-gray-800">

                            {{ $record->created_at
                                ? $record->created_at->locale('id')->translatedFormat('d M Y H:i')
                                : '-' }}

                        </span>

                    </div>

                    <div class="flex justify-between items-center text-sm">

                        <span class="text-gray-500">
                            Terakhir Diubah
                        </span>

                        <span class="font-semibold text-gray-800">

                            {{ $record->updated_at
                                ? $record->updated_at->locale('id')->translatedFormat('d M Y H:i')
                                : '-' }}

                        </span>

                    </div>

                </div>

            </div>

            {{-- ================= RINGKASAN DCM ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Ringkasan DCM

                    </h3>

                    <span
                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">

                        {{ count($record->masalah_teridentifikasi ?? []) }}

                    </span>

                </div>

                <div class="space-y-4">

                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">

                        <div class="text-xs uppercase tracking-wider text-gray-400 font-semibold">

                            Total Masalah

                        </div>

                        <div class="mt-2 text-3xl font-bold text-indigo-700">

                            {{ count($record->masalah_teridentifikasi ?? []) }}

                        </div>

                        <div class="text-sm text-gray-500 mt-1">

                            Masalah teridentifikasi

                        </div>

                    </div>

                    <div class="rounded-xl border border-gray-100 p-4">

                        <div class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-3">

                            Status Data

                        </div>

                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">

                            DCM Selesai

                        </span>

                    </div>

                </div>

            </div>

            {{-- ================= DAFTAR MASALAH ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Daftar Masalah

                    </h3>

                    <span class="text-xs text-gray-400">

                        {{ count($record->masalah_teridentifikasi ?? []) }} item

                    </span>

                </div>

                @if(!empty($record->masalah_teridentifikasi))

                    <div class="space-y-2 max-h-[320px] overflow-y-auto pr-1">

                        @foreach($record->masalah_teridentifikasi as $index => $masalah)

                            <div
                                class="flex gap-3 items-start rounded-xl border border-gray-100 p-3 hover:bg-indigo-50/20 transition">

                                <div
                                    class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0">

                                    {{ $index + 1 }}

                                </div>

                                <p class="text-sm text-gray-700">

                                    {{ $masalah }}

                                </p>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div
                        class="border border-dashed border-gray-200 rounded-xl p-8 text-center bg-gray-50">

                        <p class="text-sm text-gray-400">

                            Belum ada data masalah.

                        </p>

                    </div>

                @endif

            </div>
                    </div>
    </div>

    {{-- ================= FLASH MESSAGE ================= --}}
    <div class="mt-6">
        <x-shared.flash-message />
    </div>

    {{-- ================= MODAL DCM ================= --}}
</div>