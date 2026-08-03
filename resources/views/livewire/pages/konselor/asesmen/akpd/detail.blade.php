<?php

use App\Livewire\Konselor\Asesmen\Akpd\Detail;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Detail {};

?>

<div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 min-h-screen p-6 lg:p-10">

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

                    Asesmen AKPD

                </span>

                <h1 class="text-2xl font-bold text-gray-900 mt-1">

                    Detail AKPD

                </h1>

            </div>

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


            {{-- ================= HASIL AKPD ================= --}}
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

                        Hasil AKPD

                    </h3>

                </div>

                @foreach($this->aspectGroups as $group)

                    <div class="mb-6 last:mb-0">

                        <div class="flex items-center justify-between mb-3">

                            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide">

                                {{ $group['aspect'] }}

                            </h4>

                            <span class="px-2.5 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold border border-teal-100">

                                Ya {{ $group['ya_count'] }}/{{ $group['total'] }}

                            </span>

                        </div>

                        <div class="space-y-2">

                            @foreach($group['answers'] as $answer)

                                <div class="flex items-start justify-between gap-3 rounded-lg border border-gray-100 bg-gray-50/60 px-3 py-2">

                                    <span class="text-sm text-gray-700 leading-6">

                                        <span class="font-bold text-gray-900 mr-1">

                                            {{ $answer['no'] }}.

                                        </span>

                                        {{ $answer['pertanyaan'] }}

                                    </span>

                                    @if($answer['jawaban'] === 'Ya')

                                        <span class="shrink-0 mt-1 px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-700 text-xs font-bold border border-teal-100">

                                            Ya

                                        </span>

                                    @else

                                        <span class="shrink-0 mt-1 px-2.5 py-0.5 rounded-full bg-gray-50 text-gray-500 text-xs font-bold border border-gray-200">

                                            {{ $answer['jawaban'] }}

                                        </span>

                                    @endif

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endforeach

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
                                ? $record->tanggal->locale('id')->translatedFormat('d F Y')
                                : '-' }}

                        </span>

                    </div>

                    <div class="flex justify-between items-center text-sm">

                        <span class="text-gray-500">

                            Tahun Pelajaran

                        </span>

                        <span class="font-semibold text-gray-800">

                            {{ $record->tahun_pelajaran ?: '-' }}

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

        </div>

    </div>


    {{-- ================= FLASH MESSAGE ================= --}}
    <div class="mt-6">

        <x-shared.flash-message />

    </div>

</div>
