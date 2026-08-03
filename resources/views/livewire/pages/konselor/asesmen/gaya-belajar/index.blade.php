<?php

use App\Livewire\Konselor\Asesmen\GayaBelajar\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {};

?>

<div>

    {{-- Header --}}
    <x-organisms.header action="createGayaBelajar">
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>

        <x-slot:actions>
            <button wire:click="downloadTemplate" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                <x-atoms.icon variant="template" size="md" /> Template
            </button>
            <button wire:click="openImport" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                <x-atoms.icon variant="upload" size="md" /> Import
            </button>
            <button wire:click="openExport" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                <x-atoms.icon variant="download" size="md" /> Export
            </button>
        </x-slot:actions>

        Tambah
    </x-organisms.header>


    {{-- Pilih Tingkat --}}
    @if(!$selectedTingkat)

        <div class="px-6 sm:px-8 py-6">

            {{-- Judul --}}
            <div class="mb-5">

                <h2 class="text-base font-semibold text-gray-800">
                    Pilih Tingkat
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Pilih tingkat kelas untuk melihat data Gaya Belajar.
                </p>

            </div>


            {{-- Card Tingkat --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                @foreach(['X', 'XI', 'XII'] as $tingkat)

                    <button
                        type="button"
                        wire:key="tingkat-{{ $tingkat }}"
                        wire:click="pilihTingkat('{{ $tingkat }}')"
                        class="group text-left bg-white border border-gray-200 rounded-xl p-6
                               shadow-sm transition-all duration-200
                               hover:border-brand-teal hover:shadow-md
                               hover:-translate-y-0.5">

                        <div class="flex items-center justify-between">

                            <div>

                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                                    Tingkat
                                </p>

                                <h3 class="mt-2 text-lg font-semibold text-gray-800
                                           group-hover:text-brand-teal">
                                    Gaya Belajar Kelas {{ $tingkat }}
                                </h3>

                            </div>

                            <div class="w-11 h-11 rounded-lg bg-teal-50
                                        flex items-center justify-center
                                        text-brand-teal">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-5 h-5"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332-.477 4.5-1.253m0-10.494C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332-.477-4.5-1.253" />

                                </svg>

                            </div>

                        </div>

                        <div class="mt-5 flex items-center text-xs text-gray-400">

                            <span>
                                Lihat Gaya Belajar
                            </span>

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7" />

                            </svg>

                        </div>

                    </button>

                @endforeach

            </div>

        </div>


    {{-- Tabel data (setelah pilih tingkat) --}}
    @else

        {{-- Header Kelas --}}
        <div class="px-6 sm:px-8 py-5 border-b border-gray-100">

            <button
                type="button"
                wire:click="kembaliKeTingkat"
                class="inline-flex items-center text-xs text-gray-500
                       hover:text-brand-teal mb-2">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 mr-1"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7" />

                </svg>

                Kembali ke Daftar Kelas

            </button>

            <h2 class="text-lg font-semibold text-gray-800">
                Gaya Belajar Kelas {{ $selectedTingkat }}
            </h2>

        </div>


    {{-- Toolbar --}}
    <x-organisms.table-toolbar
        onFilter="filterAction"
        onRefresh="$refresh"
    >
        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot:pagination>
    </x-organisms.table-toolbar>


    {{-- Filter --}}
    @if($showFilters)

        <div
            class="px-6 sm:px-8 py-3 border-b border-gray-100 bg-gray-50
                   flex items-center gap-4 text-sm text-gray-600 shrink-0
                   transition-all"
        >

            <span class="text-gray-500 text-xs font-medium">
                Filter Data:
            </span>


            {{-- Filter Kelas --}}
            <select
                wire:model.live="filterKelas"
                class="text-xs border border-gray-200 rounded px-2 py-1.5
                       focus:outline-none focus:ring-1 focus:ring-brand-teal
                       w-28 sm:w-36 pr-8 flex-shrink-0 bg-white cursor-pointer"
            >

                <option value="">
                    Semua Kelas
                </option>

                @foreach($kelasOptions ?? [] as $kelas)

                    <option value="{{ $kelas }}">
                        Kelas {{ $kelas }}
                    </option>

                @endforeach

            </select>


            {{-- Filter Jurusan --}}
            <select
                wire:model.live="filterJurusan"
                class="text-xs border border-gray-200 rounded px-2 py-1.5
                       focus:outline-none focus:ring-1 focus:ring-brand-teal
                       w-32 pr-8 flex-shrink-0 bg-white cursor-pointer"
            >

                <option value="">
                    Semua Jurusan
                </option>

                @foreach($jurusanOptions ?? [] as $jurusan)

                    <option value="{{ $jurusan }}">
                        {{ $jurusan }}
                    </option>

                @endforeach

            </select>


            {{-- Reset --}}
            @if($search !== '' || $filterKelas !== '' || $filterJurusan !== '')

                <button
                    wire:click="resetFilters"
                    class="text-xs text-brand-teal font-medium
                           hover:text-teal-700 hover:underline
                           transition-colors ml-auto"
                >

                    Reset Semua

                </button>

            @endif

        </div>

    @endif


    {{-- Selected --}}
    @if(count($selected) > 0)

        <div
            class="px-6 py-2 bg-teal-50 border-b border-teal-100
                   flex justify-between items-center text-sm"
        >

            <span class="text-xs font-medium text-brand-teal">

                {{ count($selected) }} data dipilih

            </span>

            <button
                wire:click="$set('selected', [])"
                class="text-xs text-gray-500 hover:text-gray-700"
            >

                Batal Pilih

            </button>

        </div>

    @endif


    {{-- Flash --}}
    <div class="px-4 py-2">

        <x-shared.flash-message />

    </div>


    {{-- Table --}}
    <x-organisms.data-table
        :headers="[
            'Tanggal',
            'Siswa',
            'Kelas',
            'Aksi'
        ]"

        empty="Belum ada data gaya belajar untuk kelas {{ $selectedTingkat }}."
    >

        @forelse($records as $record)

            <tr
                wire:key="gaya-belajar-{{ $record->id }}"
                wire:click="goToDetail({{ $record->id }})"
                class="group border-b border-gray-100 cursor-pointer
                       transition-all duration-200 h-12 relative
                       hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)]
                       hover:z-10 hover:rounded-md"
            >

                {{-- Tanggal --}}
                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap align-middle">
                    {{ $record->tanggal
                        ? \Carbon\Carbon::parse($record->tanggal)->isoFormat('D MMM Y')
                        : '-' }}
                </td>

                {{-- Siswa --}}
                <td class="px-4 py-2 font-semibold align-middle">

                    <a
                        href="{{ route('konselor.asesmen.gaya-belajar.detail', $record->id) }}"
                        wire:navigate
                        onclick="event.stopPropagation()"
                        class="text-gray-900 hover:text-teal-600 hover:underline transition"
                    >
                        {{ $record->siswa?->nama ?? '-' }}
                    </a>

                    <p class="text-[11px] text-gray-400">
                        NIS {{ $record->siswa?->nis ?? '-' }}
                    </p>

                </td>

                {{-- Kelas --}}
                <td class="px-4 py-2 text-sm text-gray-700 align-middle">
                    {{ $record->siswa?->kelas_label ?? '-' }}
                </td>

                {{-- Aksi --}}
                <td
                    class="px-4 py-2 text-right align-middle"
                    onclick="event.stopPropagation()"
                >

                    <div class="flex items-center justify-end gap-2">

                        {{-- Edit --}}
                        <x-atoms.action-button
                            color="blue"
                            title="Edit"
                            wire:click="loadGayaBelajar({{ $record->id }})"
                        >

                            <x-atoms.icon
                                variant="edit"
                                size="sm"
                            />

                        </x-atoms.action-button>

                        {{-- Hapus --}}
                        <x-atoms.action-button
                            color="red"
                            title="Hapus"
                            wire:click="delete({{ $record->id }})"
                            wire:confirm="Yakin ingin menghapus data gaya belajar ini?"
                        >

                            <x-atoms.icon
                                variant="delete"
                                size="sm"
                            />

                        </x-atoms.action-button>

                    </div>

                </td>

            </tr>

        @empty

        @endforelse
            </x-organisms.data-table>

    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL IMPORT                              --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($showImportModal)
        <x-shared.modal name="import-gaya-belajar" :show="true" maxWidth="md">
            <div class="flex flex-col">
                <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
                    <h2 class="text-base font-bold text-gray-900">Import Data Gaya Belajar</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Format: CSV, XLS, atau XLSX — maks 5 MB. File Excel otomatis dikonversi ke CSV.</p>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-700">
                        <p class="font-semibold mb-1">Kolom yang dibutuhkan:</p>
                        <code class="block">nis | tanggal | visual | auditori | kinestetik | hasil | catatan | faktor_penghambat | faktor_pendukung</code>
                        <p class="mt-1 text-blue-500">Skor tiap gaya 0-100. Bisa juga mengimpor CSV Google Forms (39 kolom pernyataan); skor dihitung dari jumlah jawaban "Ya" per kelompok. NIS + tanggal adalah kunci.</p>
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
    {{-- MODAL EXPORT                              --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($showExportModal)
        <x-shared.modal name="export-gaya-belajar" :show="true" maxWidth="md">
            <div class="flex flex-col">
                <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
                    <h2 class="text-base font-bold text-gray-900">Export Data Gaya Belajar</h2>
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

    {{-- Modal Gaya Belajar --}}
    @include('livewire.partials.asesmen.gaya-belajar.gaya-belajar-modal', [
        'editingId' => $editingId,
    ])

</div>