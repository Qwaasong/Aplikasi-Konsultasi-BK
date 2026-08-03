<div>
    <x-shared.modal name="form-gaya-belajar" maxWidth="lg">

        <div class="flex flex-col h-full max-h-[80vh]">

            {{-- =====================================================
                HEADER
            ====================================================== --}}
            <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">

                <h2 class="text-base font-bold text-gray-900 leading-tight">
                    {{ $editingId ? 'Edit Gaya Belajar' : 'Tambah Gaya Belajar' }}
                </h2>

                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $editingId
                        ? 'Perbarui hasil asesmen gaya belajar siswa'
                        : 'Catat hasil asesmen gaya belajar siswa'
                    }}
                </p>

            </div>


            {{-- =====================================================
                BODY
            ====================================================== --}}
            <div
                class="px-6 py-4 overflow-y-auto modal-scroll grow"
                style="scrollbar-width: thin;"
            >

                {{-- =================================================
                    STEP 1
                ================================================== --}}
                <div class="{{ $step === 1 ? 'block' : 'hidden' }}">

                    {{-- Progress --}}
                    <div class="mb-6">

                        <p class="text-[14px] font-bold text-primary mb-2.5">
                            Langkah 1 Dari 2
                        </p>

                        <div class="flex gap-2.5">
                            <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>
                            <div class="h-2.5 w-1/2 bg-gray-200/80 rounded-full"></div>
                        </div>

                    </div>


                    {{-- =================================================
                        SISWA
                    ================================================== --}}
                    <div class="mb-6">

                        <x-atoms.input-label for="siswa_id" size="sm">
                            Siswa
                        </x-atoms.input-label>

                        @php
                            $selectedStudent = collect($students)
                                ->firstWhere('id', $siswa_id);
                        @endphp

                        @if($selectedStudent)

                            <div class="bg-bg-light border border-teal-100/60 rounded-lg p-4 flex items-center justify-between">

                                <div class="flex items-center gap-3">

                                    <div class="w-[45px] h-[45px] bg-icon-bg text-primary rounded-full flex items-center justify-center font-bold text-[16px]">
                                        {{ $this->getInitials($selectedStudent->nama ?? '') }}
                                    </div>

                                    <div>

                                        <h3 class="text-[14px] font-bold text-gray-900">
                                            {{ $selectedStudent->nama ?? '-' }}
                                        </h3>

                                        <p class="text-[12px] text-gray-400 mt-0.5">
                                            Kelas {{ $selectedStudent->kelas_label ?? '-' }}
                                            {{ $selectedStudent->jurusan_label ?? '' }}
                                            - NIS {{ $selectedStudent->nis ?? '-' }}
                                        </p>

                                    </div>

                                </div>

                                <button
                                    type="button"
                                    wire:click="openStudentModal"
                                    class="text-[13px] font-bold text-gray-500 hover:text-gray-800 transition-colors"
                                >
                                    Ganti
                                </button>

                            </div>

                        @else

                            <div class="bg-bg-light border border-teal-100/60 rounded-lg p-5 flex flex-col items-center justify-center text-center">

                                <div class="w-[56px] h-[56px] bg-icon-bg rounded-full flex items-center justify-center mb-3 text-primary">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        class="w-7 h-7"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                                        />
                                    </svg>

                                </div>

                                <h3 class="text-[15px] font-bold text-gray-700 mb-1">
                                    Tidak Ada Siswa Yang Dipilih
                                </h3>

                                <p class="text-[13px] text-gray-400 mb-4">
                                    Pilih siswa untuk melanjutkan asesmen gaya belajar.
                                </p>

                                <button
                                    type="button"
                                    wire:click="openStudentModal"
                                    class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-md text-[13px] font-semibold transition-colors"
                                >
                                    Pilih Siswa
                                </button>

                            </div>

                        @endif

                        @error('siswa_id')
                            <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- =================================================
                        TANGGAL
                    ================================================== --}}
                    <div class="mb-6">

                        <x-atoms.input-label for="tanggal" size="sm">
                            Tanggal Asesmen
                        </x-atoms.input-label>

                        <x-atoms.text-input
                            id="tanggal"
                            type="date"
                            wire:model="tanggal"
                            size="md"
                        />

                        @error('tanggal')
                            <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- =================================================
                        NILAI GAYA BELAJAR
                    ================================================== --}}

                    <div class="space-y-5">

                        {{-- VISUAL --}}
                        <div class="bg-bg-light border border-gray-200 rounded-xl p-5">

                            <x-atoms.input-label for="visual" size="sm">
                                Visual
                            </x-atoms.input-label>

                            <x-atoms.text-input
                                id="visual"
                                type="number"
                                min="0"
                                max="100"
                                wire:model.live="visual"
                                placeholder="Masukkan skor Visual"
                            />

                            <p class="text-xs text-gray-400 mt-2">
                                Semakin tinggi nilainya, semakin dominan kecenderungan belajar visual.
                            </p>

                            @error('visual')
                                <span class="text-red-500 text-[13px] font-medium mt-2 block">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>


                        {{-- AUDITORI --}}
                        <div class="bg-bg-light border border-gray-200 rounded-xl p-5">

                            <x-atoms.input-label for="auditori" size="sm">
                                Auditori
                            </x-atoms.input-label>

                            <x-atoms.text-input
                                id="auditori"
                                type="number"
                                min="0"
                                max="100"
                                wire:model.live="auditori"
                                placeholder="Masukkan skor Auditori"
                            />

                            <p class="text-xs text-gray-400 mt-2">
                                Nilai ini menunjukkan kecenderungan memahami materi melalui pendengaran.
                            </p>

                            @error('auditori')
                                <span class="text-red-500 text-[13px] font-medium mt-2 block">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>


                        {{-- KINESTETIK --}}
                        <div class="bg-bg-light border border-gray-200 rounded-xl p-5">

                            <x-atoms.input-label for="kinestetik" size="sm">
                                Kinestetik
                            </x-atoms.input-label>

                            <x-atoms.text-input
                                id="kinestetik"
                                type="number"
                                min="0"
                                max="100"
                                wire:model.live="kinestetik"
                                placeholder="Masukkan skor Kinestetik"
                            />

                            <p class="text-xs text-gray-400 mt-2">
                                Nilai ini menunjukkan kecenderungan belajar melalui praktik atau aktivitas fisik.
                            </p>

                            @error('kinestetik')
                                <span class="text-red-500 text-[13px] font-medium mt-2 block">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                    </div>
                                        {{-- =================================================
                            HASIL GAYA BELAJAR
                        ================================================== --}}
                        <div class="bg-gradient-to-r from-teal-50 to-cyan-50 border border-teal-100 rounded-xl p-6">

                            <div class="flex items-center gap-3 mb-3">

                                <div class="w-12 h-12 rounded-full bg-icon-bg flex items-center justify-center">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        class="w-6 h-6 text-primary">

                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75M21
                                            12a9 9 0 11-18 0
                                            9 9 0 0118 0z"/>

                                    </svg>

                                </div>

                                <div>

                                    <h3 class="text-[15px] font-bold text-gray-900">
                                        Hasil Analisis
                                    </h3>

                                    <p class="text-[12px] text-gray-500">
                                        Otomatis berdasarkan nilai tertinggi
                                    </p>

                                </div>

                            </div>

                            <div class="text-center py-3">

                                <p class="text-xs text-gray-500 uppercase tracking-wider">
                                    Gaya Belajar Dominan
                                </p>

                                <h2 class="mt-2 text-3xl font-extrabold text-primary">
                                    {{ $hasil ?: '-' }}
                                </h2>

                            </div>

                        </div>


                        {{-- =================================================
                            CATATAN
                        ================================================== --}}
                        <div>

                            <x-atoms.input-label
                                for="catatan"
                                size="sm">

                                Catatan

                            </x-atoms.input-label>

                            <textarea
                                id="catatan"
                                wire:model="catatan"
                                rows="5"
                                class="w-full border border-gray-200 rounded-md
                                    p-4 text-[14px]
                                    text-gray-900
                                    placeholder:text-gray-400
                                    focus:outline-none
                                    focus:border-primary
                                    focus:ring-1
                                    focus:ring-primary
                                    resize-none shadow-sm
                                    leading-relaxed"

                                placeholder="Tambahkan catatan hasil asesmen jika diperlukan."
                            ></textarea>

                            @error('catatan')

                                <span class="text-red-500 text-[13px] font-medium mt-2 block">
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>


                        {{-- =================================================
                            FAKTOR PENGHAMBAT
                        ================================================== --}}
                        <div>

                            <x-atoms.input-label
                                for="faktor_penghambat"
                                size="sm">

                                Faktor Penghambat Belajar

                            </x-atoms.input-label>

                            <textarea
                                id="faktor_penghambat"
                                wire:model="faktor_penghambat"
                                rows="4"
                                class="w-full border border-gray-200 rounded-md
                                    p-4 text-[14px]
                                    text-gray-900
                                    placeholder:text-gray-400
                                    focus:outline-none
                                    focus:border-primary
                                    focus:ring-1
                                    focus:ring-primary
                                    resize-none shadow-sm
                                    leading-relaxed"

                                placeholder="Faktor apa sajakah yang menghambat belajar Anda?"
                            ></textarea>

                            @error('faktor_penghambat')

                                <span class="text-red-500 text-[13px] font-medium mt-2 block">
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>


                        {{-- =================================================
                            FAKTOR PENDUKUNG
                        ================================================== --}}
                        <div>

                            <x-atoms.input-label
                                for="faktor_pendukung"
                                size="sm">

                                Faktor Pendukung Belajar

                            </x-atoms.input-label>

                            <textarea
                                id="faktor_pendukung"
                                wire:model="faktor_pendukung"
                                rows="4"
                                class="w-full border border-gray-200 rounded-md
                                    p-4 text-[14px]
                                    text-gray-900
                                    placeholder:text-gray-400
                                    focus:outline-none
                                    focus:border-primary
                                    focus:ring-1
                                    focus:ring-primary
                                    resize-none shadow-sm
                                    leading-relaxed"

                                placeholder="Faktor apa sajakah yang mendukung belajar Anda?"
                            ></textarea>

                            @error('faktor_pendukung')

                                <span class="text-red-500 text-[13px] font-medium mt-2 block">
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>

                    </div>

                </div>


                {{-- =================================================
                    STEP 2
                ================================================== --}}

                <div class="{{ $step === 2 ? 'block' : 'hidden' }}">

                    {{-- Progress --}}
                    <div class="mb-6">

                        <p class="text-[14px] font-bold text-primary mb-2.5">
                            Langkah 2 Dari 2
                        </p>

                        <div class="flex gap-2.5">

                            <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>

                            <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>

                        </div>

                    </div>


                    {{-- =================================================
                        RINGKASAN
                    ================================================== --}}

                    <div class="rounded-xl border border-gray-200 overflow-hidden">

                        <div class="bg-bg-light px-5 py-4 border-b">

                            <h3 class="text-[15px] font-bold text-gray-900">
                                Ringkasan Hasil Asesmen
                            </h3>

                            <p class="text-xs text-gray-500 mt-1">
                                Pastikan seluruh data sudah benar sebelum disimpan.
                            </p>

                        </div>


                        <div class="divide-y divide-gray-100">

                            {{-- SISWA --}}
                            <div class="flex justify-between items-center px-5 py-4">

                                <span class="text-sm text-gray-500">
                                    Nama Siswa
                                </span>

                                <span class="font-semibold text-gray-900">

                                    {{
                                        optional(
                                            collect($students)->firstWhere('id',$siswa_id)
                                        )->nama ?? '-'
                                    }}

                                </span>

                            </div>


                            {{-- TANGGAL --}}
                            <div class="flex justify-between items-center px-5 py-4">

                                <span class="text-sm text-gray-500">
                                    Tanggal
                                </span>

                                <span class="font-semibold text-gray-900">

                                    {{ $tanggal ?: '-' }}

                                </span>

                            </div>


                            {{-- VISUAL --}}
                            <div class="flex justify-between items-center px-5 py-4">

                                <span class="text-sm text-gray-500">
                                    Visual
                                </span>

                                <span class="font-bold text-primary">

                                    {{ $visual }}

                                </span>

                            </div>


                            {{-- AUDITORI --}}
                            <div class="flex justify-between items-center px-5 py-4">

                                <span class="text-sm text-gray-500">
                                    Auditori
                                </span>

                                <span class="font-bold text-primary">

                                    {{ $auditori }}

                                </span>

                            </div>


                            {{-- KINESTETIK --}}
                            <div class="flex justify-between items-center px-5 py-4">

                                <span class="text-sm text-gray-500">
                                    Kinestetik
                                </span>

                                <span class="font-bold text-primary">

                                    {{ $kinestetik }}

                                </span>

                            </div>


                            {{-- HASIL --}}
                            <div class="px-5 py-5 bg-teal-50">

                                <div class="text-center">

                                    <p class="text-xs uppercase tracking-widest text-gray-500">
                                        Hasil Gaya Belajar
                                    </p>

                                    <h2 class="mt-2 text-2xl font-extrabold text-primary">

                                        {{ $hasil ?: '-' }}

                                    </h2>

                                </div>

                            </div>


                            {{-- CATATAN --}}
                            <div class="px-5 py-4">

                                <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">
                                    Catatan
                                </p>

                                <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line">

                                    {{ $catatan ?: '-' }}

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

                        {{-- =====================================================
                FOOTER
            ====================================================== --}}
            <div class="bg-bg-light px-7 py-5 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl">

                {{-- STEP 1 --}}
                <div class="{{ $step === 1 ? 'flex' : 'hidden' }} gap-3">

                    <x-atoms.button
                        variant="secondary"
                        size="md"
                        x-on:click="show = false"
                    >
                        Batal
                    </x-atoms.button>

                    <x-atoms.button wire:click="nextStep">
                        Lanjut ke Ringkasan
                    </x-atoms.button>

                </div>


                {{-- STEP 2 --}}
                <div class="{{ $step === 2 ? 'flex' : 'hidden' }} gap-3">

                    <x-atoms.button
                        variant="secondary"
                        size="md"
                        wire:click="previousStep"
                    >
                        Kembali
                    </x-atoms.button>

                    <x-atoms.button wire:click="save">

                        {{ $editingId
                            ? 'Perbarui Gaya Belajar'
                            : 'Simpan Gaya Belajar'
                        }}

                    </x-atoms.button>

                </div>

            </div>

        </div>


        {{-- ==========================================================
            MODAL PILIH SISWA
        =========================================================== --}}

        <div
            x-data="{ showStudentMenu: @entangle('showStudentModal') }"
            x-show="showStudentMenu"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display:none;"
        >

            <div
                class="bg-white w-full max-w-[500px] rounded-xl shadow-2xl flex flex-col max-h-[80vh] overflow-hidden"
                @click.away="showStudentMenu = false"
            >

                {{-- HEADER --}}
                <div class="bg-bg-light px-6 py-4 border-b border-gray-100">

                    <h2 class="text-[20px] font-bold text-gray-900">
                        Pilih Siswa
                    </h2>

                    <p class="text-[13px] text-gray-500 mt-1">
                        Cari berdasarkan nama atau NIS
                    </p>

                </div>


                {{-- BODY --}}
                <div
                    class="px-6 py-5 overflow-y-auto grow modal-scroll"
                    style="scrollbar-width: thin;"
                >

                    {{-- SEARCH --}}
                    <div class="mb-5">

                        <input
                            type="text"
                            wire:model.live="searchSiswa"
                            placeholder="Cari Nama atau NIS"
                            class="w-full border border-gray-200 rounded-md px-4 py-2 text-[13px] text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm"
                        >

                    </div>

                    @php

                        $filteredStudents = collect($students)
                            ->filter(function ($student) {

                                $keyword = strtolower(trim($this->searchSiswa));

                                if ($keyword === '') {
                                    return true;
                                }

                                return str_contains(
                                    strtolower($student->nama ?? ''),
                                    $keyword
                                )
                                ||
                                str_contains(
                                    strtolower($student->nis ?? ''),
                                    $keyword
                                );

                            });

                    @endphp

                    <div class="space-y-3">

                        @forelse($filteredStudents as $student)

                            <div
                                wire:click="selectStudent({{ $student->id }})"
                                class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-primary hover:bg-bg-light transition-all
                                {{ $student->id == $siswa_id
                                    ? 'border-primary bg-bg-light'
                                    : ''
                                }}"
                            >

                                <div class="flex items-center gap-3">

                                    <div class="w-10 h-10 rounded-full bg-icon-bg text-primary flex items-center justify-center font-bold">

                                        {{ $this->getInitials($student->nama) }}

                                    </div>

                                    <div>

                                        <h4 class="font-bold text-[14px] text-gray-900">

                                            {{ $student->nama }}

                                        </h4>

                                        <p class="text-[12px] text-gray-500 mt-1">

                                            NIS :
                                            {{ $student->nis }}

                                            <span class="ml-2">

                                                Kelas
                                                {{ $student->kelas_label }}

                                            </span>

                                            @if($student->jurusan_label)

                                                <span class="ml-1">

                                                    {{ $student->jurusan_label }}

                                                </span>

                                            @endif

                                        </p>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="text-center py-8 text-gray-500 text-sm">

                                Tidak ada siswa ditemukan.

                            </div>

                        @endforelse

                    </div>

                </div>


                {{-- FOOTER --}}
                <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end">

                    <button
                        type="button"
                        wire:click="closeStudentModal"
                        class="px-5 py-2 rounded-md border border-gray-200 bg-white text-gray-600 text-[13px] font-bold hover:bg-gray-50 transition"
                    >

                        Batal

                    </button>

                </div>

            </div>

        </div>

    </x-shared.modal>

</div>