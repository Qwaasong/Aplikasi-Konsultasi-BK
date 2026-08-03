<?php

use App\Livewire\Konselor\Asesmen\Sosiometri\Detail;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app', ['title' => 'Detail Sosiometri - Bimbingan Konseling'])] class extends Detail {};

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
                                    href="{{ route('konselor.asesmen.sosiometri.detail', $result->id) }}"
                                    wire:navigate
                                    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition">

                                    <div class="text-sm font-semibold text-gray-800">

                                        {{ $result->siswa?->nama ?? '-' }}

                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">

                                        NIS {{ $result->siswa?->nis ?? '-' }}

                                        &middot;

                                        {{ $result->judul }}

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

                    Asesmen Sosiometri

                </span>

                <h1 class="text-2xl font-bold text-gray-900 mt-1">

                    Detail Sosiometri

                </h1>

            </div>

        </div>

        <div class="flex items-center gap-3">

            <button
                wire:click="edit"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-teal-50 text-teal-700 border border-teal-100 hover:bg-teal-100 transition">

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

            <button
                wire:click="delete"
                wire:confirm="Yakin ingin menghapus data sosiometri ini?"
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

                        {{ $sosiometri->siswa?->initials ?? 'S' }}

                    </div>

                    <div>

                        <p class="text-lg font-bold text-gray-900">

                            {{ $sosiometri->siswa?->nama ?? '-' }}

                        </p>

                        <p class="text-sm text-gray-500 mt-1">

                            NIS {{ $sosiometri->siswa?->nis ?? '-' }}

                            &middot;

                            Kelas {{ $sosiometri->siswa?->kelas_label ?? '-' }}

                            &middot;

                            {{ $sosiometri->siswa?->jurusan_label ?? '-' }}

                        </p>

                    </div>

                </div>

            </div>


            {{-- ================= HASIL SOSIOMETRI ================= --}}
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
                                  d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0Z"/>

                        </svg>

                    </div>

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Hasil Sosiometri

                    </h3>

                </div>

                @forelse($this->questionGroups as $group)

                    <div class="mb-6 last:mb-0" wire:key="question-group-{{ $group['key'] }}">

                        <div class="flex items-start justify-between gap-3 mb-3">

                            <p class="text-sm font-semibold text-gray-800">

                                {{ $loop->iteration }}. {{ $group['pertanyaan'] }}

                            </p>

                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100 shrink-0">

                                {{ count($group['dipilih']) }} Pilihan

                            </span>

                        </div>

                        @if(count($group['dipilih']) > 0)

                            <div class="space-y-2">

                                @foreach($group['dipilih'] as $nama)

                                    <div class="flex items-center gap-3 rounded-lg border border-gray-100 bg-gray-50/60 px-3 py-2">

                                        <div
                                            class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0">

                                            {{ strtoupper(mb_substr($nama, 0, 1)) }}

                                        </div>

                                        <span class="text-sm text-gray-700">

                                            {{ $nama }}

                                        </span>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <p class="text-sm text-gray-400 italic">

                                Tidak ada jawaban.

                            </p>

                        @endif

                    </div>

                @empty

                    <p class="text-center text-gray-500 text-sm py-8">

                        Belum ada data respon sosiometri.

                    </p>

                @endforelse

            </div>


            {{-- ================= INSTRUKSI ASESMEN ================= --}}
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
                                  d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>

                        </svg>

                    </div>

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Instruksi Asesmen

                    </h3>

                </div>

                <div class="rounded-xl bg-gray-50 border border-gray-100 p-5">

                    <p
                        class="text-sm leading-7 text-gray-700 whitespace-pre-line text-justify">

                        {{ $sosiometri->instruksi ?: 'Belum ada instruksi yang ditambahkan.' }}

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

                            Judul

                        </span>

                        <span class="font-semibold text-gray-800 text-right">

                            {{ $sosiometri->judul ?: '-' }}

                        </span>

                    </div>

                    <div class="flex justify-between items-center text-sm">

                        <span class="text-gray-500">

                            Tanggal

                        </span>

                        <span class="font-semibold text-gray-800">

                            {{ $sosiometri->created_at
                                ? \Carbon\Carbon::parse($sosiometri->created_at)->locale('id')->translatedFormat('d F Y')
                                : '-' }}

                        </span>

                    </div>

                    <div class="flex justify-between items-center text-sm">

                        <span class="text-gray-500">

                            Jumlah Pilihan

                        </span>

                        <span class="font-semibold text-gray-800">

                            {{ $sosiometri->jumlah_pilihan }}

                        </span>

                    </div>

                    <div class="flex justify-between items-center text-sm">

                        <span class="text-gray-500">

                            Jumlah Respon

                        </span>

                        <span class="font-semibold text-gray-800">

                            {{ $sosiometri->respons->count() }}

                        </span>

                    </div>

                    <div class="flex justify-between items-center text-sm">

                        <span class="text-gray-500">

                            Dibuat

                        </span>

                        <span class="font-semibold text-gray-800">

                            {{ $sosiometri->created_at
                                ? $sosiometri->created_at->locale('id')->translatedFormat('d M Y H:i')
                                : '-' }}

                        </span>

                    </div>

                    <div class="flex justify-between items-center text-sm">

                        <span class="text-gray-500">

                            Terakhir Diubah

                        </span>

                        <span class="font-semibold text-gray-800">

                            {{ $sosiometri->updated_at
                                ? $sosiometri->updated_at->locale('id')->translatedFormat('d M Y H:i')
                                : '-' }}

                        </span>

                    </div>

                </div>

            </div>


            {{-- ================= RINGKASAN SOSIOMETRI ================= --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">

                <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-5">

                    <h3 class="font-bold text-gray-900 text-[15px]">

                        Ringkasan Sosiometri

                    </h3>

                    <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-100">

                        {{ count(\App\Models\Sosiometri::PERTANYAAN) }} Pertanyaan

                    </span>

                </div>

                <div class="space-y-4">

                    <div class="rounded-xl border border-gray-100 p-4">

                        <div class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">

                            Jumlah Pertanyaan

                        </div>

                        <div class="mt-1 text-2xl font-bold text-indigo-700">

                            {{ count(\App\Models\Sosiometri::PERTANYAAN) }}

                        </div>

                    </div>

                    <div class="rounded-xl border border-gray-100 p-4">

                        <div class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">

                            Pilihan per Pertanyaan

                        </div>

                        <div class="mt-1 text-2xl font-bold text-emerald-700">

                            {{ $sosiometri->jumlah_pilihan }}

                        </div>

                    </div>

                    <div class="rounded-xl border border-gray-100 p-4">

                        <div class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">

                            Total Respon

                        </div>

                        <div class="mt-1 text-2xl font-bold text-orange-600">

                            {{ $sosiometri->respons->count() }}

                        </div>

                    </div>

                </div>

            </div>


            {{-- ================= STATUS ASESMEN ================= --}}
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

                    <div class="rounded-xl bg-green-50 border border-green-100 p-5 text-center">

                        <div class="text-xs uppercase tracking-wider text-green-600 font-semibold">

                            Status Data

                        </div>

                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold mt-2">

                            Asesmen Selesai

                        </span>

                    </div>

                    <div class="rounded-xl border border-gray-100 p-4">

                        <div class="flex justify-between items-center">

                            <span class="text-sm text-gray-500">

                                Total Respon

                            </span>

                            <span class="font-bold text-gray-900">

                                {{ $sosiometri->respons->count() }}

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================= FLASH MESSAGE ================= --}}
    <div class="mt-6">

        <x-shared.flash-message />

    </div>

</div>
