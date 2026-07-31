<?php

use App\Livewire\Konselor\Asesmen\TesBakatMinat\Detail;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Detail {};

?>

<div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 min-h-screen p-6 lg:p-10">

    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <div class="-mt-6 lg:-mt-10 -mx-6 lg:-mx-10 mb-8">

        <x-organisms.header>

            <x-slot:search>

                <div class="relative w-full max-w-md z-50">

                    <x-molecules.search-input model="search"/>

                    @if(strlen($search) >= 2)

                        <div class="absolute top-full left-0 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">

                            @forelse($this->searchResults as $result)

                                <a
                                    href="{{ route('konselor.asesmen.tes-bakat-minat.detail',$result->id) }}"
                                    wire:navigate
                                    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition"
                                >

                                    <div class="font-semibold text-sm text-gray-800 truncate">

                                        {{ $result->siswa->nama ?? 'Siswa' }}

                                    </div>

                                    <div class="text-xs text-gray-500 mt-0.5">

                                        {{ optional($result->tanggal)->format('d M Y') }}

                                        &bull;

                                        {{ $result->hasil }}

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



    {{-- =====================================================
        HEADER DETAIL
    ====================================================== --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">

        <div class="flex items-center gap-4">

            <button
                wire:click="goBack"
                class="w-10 h-10 rounded-full border border-gray-200 bg-white flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition shadow-sm"
            >

                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2.5"
                    stroke="currentColor"
                    class="w-5 h-5">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>

                </svg>

            </button>

            <div>

                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-100">

                    Tes Bakat Minat

                </span>

                <h1 class="text-2xl font-bold text-gray-900 mt-1">

                    {{ $record->siswa->nama ?? 'Siswa' }}

                </h1>

                <p class="text-xs text-gray-500 mt-1">

                    NIS {{ $record->siswa->nis ?? '-' }}

                    &middot;

                    Kelas {{ $record->siswa->kelas_label ?? '-' }}

                    -

                    {{ $record->siswa->jurusan_label ?? '-' }}

                </p>

            </div>

        </div>



        {{-- ACTION --}}
        <div class="flex items-center gap-3">

            <button
                wire:click="edit"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-50 text-teal-700 border border-teal-100 hover:bg-teal-100 transition"
            >

                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                    class="w-4 h-4">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.112l-3.154 1.054a.75.75 0 01-.94-.94l1.054-3.154a4.5 4.5 0 011.112-1.89l13.416-13.416z"/>

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 7.125L16.875 4.5"/>

                </svg>

                Edit

            </button>



            <button
                wire:click="delete"
                wire:confirm="Yakin ingin menghapus hasil Tes Bakat Minat ini?"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 transition"
            >

                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                    class="w-4 h-4">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0115.916 21.75H8.084A2.25 2.25 0 015.84 19.673L4.772 5.79"/>

                </svg>

                Hapus

            </button>

        </div>

    </div>

    {{-- =======================================================
    CONTENT GRID
======================================================= --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- ===================================================
        KOLOM KIRI
    ==================================================== --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Pilihan Bakat Minat --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            <h3 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-5">
                Pilihan Bakat Minat
            </h3>

            <div class="space-y-4">

                <div class="flex justify-between items-center">

                    <span class="text-gray-500">
                        Pilihan Pertama
                    </span>

                    <span class="font-semibold text-gray-900">
                        {{ $record->pilihan1 ?: '-' }}
                    </span>

                </div>

                <div class="flex justify-between items-center">

                    <span class="text-gray-500">
                        Pilihan Kedua
                    </span>

                    <span class="font-semibold text-gray-900">
                        {{ $record->pilihan2 ?: '-' }}
                    </span>

                </div>

                <div class="flex justify-between items-center">

                    <span class="text-gray-500">
                        Pilihan Ketiga
                    </span>

                    <span class="font-semibold text-gray-900">
                        {{ $record->pilihan3 ?: '-' }}
                    </span>

                </div>

            </div>

        </div>


        {{-- Hasil Tes --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-5">

                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">

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

                <div>

                    <h3 class="font-bold text-gray-900">
                        Hasil Tes Bakat Minat
                    </h3>

                    <p class="text-xs text-gray-400">
                        Rekomendasi utama berdasarkan asesmen
                    </p>

                </div>

            </div>

            <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-5">

                <div class="text-xs uppercase tracking-wide text-emerald-600 font-semibold mb-2">
                    Hasil
                </div>

                <div class="text-xl font-bold text-emerald-800">
                    {{ $record->hasil ?: '-' }}
                </div>

            </div>

        </div>


        {{-- Catatan BK --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-5">

                <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="2"
                         stroke="currentColor"
                         class="w-5 h-5">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M7.5 8.25h9m-9 3h6m5.25 8.25H5.25A2.25 2.25 0 013 17.25V6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75v10.5A2.25 2.25 0 0118.75 19.5z"/>

                    </svg>

                </div>

                <div>

                    <h3 class="font-bold text-gray-900">
                        Catatan Guru BK
                    </h3>

                    <p class="text-xs text-gray-400">
                        Hasil observasi dan rekomendasi
                    </p>

                </div>

            </div>

            <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">

                {{ $record->catatan ?: 'Belum ada catatan dari Guru BK.' }}

            </div>

        </div>


        {{-- Informasi Tes --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            <h3 class="font-bold text-gray-900 border-b border-gray-100 pb-4 mb-5">
                Informasi Tes
            </h3>

            <div class="grid md:grid-cols-2 gap-4">

                <div class="bg-gray-50 rounded-xl p-4">

                    <div class="text-xs text-gray-400 mb-1">
                        Tanggal Tes
                    </div>

                    <div class="font-semibold text-gray-900">
                        {{ optional($record->tanggal)->translatedFormat('d F Y') }}
                    </div>

                </div>

                <div class="bg-gray-50 rounded-xl p-4">

                    <div class="text-xs text-gray-400 mb-1">
                        Dibuat Pada
                    </div>

                    <div class="font-semibold text-gray-900">
                        {{ optional($record->created_at)->translatedFormat('d F Y H:i') }}
                    </div>

                </div>

                <div class="bg-gray-50 rounded-xl p-4">

                    <div class="text-xs text-gray-400 mb-1">
                        Terakhir Diubah
                    </div>

                    <div class="font-semibold text-gray-900">
                        {{ optional($record->updated_at)->translatedFormat('d F Y H:i') }}
                    </div>

                </div>

            </div>

        </div>

    </div>

        {{-- ===================================================
        SIDEBAR
    ==================================================== --}}
    <div class="space-y-6">

        {{-- Informasi Siswa --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            <h3 class="font-bold text-gray-900 border-b border-gray-100 pb-4 mb-5">
                Informasi Siswa
            </h3>

            <div class="space-y-4">

                <div>
                    <p class="text-xs text-gray-400">
                        Nama Lengkap
                    </p>

                    <p class="font-semibold text-gray-900">
                        {{ $record->siswa->nama ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">
                        NIS
                    </p>

                    <p class="font-semibold text-gray-900">
                        {{ $record->siswa->nis ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">
                        Kelas
                    </p>

                    <p class="font-semibold text-gray-900">
                        {{ $record->siswa->kelas_label ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">
                        Jurusan
                    </p>

                    <p class="font-semibold text-gray-900">
                        {{ $record->siswa->jurusan_label ?? '-' }}
                    </p>
                </div>

            </div>

        </div>


        {{-- Ringkasan Tes --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            <h3 class="font-bold text-gray-900 border-b border-gray-100 pb-4 mb-5">
                Ringkasan Tes
            </h3>

            <div class="space-y-5">

                <div>

                    <p class="text-xs uppercase tracking-wider text-gray-400 mb-2">
                        Rekomendasi Utama
                    </p>

                    <span
                        class="inline-flex items-center px-4 py-2 rounded-full
                               bg-emerald-100 text-emerald-700
                               text-sm font-semibold"
                    >
                        {{ $record->hasil ?: '-' }}
                    </span>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wider text-gray-400 mb-2">
                        Prioritas Pilihan
                    </p>

                    <div class="space-y-2">

                        <div
                            class="flex justify-between items-center
                                   rounded-lg bg-gray-50 px-3 py-2"
                        >
                            <span class="text-gray-500 text-sm">
                                Pilihan 1
                            </span>

                            <span class="font-semibold text-gray-900">
                                {{ $record->pilihan1 ?: '-' }}
                            </span>
                        </div>

                        <div
                            class="flex justify-between items-center
                                   rounded-lg bg-gray-50 px-3 py-2"
                        >
                            <span class="text-gray-500 text-sm">
                                Pilihan 2
                            </span>

                            <span class="font-semibold text-gray-900">
                                {{ $record->pilihan2 ?: '-' }}
                            </span>
                        </div>

                        <div
                            class="flex justify-between items-center
                                   rounded-lg bg-gray-50 px-3 py-2"
                        >
                            <span class="text-gray-500 text-sm">
                                Pilihan 3
                            </span>

                            <span class="font-semibold text-gray-900">
                                {{ $record->pilihan3 ?: '-' }}
                            </span>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Status Data --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            <h3 class="font-bold text-gray-900 border-b border-gray-100 pb-4 mb-5">
                Status Data
            </h3>

            <div class="space-y-4">

                <div
                    class="flex justify-between items-center
                           rounded-xl bg-gray-50 p-4"
                >

                    <span class="text-sm text-gray-500">
                        Status
                    </span>

                    <span
                        class="px-3 py-1 rounded-full
                               bg-green-100 text-green-700
                               text-xs font-bold"
                    >
                        Selesai
                    </span>

                </div>

                <div
                    class="flex justify-between items-center
                           rounded-xl bg-gray-50 p-4"
                >

                    <span class="text-sm text-gray-500">
                        Dicatat
                    </span>

                    <span class="font-semibold text-gray-900 text-sm">
                        {{ optional($record->created_at)->diffForHumans() }}
                    </span>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =======================================================
    FLASH MESSAGE
======================================================= --}}
<div class="mt-6">

    <x-shared.flash-message />

</div>
</div>