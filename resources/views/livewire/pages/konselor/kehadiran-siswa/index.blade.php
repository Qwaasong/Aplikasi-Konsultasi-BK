<?php

use App\Livewire\Konselor\KehadiranSiswa\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app', ['title' => 'Kehadiran Siswa - Bimbingan Konseling'])] class extends Index {}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full">

    {{-- Header --}}
    <header class="h-20 border-b border-gray-200 px-8 flex items-center justify-between shrink-0">

        @if($selectedKelas)
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kehadiran: {{ $selectedKelas }}</h2>
            </div>
        @else
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kehadiran Siswa</h2>
        @endif

        <div class="flex items-center gap-2">
            <button wire:click="downloadTemplate" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                <x-atoms.icon variant="template" size="md" />
                Template
            </button>
            <button wire:click="openImport" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                <x-atoms.icon variant="upload" size="md" />
                Import
            </button>
            <button wire:click="openExport" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                <x-atoms.icon variant="download" size="md" />
                Export
            </button>
        </div>

    </header>


    {{-- ========================================================= --}}
    {{-- DAFTAR KELAS --}}
    {{-- ========================================================= --}}

    @if(!$selectedKelas)

        <div class="px-6 sm:px-8 py-6">

            {{-- Selector Tahun Ajaran --}}
            <div class="mb-6 bg-teal-50 border border-teal-100 rounded-xl px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex items-center gap-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm font-semibold text-gray-700">Tahun Ajaran</span>
                </div>
                <div class="flex-1">
                    <select
                        wire:model.live="selectedTahunAjaranId"
                        class="w-full sm:w-auto text-sm font-medium border-teal-200 rounded-lg shadow-sm focus:border-brand-teal focus:ring-brand-teal py-2 pl-3 pr-8 bg-white text-gray-800"
                    >
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($tahunOptions as $tahun)
                            <option value="{{ $tahun['id'] }}"
                                {{ isset($tahun['status_aktif']) && $tahun['status_aktif'] ? '' : '' }}>
                                {{ $tahun['tahun'] }} — Semester {{ $tahun['semester'] }}
                                @if(isset($tahun['status_aktif']) && $tahun['status_aktif'])
                                    (Aktif)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($selectedTahunAjaranId)
                    <div class="flex items-center gap-1.5 text-xs text-teal-700 font-medium shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Tahun ajaran terpilih
                    </div>
                @endif
            </div>

            {{-- Judul --}}
            <div class="mb-5">
                <h2 class="text-base font-semibold text-gray-800">
                    Pilih Kelas
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Pilih kelas untuk mengelola data kehadiran siswa secara massal per hari.
                </p>
            </div>

            {{-- Card Kelas --}}
            @if(!$selectedTahunAjaranId)

                <div class="border border-dashed border-teal-200 rounded-xl py-12 text-center bg-teal-50/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto mb-3 text-teal-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm font-medium text-gray-500">Silakan pilih tahun ajaran terlebih dahulu</p>
                    <p class="text-xs text-gray-400 mt-1">Daftar kelas akan muncul setelah tahun ajaran dipilih</p>
                </div>

            @elseif(count($kelasOptions) > 0)

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                    @foreach($kelasOptions as $kelas)

                        <button
                            type="button"
                            wire:click="pilihKelas('{{ addslashes($kelas) }}')"
                            class="group text-left bg-white border border-gray-200 rounded-xl p-6
                                   shadow-sm transition-all duration-200
                                   hover:border-brand-teal hover:shadow-md
                                   hover:-translate-y-0.5">

                            <div class="flex items-center justify-between">

                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                                        Kelas
                                    </p>
                                    <h3 class="mt-2 text-lg font-semibold text-gray-800
                                               group-hover:text-brand-teal">
                                        {{ $kelas }}
                                    </h3>
                                </div>

                                <div class="w-11 h-11 rounded-lg bg-teal-50
                                            flex items-center justify-center
                                            text-brand-teal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332-.477 4.5-1.253m0-10.494C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332-.477-4.5-1.253" />
                                    </svg>
                                </div>

                            </div>

                            <div class="mt-5 flex items-center text-xs text-gray-400">
                                <span>Kelola Kehadiran</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>

                        </button>

                    @endforeach

                </div>

            @else

                <div class="border border-dashed border-gray-300 rounded-xl py-12 text-center">
                    <p class="text-sm text-gray-500">
                        Belum ada data kelas.
                    </p>
                </div>

            @endif

        </div>

    {{-- ========================================================= --}}
    {{-- DATA KEHADIRAN BERDASARKAN KELAS --}}
    {{-- ========================================================= --}}

    @else

        {{-- Header kelas --}}
        <div class="px-6 sm:px-8 py-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <button
                    type="button"
                    wire:click="kembaliKeKelas"
                    class="inline-flex items-center text-xs text-gray-500 hover:text-brand-teal mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Daftar Kelas
                </button>
                <h2 class="text-lg font-semibold text-gray-800">
                    Form Kehadiran {{ $selectedKelas }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Pilih status kehadiran siswa. Perubahan akan tersimpan secara otomatis.
                </p>
            </div>
            {{-- Tahun Ajaran Badge (read-only context info) --}}
            @php
                $activeTahun = collect($tahunOptions)->firstWhere('id', $selectedTahunAjaranId);
            @endphp
            @if($activeTahun)
                <div class="flex items-center gap-2 bg-teal-50 border border-teal-100 rounded-lg px-3 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-brand-teal shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400 leading-none">Tahun Ajaran</p>
                        <p class="text-xs font-semibold text-gray-700 leading-snug mt-0.5">{{ $activeTahun['tahun'] }} — Smt. {{ $activeTahun['semester'] }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Filter Toolbar --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                {{-- Tanggal --}}
                <div class="flex items-center gap-2">
                    <label class="text-sm font-semibold text-gray-700">Tanggal:</label>
                    <input type="date" wire:model.live="selectedTanggal" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-brand-teal focus:ring-brand-teal py-1.5">
                </div>

                {{-- Filter Status --}}
                <div class="flex items-center gap-2">
                    <label class="text-sm font-semibold text-gray-700">Status:</label>
                    <div class="flex items-center gap-1.5">
                        <button wire:click="$set('filterStatus', '')"
                            class="px-3 py-1 rounded-full text-xs font-medium transition-all
                                {{ $filterStatus === '' ? 'bg-gray-700 text-white shadow' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-100' }}">
                            Semua
                        </button>
                        <button wire:click="$set('filterStatus', 'Hadir')"
                            class="px-3 py-1 rounded-full text-xs font-medium transition-all
                                {{ $filterStatus === 'Hadir' ? 'bg-green-600 text-white shadow' : 'bg-white border border-gray-300 text-gray-600 hover:bg-green-50 hover:border-green-400' }}">
                            Hadir
                        </button>
                        <button wire:click="$set('filterStatus', 'Izin')"
                            class="px-3 py-1 rounded-full text-xs font-medium transition-all
                                {{ $filterStatus === 'Izin' ? 'bg-yellow-500 text-white shadow' : 'bg-white border border-gray-300 text-gray-600 hover:bg-yellow-50 hover:border-yellow-400' }}">
                            Izin
                        </button>
                        <button wire:click="$set('filterStatus', 'Sakit')"
                            class="px-3 py-1 rounded-full text-xs font-medium transition-all
                                {{ $filterStatus === 'Sakit' ? 'bg-blue-600 text-white shadow' : 'bg-white border border-gray-300 text-gray-600 hover:bg-blue-50 hover:border-blue-400' }}">
                            Sakit
                        </button>
                        <button wire:click="$set('filterStatus', 'Alpha')"
                            class="px-3 py-1 rounded-full text-xs font-medium transition-all
                                {{ $filterStatus === 'Alpha' ? 'bg-red-600 text-white shadow' : 'bg-white border border-gray-300 text-gray-600 hover:bg-red-50 hover:border-red-400' }}">
                            Tanpa Keterangan
                        </button>
                    </div>
                </div>
            </div>

            {{-- Search --}}
            <div class="w-full sm:w-64">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-atoms.icon variant="search" class="text-gray-400" size="sm" />
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Cari Siswa..." class="block w-full pl-10 pr-3 py-1.5 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-brand-teal focus:border-brand-teal sm:text-sm transition duration-150 ease-in-out">
                </div>
            </div>
        </div>

        {{-- Flash Message --}}
        <div class="px-4 py-2">
            <x-shared.flash-message />
        </div>

        {{-- Tabel Kehadiran --}}
        <x-organisms.data-table
            :headers="[
                'NIS',
                'Nama Siswa',
                'Hadir',
                'Izin',
                'Sakit',
                'Tanpa Keterangan',
            ]"
            empty="Belum ada data siswa untuk kelas ini."
        >
            @forelse($records as $record)
                <tr
                    wire:key="kehadiran-{{ $record['id'] }}-{{ $selectedTanggal }}"
                    class="group border-b border-gray-100
                           transition-all duration-200 h-12 relative
                           hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)]
                           hover:z-10 hover:rounded-md bg-white">

                    {{-- NIS --}}
                    <td class="px-4 py-2 text-sm text-gray-600">
                        {{ $record['nis'] }}
                    </td>

                    {{-- Nama --}}
                    <td class="px-4 py-2 font-semibold text-gray-800">
                        {{ $record['nama'] }}
                    </td>

                    {{-- Hadir --}}
                    <td class="px-4 py-2 text-center align-middle">
                        <label class="cursor-pointer inline-flex items-center justify-center p-2 rounded-full hover:bg-green-50 transition">
                            <input type="radio" 
                                name="att_{{ $record['id'] }}_{{ $selectedTanggal }}" 
                                value="Hadir" 
                                wire:click="saveAttendance({{ $record['id'] }}, 'Hadir')" 
                                {{ (isset($attendance[$record['id']]) && $attendance[$record['id']] == 'Hadir') ? 'checked' : '' }} 
                                class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300 cursor-pointer">
                        </label>
                    </td>

                    {{-- Izin --}}
                    <td class="px-4 py-2 text-center align-middle">
                        <label class="cursor-pointer inline-flex items-center justify-center p-2 rounded-full hover:bg-yellow-50 transition">
                            <input type="radio" 
                                name="att_{{ $record['id'] }}_{{ $selectedTanggal }}" 
                                value="Izin" 
                                wire:click="saveAttendance({{ $record['id'] }}, 'Izin')" 
                                {{ (isset($attendance[$record['id']]) && $attendance[$record['id']] == 'Izin') ? 'checked' : '' }} 
                                class="w-5 h-5 text-yellow-500 focus:ring-yellow-500 border-gray-300 cursor-pointer">
                        </label>
                    </td>

                    {{-- Sakit --}}
                    <td class="px-4 py-2 text-center align-middle">
                        <label class="cursor-pointer inline-flex items-center justify-center p-2 rounded-full hover:bg-blue-50 transition">
                            <input type="radio" 
                                name="att_{{ $record['id'] }}_{{ $selectedTanggal }}" 
                                value="Sakit" 
                                wire:click="saveAttendance({{ $record['id'] }}, 'Sakit')" 
                                {{ (isset($attendance[$record['id']]) && $attendance[$record['id']] == 'Sakit') ? 'checked' : '' }} 
                                class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300 cursor-pointer">
                        </label>
                    </td>

                    {{-- Alpha (Tanpa Keterangan) --}}
                    <td class="px-4 py-2 text-center align-middle">
                        <label class="cursor-pointer inline-flex items-center justify-center p-2 rounded-full hover:bg-red-50 transition">
                            <input type="radio" 
                                name="att_{{ $record['id'] }}_{{ $selectedTanggal }}" 
                                value="Alpha" 
                                wire:click="saveAttendance({{ $record['id'] }}, 'Alpha')" 
                                {{ (isset($attendance[$record['id']]) && $attendance[$record['id']] == 'Alpha') ? 'checked' : '' }} 
                                class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300 cursor-pointer">
                        </label>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-sm text-gray-500">
                        Belum ada data siswa untuk kelas {{ $selectedKelas }}.
                    </td>
                </tr>
            @endforelse

        </x-organisms.data-table>

    @endif


    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL IMPORT KEHADIRAN                      --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($showImportModal)
        <x-shared.modal name="import-kehadiran" :show="true" maxWidth="md">
            <div class="flex flex-col">
                <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
                    <h2 class="text-base font-bold text-gray-900">Import Data Kehadiran</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Format: CSV, XLS, atau XLSX — maks 5 MB. File Excel otomatis dikonversi ke CSV.</p>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-700">
                        <p class="font-semibold mb-1">Kolom yang dibutuhkan:</p>
                        <code class="block">nis | nama | tanggal_kehadiran | status | tahun_ajaran | semester</code>
                        <p class="mt-1 text-blue-500">NIS harus terdaftar. Status: Hadir, Sakit, Izin, Alpha.</p>
                    </div>
                    <div x-data="{ dropping: false }" x-on:dragover.prevent="dropping = true" x-on:dragleave.prevent="dropping = false" x-on:drop.prevent="dropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))" x-on:click="$refs.fileInput.click()" class="border-2 border-dashed rounded-xl py-10 flex flex-col items-center justify-center cursor-pointer transition-colors" :class="dropping ? 'border-brand-teal bg-bg-light' : 'border-gray-200 hover:bg-gray-50'">
                        <input type="file" wire:model="importFile" accept=".csv,.xlsx,.xls" x-ref="fileInput" class="hidden">
                        <p class="text-sm font-medium text-gray-600">Klik atau tarik file ke sini</p>
                        <p class="text-xs text-gray-400 mt-1">CSV, XLS, XLSX — maks 5 MB</p>
                        @if($importFile)
                            <p class="mt-3 text-xs font-semibold text-brand-teal">✓ {{ $importFile->getClientOriginalName() }}</p>
                        @endif
                    </div>
                    @error('importFile')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @if($importedCount > 0)
                        <div class="bg-green-50 border border-green-100 rounded-lg px-4 py-2 text-sm text-green-700">✓ Berhasil memproses {{ $importedCount }} baris data.</div>
                    @endif
                    @if(!empty($importErrors))
                        <div class="bg-red-50 border border-red-100 rounded-lg px-4 py-2 text-xs text-red-700 max-h-40 overflow-y-auto">
                            <p class="font-semibold mb-1">Baris yang gagal:</p>
                            <ul class="list-disc pl-4 space-y-0.5">
                                @foreach($importErrors as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end gap-3 rounded-b-xl">
                    <x-atoms.button variant="secondary" wire:click="$set('showImportModal', false)">Batal</x-atoms.button>
                    <x-atoms.button wire:click="processImport" :disabled="!$importFile">
                        <span wire:loading.remove wire:target="processImport">Proses Import</span>
                        <span wire:loading wire:target="processImport">Memproses...</span>
                    </x-atoms.button>
                </div>
            </div>
        </x-shared.modal>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL EXPORT KEHADIRAN                      --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($showExportModal)
        <x-shared.modal name="export-kehadiran" :show="true" maxWidth="md">
            <div class="flex flex-col">
                <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
                    <h2 class="text-base font-bold text-gray-900">Export Data Kehadiran</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pilih format: CSV atau Excel</p>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="flex items-center gap-3 bg-teal-50 border border-teal-100 rounded-lg px-4 py-3">
                        <div>
                            <p class="text-xs text-gray-500">Data yang akan di-export</p>
                            <p class="text-2xl font-bold text-brand-teal leading-tight">{{ $exportPreviewCount ?? 0 }} <span class="text-sm font-normal text-gray-500">data</span></p>
                        </div>
                    </div>
                </div>
                <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end gap-3 rounded-b-xl">
                    <x-atoms.button variant="secondary" wire:click="$set('showExportModal', false)">Batal</x-atoms.button>
                    <x-atoms.button wire:click="exportCsv" :disabled="($exportPreviewCount ?? 0) === 0">
                        <span wire:loading.remove wire:target="exportCsv">Download CSV</span>
                        <span wire:loading wire:target="exportCsv">Menyiapkan...</span>
                    </x-atoms.button>
                    <x-atoms.button wire:click="exportExcel" :disabled="($exportPreviewCount ?? 0) === 0">
                        <span wire:loading.remove wire:target="exportExcel">Download Excel</span>
                        <span wire:loading wire:target="exportExcel">Menyiapkan...</span>
                    </x-atoms.button>
                </div>
            </div>
        </x-shared.modal>
    @endif

</div>
