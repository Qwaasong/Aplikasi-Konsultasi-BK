<?php

use App\Livewire\Konselor\Asesmen\GayaBelajar\Detail;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app', ['title' => 'Detail Gaya Belajar - Bimbingan Konseling'])] class extends Detail {};

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
                                    href="{{ route('konselor.asesmen.gaya-belajar.detail',$result->id) }}"
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

            {{-- Back --}}
            <button
                wire:click="goBack"
                class="w-10 h-10 rounded-full flex items-center justify-center border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition">

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

                <span
                    class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">

                    Asesmen Gaya Belajar

                </span>

                <h1 class="text-2xl font-bold text-gray-900 mt-1">

                    Detail Gaya Belajar

                </h1>

            </div>

        </div>


        {{-- Action --}}
        <div class="flex items-center gap-3">

            {{-- Edit --}}
            <button
                wire:click="edit"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-teal-50 text-teal-700 border border-teal-100 hover:bg-teal-100 transition">

                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="2"
                     stroke="currentColor"
                     class="w-4 h-4">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.112l-3.154 1.054a.75.75 0 01-.94-.94l1.054-3.154a4.5 4.5 0 011.112-1.89l13.416-13.416z"/>

                </svg>

                Edit

            </button>


            {{-- Delete --}}
            <button
                wire:click="delete"
                wire:confirm="Yakin ingin menghapus data Gaya Belajar ini?"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 transition">

                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="2"
                     stroke="currentColor"
                     class="w-4 h-4">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166"/>

                </svg>

                Hapus

            </button>

        </div>

    </div>


    {{-- ================= CONTENT ================= --}}

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- =========================================================
            LEFT CONTENT
        ========================================================== --}}
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
                                  d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632"/>

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


            {{-- ================= HASIL GAYA BELAJAR ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">

                    <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="2"
                             stroke="currentColor"
                             class="w-5 h-5">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0"/>

                        </svg>

                    </div>

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Hasil Gaya Belajar

                    </h3>

                </div>

                <div
                    class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50 via-white to-indigo-50 p-8 text-center">

                    <div class="text-xs uppercase tracking-widest text-gray-500">

                        Dominan

                    </div>

                    <div class="mt-3 text-3xl font-extrabold text-indigo-700">

                        {{ strtoupper($record->hasil ?? '-') }}

                    </div>

                    <div class="mt-3 text-sm text-gray-500">

                        Berdasarkan hasil asesmen yang telah dilakukan.

                    </div>

                </div>

            </div>


            {{-- ================= SKOR GAYA BELAJAR ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">

                    <div class="p-2 rounded-xl bg-sky-50 text-sky-600">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="2"
                             stroke="currentColor"
                             class="w-5 h-5">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M3 13.125C3 11.399 4.4 10 6.125 10h11.75C19.601 10 21 11.4 21 13.125v4.75C21 19.601 19.6 21 17.875 21H6.125A3.125 3.125 0 013 17.875v-4.75Z"/>

                        </svg>

                    </div>

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Skor Gaya Belajar

                    </h3>

                </div>

                {{-- Visual --}}
                <div class="mb-6">

                    <div class="flex justify-between mb-2">

                        <span class="font-semibold text-gray-700">

                            Visual

                        </span>

                        <span class="font-bold text-indigo-700">

                            {{ $record->visual }}

                        </span>

                    </div>

                    <div class="w-full h-3 rounded-full bg-gray-200 overflow-hidden">

                        <div
                            class="h-full rounded-full bg-indigo-500"
                            style="width: {{ min($record->visual,100) }}%;">
                        </div>

                    </div>

                </div>

                {{-- Auditori --}}
                <div class="mb-6">

                    <div class="flex justify-between mb-2">

                        <span class="font-semibold text-gray-700">

                            Auditori

                        </span>

                        <span class="font-bold text-emerald-700">

                            {{ $record->auditori }}

                        </span>

                    </div>

                    <div class="w-full h-3 rounded-full bg-gray-200 overflow-hidden">

                        <div
                            class="h-full rounded-full bg-emerald-500"
                            style="width: {{ min($record->auditori,100) }}%;">
                        </div>

                    </div>

                </div>

                {{-- Kinestetik --}}
                <div>

                    <div class="flex justify-between mb-2">

                        <span class="font-semibold text-gray-700">

                            Kinestetik

                        </span>

                        <span class="font-bold text-orange-600">

                            {{ $record->kinestetik }}

                        </span>

                    </div>

                    <div class="w-full h-3 rounded-full bg-gray-200 overflow-hidden">

                        <div
                            class="h-full rounded-full bg-orange-500"
                            style="width: {{ min($record->kinestetik,100) }}%;">
                        </div>

                    </div>

                </div>

            </div>


            {{-- ================= PERTANYAAN GAYA BELAJAR ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">

                    <div class="p-2 rounded-xl bg-indigo-50 text-indigo-600">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="2"
                             stroke="currentColor"
                             class="w-5 h-5">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>

                        </svg>

                    </div>

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Pertanyaan Gaya Belajar

                    </h3>

                </div>

                @foreach($this->questionGroups as $group)

                    <div class="mb-6 last:mb-0">

                        <div class="flex items-center justify-between mb-3">

                            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide">

                                {{ $group['name'] }}

                            </h4>

                            <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-100">

                                Skor {{ $group['score'] }}

                            </span>

                        </div>

                        <div class="space-y-2">

                            @foreach($group['questions'] as $i => $pertanyaan)

                                <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50/60 px-3 py-2">

                                    <span class="shrink-0 w-5 h-5 mt-0.5 rounded border border-gray-300 bg-white"></span>

                                    <span class="text-sm text-gray-700 leading-6">

                                        <span class="font-bold text-gray-900 mr-1">

                                            {{ $i + 1 }}.

                                        </span>

                                        {{ $pertanyaan }}

                                    </span>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endforeach

            </div>


            {{-- ================= CATATAN ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">

                    <div class="p-2 rounded-xl bg-amber-50 text-amber-600">

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

                <div class="rounded-xl bg-gray-50 border border-gray-100 p-5">

                    <p
                        class="text-sm leading-7 text-gray-700 whitespace-pre-line text-justify">

                        {{ $record->catatan ?: 'Belum ada catatan yang ditambahkan.' }}

                    </p>

                </div>

            </div>


            {{-- ================= FAKTOR PENGHAMBAT ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">

                    <div class="p-2 rounded-xl bg-red-50 text-red-600">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="2"
                             stroke="currentColor"
                             class="w-5 h-5">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>

                        </svg>

                    </div>

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Faktor Penghambat Belajar

                    </h3>

                </div>

                <div class="rounded-xl bg-gray-50 border border-gray-100 p-5">

                    <p
                        class="text-sm leading-7 text-gray-700 whitespace-pre-line text-justify">

                        {{ $record->faktor_penghambat ?: 'Belum ada data.' }}

                    </p>

                </div>

            </div>


            {{-- ================= FAKTOR PENDUKUNG ================= --}}
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
                                  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>

                        </svg>

                    </div>

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Faktor Pendukung Belajar

                    </h3>

                </div>

                <div class="rounded-xl bg-gray-50 border border-gray-100 p-5">

                    <p
                        class="text-sm leading-7 text-gray-700 whitespace-pre-line text-justify">

                        {{ $record->faktor_pendukung ?: 'Belum ada data.' }}

                    </p>

                </div>

            </div>
                </div>

        {{-- ================= SIDEBAR ================= --}}
        <div class="space-y-6">

            {{-- ================= INFORMASI ASESMEN ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <h3 class="font-bold text-gray-900 text-[15px] border-b border-gray-100 pb-4 mb-4">

                    Informasi Asesmen

                </h3>

                <div class="space-y-4">

                    <div class="flex justify-between items-center text-sm">

                        <span class="text-gray-500">

                            Tanggal Asesmen

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


            {{-- ================= RINGKASAN NILAI ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-5">

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Ringkasan Nilai

                    </h3>

                    <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-100">

                        3 Aspek

                    </span>

                </div>

                <div class="space-y-4">

                    {{-- Visual --}}
                    <div class="rounded-xl border border-gray-100 p-4">

                        <div class="flex justify-between items-center mb-2">

                            <span class="text-sm font-semibold text-gray-700">

                                Visual

                            </span>

                            <span class="font-bold text-indigo-700">

                                {{ $record->visual }}

                            </span>

                        </div>

                        <div class="h-2.5 bg-gray-200 rounded-full overflow-hidden">

                            <div
                                class="h-full rounded-full bg-indigo-500"
                                style="width: {{ min($record->visual,100) }}%;">
                            </div>

                        </div>

                    </div>

                    {{-- Auditori --}}
                    <div class="rounded-xl border border-gray-100 p-4">

                        <div class="flex justify-between items-center mb-2">

                            <span class="text-sm font-semibold text-gray-700">

                                Auditori

                            </span>

                            <span class="font-bold text-emerald-700">

                                {{ $record->auditori }}

                            </span>

                        </div>

                        <div class="h-2.5 bg-gray-200 rounded-full overflow-hidden">

                            <div
                                class="h-full rounded-full bg-emerald-500"
                                style="width: {{ min($record->auditori,100) }}%;">
                            </div>

                        </div>

                    </div>

                    {{-- Kinestetik --}}
                    <div class="rounded-xl border border-gray-100 p-4">

                        <div class="flex justify-between items-center mb-2">

                            <span class="text-sm font-semibold text-gray-700">

                                Kinestetik

                            </span>

                            <span class="font-bold text-orange-600">

                                {{ $record->kinestetik }}

                            </span>

                        </div>

                        <div class="h-2.5 bg-gray-200 rounded-full overflow-hidden">

                            <div
                                class="h-full rounded-full bg-orange-500"
                                style="width: {{ min($record->kinestetik,100) }}%;">
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ================= STATUS GAYA BELAJAR ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-5">

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Status Asesmen

                    </h3>

                    <span class="text-xs text-gray-400">

                        Ringkasan

                    </span>

                </div>

                <div class="space-y-5">

                    <div class="rounded-xl bg-indigo-50 border border-indigo-100 p-5 text-center">

                        <div class="text-xs uppercase tracking-wider text-indigo-500 font-semibold">

                            Gaya Belajar Dominan

                        </div>

                        <div class="mt-2 text-3xl font-extrabold text-indigo-700">

                            {{ strtoupper($record->hasil ?? '-') }}

                        </div>

                    </div>

                    <div class="rounded-xl border border-gray-100 p-4">

                        <div class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-3">

                            Status Data

                        </div>

                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">

                            Asesmen Selesai

                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================= FLASH MESSAGE ================= --}}
    <div class="mt-6">

        <x-shared.flash-message />

    </div>


    {{-- ================= MODAL ================= --}}
</div>