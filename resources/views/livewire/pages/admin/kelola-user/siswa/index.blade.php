<?php

use App\Livewire\Admin\KelolaUser\Siswa\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app', ['title' => 'Kelola Data Siswa'])] class extends Index {}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full" x-data="{ loading: false }"
    x-on:click="if ($event.target.closest('button[wire\\:click^=\'edit\'], button[wire\\:click=\'create\']')) loading = true"
    x-on:open-modal.window="loading = false" x-on:close-modal.window="loading = false">

    <x-organisms.header action="create">
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot>
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
        Tambah User Siswa
    </x-organisms.header>

    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="$refresh">
        <x-slot:pagination>
            {{ count($records) }} data
            </x-slot>
    </x-organisms.table-toolbar>

    {{-- Baris Filter --}}
    @if($showFilters)
        <div
            class="px-6 sm:px-8 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-4 text-sm text-gray-600">

            <span class="text-gray-500 text-xs font-medium">
                Filter Data:
            </span>

            {{-- Kelas --}}
            <select
                wire:model.live="filterKelas"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 w-36 bg-white">

                <option value="">Semua Kelas</option>

                @foreach($kelasOptions as $id => $kelas)
                    <option value="{{ $id }}">
                        {{ $kelas }}
                    </option>
                @endforeach
            </select>

            {{-- Jurusan --}}
            <select
                wire:model.live="filterJurusan"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 w-40 bg-white">

                <option value="">Semua Jurusan</option>

                @foreach($jurusanOptions as $jurusan)
                    <option value="{{ $jurusan }}">
                        {{ $jurusan }}
                    </option>
                @endforeach
            </select>

            {{-- Jenis Kelamin --}}
            <select
                wire:model.live="filterJenisKelamin"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 w-36 bg-white">

                <option value="">Semua</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>

            {{-- Reset --}}
            @if(
                $search ||
                $filterKelas ||
                $filterJurusan ||
                $filterJenisKelamin
            )
                <button
                    wire:click="resetFilters"
                    class="ml-auto text-xs text-brand-teal hover:underline">

                    Reset Semua

                </button>
            @endif

        </div>
    @endif

    {{-- Indikator jumlah yang dipilih --}}
    @if(count($selected) > 0)
        <div class="px-6 py-2 bg-teal-50 border-b border-teal-100 flex justify-between items-center text-sm">
            <span class="text-xs font-medium text-brand-teal">{{ count($selected) }} data dipilih</span>
            <button wire:click="$set('selected', [])" class="text-xs text-gray-500 hover:text-gray-700">Batal Pilih</button>
        </div>
    @endif

    <div class="px-4 py-2">
        <x-shared.flash-message />
    </div>

    <x-organisms.data-table 
        :headers="[
            '',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Jurusan',
            'Jenis Kelamin',
            'Aksi',
        ]"
    empty="Belum ada data siswa.">
        @foreach($records as $record)
            <tr
                wire:key="siswa-{{ $record->id }}"
                class="group border-b border-gray-100 transition-all duration-200 h-12 relative cursor-pointer
                    hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)]
                    hover:z-10 hover:rounded-md
                    {{ in_array($record->id, $selected) ? 'bg-teal-50/50' : 'bg-white' }}">

                {{-- Checkbox --}}
                <td class="w-16 text-center align-middle rounded-l-md py-2"
                    onclick="event.stopPropagation()">
                    <input
                        type="checkbox"
                        value="{{ $record->id }}"
                        wire:model.live="selected"
                        class="w-4 h-4 rounded border-gray-300 text-brand-teal focus:ring-brand-teal accent-brand-teal cursor-pointer">
                </td>

                {{-- NIS --}}
                <td class="px-4 py-2 font-medium text-gray-700 text-xs">
                    {{ $record->nis }}
                </td>

                {{-- Nama --}}
                <td class="px-4 py-2 font-semibold text-gray-900">
                    {{ $record->nama }}
                </td>

                {{-- Kelas --}}
                <td class="px-4 py-2 text-gray-700 text-xs">
                    {{ $record->kelas_label }}
                </td>

                {{-- Jurusan --}}
                <td class="px-4 py-2 text-gray-700 text-xs">
                    {{ $record->jurusan_label }}
                </td>

                {{-- Jenis Kelamin --}}
                <td class="px-4 py-2 text-xs">
                    @if($record->jenis_kelamin === 'L')
                        <span
                            class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                            Laki-laki
                        </span>
                    @else
                        <span
                            class="px-2 py-1 rounded-full bg-pink-100 text-pink-700 font-medium">
                            Perempuan
                        </span>
                    @endif
                </td>

                {{-- Aksi --}}
                <td class="px-4 py-2 text-right relative rounded-r-md">

                    <x-molecules.table-action :id="$record->id">
                        <x-slot:edit>
                            <span class="sr-only">Edit</span>
                        </x-slot>

                        <x-slot:delete>
                            <span class="sr-only">Delete</span>
                        </x-slot>
                    </x-molecules.table-action>

                </td>

            </tr>
        @endforeach
    </x-organisms.data-table>

    <livewire:partials.admin.kelola-user.siswa.siswa-modal />

    <!-- Skeleton Loading Modal Overlay -->
    <div x-show="loading" style="display: none;" x-transition:leave="transition-opacity duration-300 ease-in"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>

        <!-- Skeleton Modal Panel -->
        <div
            class="bg-white rounded-xl shadow-2xl flex flex-col w-full sm:max-w-lg h-full max-h-[80vh] overflow-hidden relative z-10 transition-all">
            <!-- Header Skeleton -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 shrink-0 flex flex-col gap-2">
                <div class="h-5 bg-gray-200 rounded-md w-1/3 animate-pulse"></div>
                <div class="h-3 bg-gray-200 rounded-md w-1/4 animate-pulse"></div>
            </div>

            <!-- Body Skeleton (Scrollable like original) -->
            <div class="px-6 py-6 overflow-y-auto modal-scroll grow flex flex-col gap-5" style="scrollbar-width: thin;">
                <!-- Siswa Progress Bar -->
                <div>
                    <div class="h-4 bg-gray-200 rounded w-1/4 mb-3 animate-pulse"></div>
                    <div class="flex gap-2.5">
                        <div class="h-2.5 w-1/2 bg-gray-200 rounded-full animate-pulse"></div>
                        <div class="h-2.5 w-1/2 bg-gray-100 rounded-full animate-pulse"></div>
                    </div>
                </div>

                <!-- Input Skeletons -->
                <div>
                    <div class="h-4 bg-gray-200 rounded w-1/5 mb-2 animate-pulse"></div>
                    <div class="h-[74px] bg-gray-100 rounded-lg w-full animate-pulse border border-gray-100"></div>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <div class="h-4 bg-gray-200 rounded w-1/3 mb-2 animate-pulse"></div>
                        <div class="h-10 bg-gray-100 rounded border border-gray-100 animate-pulse"></div>
                    </div>
                    <div>
                        <div class="h-4 bg-gray-200 rounded w-1/3 mb-2 animate-pulse"></div>
                        <div class="h-10 bg-gray-100 rounded border border-gray-100 animate-pulse"></div>
                    </div>
                </div>
            </div>

            <!-- Footer Skeleton -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-3">
                <div class="h-10 w-24 bg-gray-200 rounded-md animate-pulse"></div>
                <div class="h-10 w-32 bg-gray-300 rounded-md animate-pulse"></div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL IMPORT KOMULATIF RECORD              --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($showImportModal)
        <x-shared.modal name="import-siswa" :show="true" maxWidth="md">
            <div class="flex flex-col">
                <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
                    <h2 class="text-base font-bold text-gray-900">Import Komulatif Record</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Format: CSV, XLS, atau XLSX — maks 5 MB. Siswa dicocokkan via Nama + Kelas; bila belum ada, otomatis dibuat.</p>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-700">
                        <p class="font-semibold mb-1">Kolom yang dibutuhkan:</p>
                        <code class="block">Timestamp | KELAS | TAHUN PELAJARAN | NAMA LENGKAP | JENIS KELAMIN | TEMPAT, TANGGAL LAHIR | ASAL SMP | AGAMA | ALAMAT RUMAH (RT, RW) | FOTO DIRI / SELFIE | NAMA LENGKAP AYAH / WALI | ... | AKUN MEDIA SOSIAL</code>
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
    {{-- MODAL EXPORT KOMULATIF RECORD              --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($showExportModal)
        <x-shared.modal name="export-siswa" :show="true" maxWidth="md">
            <div class="flex flex-col">
                <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
                    <h2 class="text-base font-bold text-gray-900">Export Komulatif Record</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pilih format: CSV atau Excel</p>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="flex items-center gap-3 bg-teal-50 border border-teal-100 rounded-lg px-4 py-3">
                        <div>
                            <p class="text-xs text-gray-500">Data yang akan di-export</p>
                            <p class="text-2xl font-bold text-brand-teal leading-tight">{{ $exportPreviewCount ?? 0 }} <span class="text-sm font-normal text-gray-500">siswa</span></p>
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