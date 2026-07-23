<?php

use App\Livewire\Admin\Siswa\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full">

    {{-- ── Header ───────────────────────────────── --}}
    <header class="h-20 border-b border-gray-200 px-8 flex items-center justify-between shrink-0">

        <x-molecules.search-input model="search" />

        <div class="flex items-center gap-2">

            {{-- Export CSV --}}
            <button
                wire:click="openExport"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-600
                       border border-gray-300 rounded-md hover:bg-gray-50 transition">
                <x-atoms.icon variant="filter" size="md" />
                Export CSV
            </button>

            {{-- Import --}}
            <button
                wire:click="openImport"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-600
                       border border-gray-300 rounded-md hover:bg-gray-50 transition">
                <x-atoms.icon variant="plus" size="md" />
                Import
            </button>

            {{-- Tambah Siswa --}}
            <x-atoms.button wire:click="create">
                <x-atoms.icon variant="plus" size="md" />
                Tambah Siswa
            </x-atoms.button>

        </div>
    </header>

    {{-- ── Stats Bar ────────────────────────────── --}}
    <div class="px-8 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-6 text-sm text-gray-600 shrink-0">
        <span>Total: <strong class="text-gray-900">{{ $stats['total'] }}</strong></span>
        <span>Laki-laki: <strong class="text-gray-900">{{ $stats['laki'] }}</strong></span>
        <span>Perempuan: <strong class="text-gray-900">{{ $stats['perempuan'] }}</strong></span>

        {{-- Filter cepat kelas --}}
        <div class="ml-auto flex items-center gap-2">
            <span class="text-gray-400 text-xs">Filter:</span>

            {{-- Kelas --}}
            <select wire:model.live="filterKelas"
                class="text-xs border border-gray-200 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-6 flex-shrink-0">
                <option value="">Semua Kelas</option>
                @foreach($filterOptions['kelas'] as $kelasId => $kelasNama)
                    <option value="{{ $kelasId }}">{{ $kelasNama }}</option>
                @endforeach
            </select>

            {{-- Jurusan --}}
            <select wire:model.live="filterJurusan"
                class="text-xs border border-gray-200 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-6 flex-shrink-0">
                <option value="">Semua Jurusan</option>
                @foreach($filterOptions['jurusan'] as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                @endforeach
            </select>

            {{-- Jenis Kelamin --}}
         <select wire:model.live="filterJenisKelamin"
        class="appearance-none text-xs border border-gray-200 rounded pl-2 pr-7 py-1 focus:outline-none focus:ring-1 focus:ring-brand-teal">
        <option value="">Semua</option>
        <option value="L">Laki-laki</option>
        <option value="P">Perempuan</option>
         </select>

            <button wire:click="resetFilters"
                class="text-xs text-brand-teal hover:underline">Reset</button>
        </div>
    </div>

    {{-- ── Flash Message ────────────────────────── --}}
    <div class="px-4 py-2">
        <x-shared.flash-message />
    </div>

    {{-- ── Tabel Siswa ──────────────────────────── --}}
    {{-- Wrapper: flex-col, tabel scroll di dalam, footer pagination selalu kelihatan --}}
    <div class="flex flex-col flex-1 min-h-0">

        {{-- Tabel scrollable --}}
        <div class="flex-1 overflow-auto">
            {{-- class="w-full" override flex-1 overflow-auto bawaan organism --}}
            <x-organisms.data-table class="w-full" empty="Belum ada data siswa.">
                @foreach($records as $siswa)
                    <tr wire:key="siswa-{{ $siswa->id }}"
                        wire:click="openDetail({{ $siswa->id }})"
                        class="group border-b border-gray-100 bg-white transition-all duration-200 h-12 relative
                               hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1)] hover:z-10 cursor-pointer">

                        {{-- Checkbox --}}
                        <td class="w-16 text-center align-middle rounded-l-md py-2" onclick="event.stopPropagation()">
                            <input type="checkbox"
                                class="w-4 h-4 rounded border-gray-300 text-brand-teal accent-brand-teal cursor-pointer">
                        </td>

                        {{-- NIS --}}
                        <td class="px-4 py-2 w-24 font-mono text-xs text-gray-500 align-middle">
                            {{ $siswa->nis }}
                        </td>

                        {{-- Nama --}}
                        <td class="px-4 py-2 w-1/3 font-semibold text-gray-900 align-middle">
                            {{ $siswa->nama }}
                        </td>

                        {{-- Kelas + Jurusan --}}
                        <td class="px-4 py-2 align-middle text-xs text-gray-600">
                            {{ $siswa->kelas_label }}
                        </td>

                        {{-- Foto --}}
                        <td class="px-4 py-2 align-middle">
                            @if($siswa->user->foto)
                                <img src="{{ asset('storage/' . $siswa->user->foto) }}" alt="{{ $siswa->nama }}"
                                    class="w-8 h-8 rounded-full object-cover border border-gray-200">
                            @else
                                <div class="w-8 h-8 rounded-full bg-icon-bg text-primary flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($siswa->nama, 0, 1)) }}{{ strtoupper(substr(strstr($siswa->nama, ' ') ?: '_', 1, 1)) }}
                                </div>
                            @endif
                        </td>

                        {{-- Jenis Kelamin --}}
                        <td class="px-4 py-2 align-middle">
                            <span @class([
                                'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                'bg-blue-100 text-blue-700'   => $siswa->jenis_kelamin === 'L',
                                'bg-pink-100 text-pink-700'   => $siswa->jenis_kelamin === 'P',
                            ])>
                                {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>

                        {{-- Alamat --}}
                        <td class="px-4 py-2 text-xs text-gray-500 align-middle max-w-[200px] truncate" title="{{ $siswa->alamat }}">
                            {{ $siswa->alamat ?? '-' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-2 align-middle">
                            <span @class([
                                'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                'bg-green-100 text-green-700' => ($siswa->user->status ?? 'aktif') === 'aktif',
                                'bg-red-100 text-red-700'     => ($siswa->user->status ?? 'aktif') !== 'aktif',
                            ])>
                                {{ $siswa->user->status ?? 'aktif' }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-2 w-40 text-right align-middle relative rounded-r-md">
                            <x-molecules.table-action :id="$siswa->id">
                                <x-slot:edit><span class="sr-only">Edit</span></x-slot:edit>
                                <x-slot:delete><span class="sr-only">Hapus</span></x-slot:delete>
                            </x-molecules.table-action>
                        </td>
                    </tr>
                @endforeach
            </x-organisms.data-table>
        </div>

        {{-- Footer Pagination — selalu menempel di bawah, tidak ikut scroll --}}
        <div class="border-t border-gray-100 bg-white px-6 py-2.5 flex items-center justify-between shrink-0">

            {{-- Kiri: info + dropdown per page --}}
            <div class="flex items-center gap-3 text-xs text-gray-500">
                <span>
                    Menampilkan
                    <strong class="text-gray-700">{{ $records->firstItem() ?? 0 }}</strong>
                    –
                    <strong class="text-gray-700">{{ $records->lastItem() ?? 0 }}</strong>
                    dari
                    <strong class="text-gray-700">{{ $records->total() }}</strong>
                    siswa
                </span>

                <div class="h-3.5 w-px bg-gray-200"></div>

                <div class="flex items-center gap-1.5">
                    <label for="perPage" class="text-gray-400 whitespace-nowrap">Tampilkan</label>
                    <select
                        id="perPage"
                        wire:model.live="perPage"
                        class="border border-gray-200 rounded px-2 py-1 text-xs text-gray-700
                               focus:outline-none focus:ring-1 focus:ring-brand-teal focus:border-brand-teal
                               bg-white cursor-pointer w-16">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="35">35</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-gray-400">per halaman</span>
                </div>
            </div>

            {{-- Kanan: navigasi halaman --}}
            <div class="flex items-center gap-1">

                {{-- Tombol Previous --}}
                @if($records->onFirstPage())
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium
                                 text-gray-300 border border-gray-100 rounded-md cursor-not-allowed select-none">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Sebelumnya
                    </span>
                @else
                    <button
                        wire:click="previousPage"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium
                               text-gray-600 border border-gray-200 rounded-md
                               hover:bg-gray-50 hover:border-gray-300 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Sebelumnya
                    </button>
                @endif

                {{-- Nomor halaman --}}
                <div class="flex items-center gap-0.5 mx-1">
                    @foreach($records->getUrlRange(
                        max(1, $records->currentPage() - 2),
                        min($records->lastPage(), $records->currentPage() + 2)
                    ) as $page => $url)
                        @if($page === $records->currentPage())
                            <span class="inline-flex items-center justify-center w-7 h-7 text-xs font-bold
                                         bg-brand-teal text-white rounded-md">
                                {{ $page }}
                            </span>
                        @else
                            <button
                                wire:click="gotoPage({{ $page }})"
                                class="inline-flex items-center justify-center w-7 h-7 text-xs font-medium
                                       text-gray-600 hover:bg-gray-100 rounded-md transition-colors">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                </div>

                {{-- Tombol Next --}}
                @if($records->hasMorePages())
                    <button
                        wire:click="nextPage"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium
                               text-gray-600 border border-gray-200 rounded-md
                               hover:bg-gray-50 hover:border-gray-300 transition-colors">
                        Berikutnya
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium
                                 text-gray-300 border border-gray-100 rounded-md cursor-not-allowed select-none">
                        Berikutnya
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                @endif

            </div>
        </div>

    </div>{{-- end wrapper --}}


    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL FORM (Tambah / Edit)                  --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($showForm)
        <x-shared.modal name="form-siswa" :show="true" maxWidth="lg">
            <div class="flex flex-col max-h-[90vh]">

                {{-- Header Modal --}}
                <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
                    <h2 class="text-base font-bold text-gray-900">
                        {{ $editingId ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Isi semua field yang wajib diisi (*)
                    </p>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 overflow-y-auto modal-scroll grow space-y-4"
                     style="scrollbar-width: thin;">

                    {{-- NIS --}}
                    <div>
                        <x-atoms.input-label for="nis" size="sm">NIS *</x-atoms.input-label>
                        <x-atoms.text-input
                            id="nis"
                            type="number"
                            wire:model="nis"
                            placeholder="Contoh: 21001"
                            size="md"
                            :disabled="(bool) $editingId"
                        />
                        @error('nis')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @if($editingId)
                            <p class="mt-1 text-xs text-gray-400">NIS tidak dapat diubah setelah dibuat.</p>
                        @endif
                    </div>

                    {{-- Nama --}}
                    <div>
                        <x-atoms.input-label for="nama" size="sm">Nama Lengkap *</x-atoms.input-label>
                        <x-atoms.text-input
                            id="nama"
                            type="text"
                            wire:model="nama"
                            placeholder="Nama lengkap siswa"
                            size="md"
                        />
                        @error('nama')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <x-atoms.input-label for="alamat" size="sm">Alamat *</x-atoms.input-label>
                        <x-atoms.text-input
                            id="alamat"
                            type="text"
                            wire:model="alamat"
                            placeholder="Alamat siswa"
                            size="md"
                        />
                        @error('alamat')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kelas + Jenis Kelamin (2 kolom) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-atoms.input-label for="kelas" size="sm">Kelas *</x-atoms.input-label>
                            <select id="kelas" wire:model="kelas"
                                class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm
                                       focus:outline-none focus:ring-1 focus:ring-brand-teal focus:border-brand-teal
                                       transition duration-150 bg-white">
                                <option value="">Pilih Kelas</option>
                                @foreach($filterOptions['kelas'] as $kelasId => $kelasNama)
                                    <option value="{{ $kelasId }}">{{ $kelasNama }}</option>
                                @endforeach
                            </select>
                            @error('kelas')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-atoms.input-label for="jenis_kelamin" size="sm">Jenis Kelamin *</x-atoms.input-label>
                            <x-molecules.input-dropdown
                                id="jenis_kelamin"
                                wire:model="jenis_kelamin"
                                size="md"
                                :options="$jenisKelaminOptions"
                            />
                            @error('jenis_kelamin')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end gap-3 shrink-0 rounded-b-xl">
                    <x-atoms.button variant="secondary" wire:click="cancelForm">Batal</x-atoms.button>
                    <x-atoms.button wire:click="save">
                        <span wire:loading.remove wire:target="save">
                            {{ $editingId ? 'Perbarui' : 'Simpan' }}
                        </span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </x-atoms.button>
                </div>
            </div>
        </x-shared.modal>
    @endif


    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL IMPORT                                --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($showImportModal)
        <x-shared.modal name="import-siswa" :show="true" maxWidth="md">
            <div class="flex flex-col">

                <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
                    <h2 class="text-base font-bold text-gray-900">Import Data Siswa</h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Format: CSV, XLS, atau XLSX — maks 5 MB
                    </p>
                </div>

                <div class="px-6 py-5 space-y-4">

                    {{-- Panduan format --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-700">
                        <p class="font-semibold mb-1">Kolom yang dibutuhkan:</p>
                        <code class="block">nis | nama | kelas | jenis_kelamin | jurusan</code>
                        <p class="mt-1 text-blue-500">NIS yang sudah ada akan diperbarui (upsert).</p>
                    </div>

                    {{-- Upload area --}}
                    <div x-data="{ dropping: false }"
                         x-on:dragover.prevent="dropping = true"
                         x-on:dragleave.prevent="dropping = false"
                         x-on:drop.prevent="dropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                         x-on:click="$refs.fileInput.click()"
                         class="border-2 border-dashed rounded-xl py-10 flex flex-col items-center justify-center cursor-pointer transition-colors"
                         :class="dropping ? 'border-brand-teal bg-bg-light' : 'border-gray-200 hover:bg-gray-50'">

                        <input type="file" wire:model="importFile" accept=".csv,.xlsx,.xls" x-ref="fileInput" class="hidden">

                        <x-atoms.icon variant="plus" size="xl" class="text-gray-300 mb-2" />
                        <p class="text-sm font-medium text-gray-600">Klik atau tarik file ke sini</p>
                        <p class="text-xs text-gray-400 mt-1">CSV, XLS, XLSX — maks 5 MB</p>

                        @if($importFile)
                            <p class="mt-3 text-xs font-semibold text-brand-teal">
                                ✓ {{ $importFile->getClientOriginalName() }}
                            </p>
                        @endif
                    </div>

                    @error('importFile')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Hasil import --}}
                    @if($importedCount !== null)
                        <div class="bg-green-50 border border-green-100 rounded-lg px-4 py-2 text-sm text-green-700">
                            ✓ Berhasil memproses {{ $importedCount }} baris data.
                        </div>
                    @endif

                    {{-- Error rows --}}
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
    {{-- MODAL EXPORT CSV                            --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($showExportModal)
        <x-shared.modal name="export-siswa" :show="true" maxWidth="md">
            <div class="flex flex-col">

                {{-- Header --}}
                <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
                    <h2 class="text-base font-bold text-gray-900">Export Data Siswa</h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Pilih filter data yang akan di-export ke CSV
                    </p>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 space-y-4">

                    {{-- Preview count --}}
                    <div class="flex items-center gap-3 bg-teal-50 border border-teal-100 rounded-lg px-4 py-3">
                        <div class="w-10 h-10 rounded-full bg-brand-teal/10 flex items-center justify-center shrink-0">
                            <x-atoms.icon variant="student" size="md" class="text-brand-teal" />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Data yang akan di-export</p>
                            <p class="text-2xl font-bold text-brand-teal leading-tight">
                                {{ $exportPreviewCount ?? 0 }}
                                <span class="text-sm font-normal text-gray-500">siswa</span>
                            </p>
                        </div>

                        {{-- Loading indicator saat filter berubah --}}
                        <div wire:loading
                             wire:target="exportKelas,exportJurusan,exportPeriode"
                             class="ml-auto">
                            <svg class="animate-spin h-4 w-4 text-brand-teal" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-px bg-gray-100"></div>
                        <span class="text-xs text-gray-400 font-medium">Filter (opsional)</span>
                        <div class="flex-1 h-px bg-gray-100"></div>
                    </div>

                    {{-- Filter: Kelas --}}
                    <div>
                        <x-atoms.input-label for="exportKelas" size="sm">Kelas</x-atoms.input-label>
                        <select
                            id="exportKelas"
                            wire:model.live="exportKelas"
                            class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm
                                   focus:outline-none focus:ring-1 focus:ring-brand-teal focus:border-brand-teal
                                   transition duration-150 bg-white">
                            <option value="">Semua Kelas</option>
                            @foreach($filterOptions['kelas'] as $kelasId => $kelasNama)
                                <option value="{{ $kelasId }}">{{ $kelasNama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter: Jurusan --}}
                    <div>
                        <x-atoms.input-label for="exportJurusan" size="sm">Jurusan</x-atoms.input-label>
                        <select
                            id="exportJurusan"
                            wire:model.live="exportJurusan"
                            class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm
                                   focus:outline-none focus:ring-1 focus:ring-brand-teal focus:border-brand-teal
                                   transition duration-150 bg-white">
                            <option value="">Semua Jurusan</option>
                            @foreach($filterOptions['jurusan'] as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter: Periode Ajaran --}}
                    <div>
                        <x-atoms.input-label for="exportPeriode" size="sm">Periode Ajaran</x-atoms.input-label>
                        <select
                            id="exportPeriode"
                            wire:model.live="exportPeriode"
                            class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm
                                   focus:outline-none focus:ring-1 focus:ring-brand-teal focus:border-brand-teal
                                   transition duration-150 bg-white">
                            <option value="">Semua Periode</option>
                            @foreach($filterOptions['periode'] as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Info nama file --}}
                    <p class="text-xs text-gray-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z"/>
                        </svg>
                        File akan diunduh sebagai
                        <strong>data-siswa-{{ now()->format('Ymd') }}.csv</strong>
                    </p>

                </div>

                {{-- Footer --}}
                <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-between items-center shrink-0 rounded-b-xl">

                    {{-- Reset filter --}}
                    <button
                        wire:click="$set('exportKelas', ''); $set('exportJurusan', ''); $set('exportPeriode', '')"
                        class="text-xs text-gray-400 hover:text-gray-600 transition">
                        Reset filter
                    </button>

                    <div class="flex gap-3">
                        <x-atoms.button variant="secondary" wire:click="$set('showExportModal', false)">
                            Batal
                        </x-atoms.button>

                        <x-atoms.button
                            wire:click="exportCsv"
                            :disabled="($exportPreviewCount ?? 0) === 0">
                            <span wire:loading.remove wire:target="exportCsv">
                                <svg class="w-4 h-4 mr-1 inline-block" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download CSV
                            </span>
                            <span wire:loading wire:target="exportCsv">Menyiapkan...</span>
                        </x-atoms.button>
                    </div>
                </div>

            </div>
        </x-shared.modal>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL DETAIL SISWA                          --}}
    {{-- ═══════════════════════════════════════════ --}}
    <x-shared.modal name="detail-siswa" :show="$showDetail" maxWidth="lg">
        <div class="flex flex-col max-h-[90vh]">
            {{-- Header --}}
            <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
                <h2 class="text-base font-bold text-gray-900">Detail Siswa</h2>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 overflow-y-auto grow space-y-5">
                @if($detailSiswa)
                    {{-- Data Pribadi --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Data Pribadi</h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><span class="text-gray-500">NIS</span><p class="font-medium">{{ $detailSiswa->nis }}</p></div>
                            <div><span class="text-gray-500">Nama</span><p class="font-medium">{{ $detailSiswa->nama }}</p></div>
                            <div><span class="text-gray-500">Jenis Kelamin</span><p class="font-medium">{{ $detailSiswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p></div>
                            <div><span class="text-gray-500">Kelas</span><p class="font-medium">{{ $detailSiswa->kelas_label }}</p></div>
                            <div><span class="text-gray-500">Jurusan</span><p class="font-medium">{{ $detailSiswa->jurusan_label }}</p></div>
                            <div><span class="text-gray-500">Alamat</span><p class="font-medium">{{ $detailSiswa->alamat ?? '-' }}</p></div>
                            <div><span class="text-gray-500">Tempat Lahir</span><p class="font-medium">{{ $detailSiswa->tempat_lahir ?? '-' }}</p></div>
                            <div><span class="text-gray-500">Tanggal Lahir</span><p class="font-medium">{{ $detailSiswa->tgl_lahir ?? '-' }}</p></div>
                            <div><span class="text-gray-500">Agama</span><p class="font-medium">{{ $detailSiswa->agama ?? '-' }}</p></div>
                            <div><span class="text-gray-500">Asal SMP</span><p class="font-medium">{{ $detailSiswa->asal_smp ?? '-' }}</p></div>
                            <div><span class="text-gray-500">Anak Ke</span><p class="font-medium">{{ $detailSiswa->anak_ke ?? '-' }}</p></div>
                            <div><span class="text-gray-500">Jml Saudara</span><p class="font-medium">{{ $detailSiswa->jml_saudara ?? '-' }}</p></div>
                            <div><span class="text-gray-500">Hobi</span><p class="font-medium">{{ $detailSiswa->hobi ?? '-' }}</p></div>
                            <div><span class="text-gray-500">Bakat</span><p class="font-medium">{{ $detailSiswa->bakat ?? '-' }}</p></div>
                            <div class="col-span-2"><span class="text-gray-500">Rencana Setelah Lulus</span><p class="font-medium">{{ $detailSiswa->rencana_lulus ?? '-' }}</p></div>
                            @if($detailSiswa->detail_rencana_lulus)
                                <div class="col-span-2"><span class="text-gray-500">Detail Rencana</span><p class="font-medium">{{ $detailSiswa->detail_rencana_lulus }}</p></div>
                            @endif
                        </div>
                    </div>

                    {{-- Data Keluarga --}}
                    @if($detailSiswa->keluarga)
                        <hr class="border-gray-100">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Data Keluarga</h3>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div><span class="text-gray-500">Ayah</span><p class="font-medium">{{ $detailSiswa->keluarga->nama_ayah ?? '-' }}</p></div>
                                <div><span class="text-gray-500">Ibu</span><p class="font-medium">{{ $detailSiswa->keluarga->nama_ibu ?? '-' }}</p></div>
                                <div><span class="text-gray-500">Pendidikan Ayah</span><p class="font-medium">{{ $detailSiswa->keluarga->pendidikan_ayah ?? '-' }}</p></div>
                                <div><span class="text-gray-500">Pendidikan Ibu</span><p class="font-medium">{{ $detailSiswa->keluarga->pendidikan_ibu ?? '-' }}</p></div>
                                <div><span class="text-gray-500">Pekerjaan Ayah</span><p class="font-medium">{{ $detailSiswa->keluarga->pekerjaan_ayah ?? '-' }}</p></div>
                                <div><span class="text-gray-500">Pekerjaan Ibu</span><p class="font-medium">{{ $detailSiswa->keluarga->pekerjaan_ibu ?? '-' }}</p></div>
                                <div><span class="text-gray-500">No. Telepon</span><p class="font-medium">{{ $detailSiswa->keluarga->telp_ortu ?? '-' }}</p></div>
                                <div><span class="text-gray-500">Status Rumah</span><p class="font-medium">{{ $detailSiswa->keluarga->status_rumah ?? '-' }}</p></div>
                            </div>
                        </div>
                    @endif

                    {{-- Total Konsultasi --}}
                    <hr class="border-gray-100">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-500">Total Konsultasi:</span>
                        <span class="font-semibold text-brand-teal">{{ $detailSiswa->total_konsultasi }}</span>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl">
                <x-atoms.button variant="secondary" wire:click="closeDetail">Tutup</x-atoms.button>
            </div>
        </div>
    </x-shared.modal>

</div>
