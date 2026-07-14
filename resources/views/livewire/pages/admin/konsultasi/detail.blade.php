<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\Konsultasi; // Pastikan namespace Model kamu benar
use App\Services\KonsultasiService;

new #[Layout('layouts.app')] class extends Component {
    public $record;

    public $search = '';

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return [];
        }

        return Konsultasi::with('siswa')
            ->where(function ($query) {
                $query->whereHas('siswa.user', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('jenis_layanan', 'like', '%' . $this->search . '%');
            })
            ->take(5)
            ->get();
    }

    public function mount($id)
    {
        $this->record = Konsultasi::with(['siswa.kelas.jurusan', 'lampirans', 'gurubk.user'])->findOrFail($id);
    }

    public function goBack()
    {
        // Sesuaikan dengan URL halaman utama konsultasi kamu
        return redirect()->to('/admin/konsultasi');
    }

    public function edit()
    {
        $this->dispatch('edit-konsultasi', id: $this->record->id);
    }

    public function delete()
    {
        $service = app(KonsultasiService::class);
        $service->delete($this->record->id);

        session()->flash('success', 'Konsultasi berhasil dihapus!');
        return redirect()->to('/admin/konsultasi');
    }
}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white min-h-screen p-8 lg:p-12">

    <!-- Top Navigation Header Wrapper (Full Bleed) -->
    <div class="-mt-8 lg:-mt-12 -mx-8 lg:-mx-12 mb-10">
        <x-organisms.header>
            <x-slot:search>
                <div class="relative w-full z-50">
                    <x-molecules.search-input model="search" />

                    @if(strlen($search) >= 2)
                        <div
                            class="absolute top-full left-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden min-w-[300px]">
                            @forelse($this->searchResults as $result)
                                <a href="{{ route('admin.konsultasi.detail', $result->id) }}" wire:navigate
                                    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors">
                                    <div class="font-medium text-gray-900">{{ $result->siswa->nama ?? 'Anonim' }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $result->jenis_layanan }} &bull;
                                        {{ \Carbon\Carbon::parse($result->tanggal_konsultasi)->format('d M Y') }}
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-3 text-sm text-gray-500">
                                    Tidak ada data untuk "{{ $search }}".
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
                </x-slot>
        </x-organisms.header>
    </div>

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start justify-between mb-12 gap-4">
        <div class="flex gap-4">
            <!-- Back Button -->
            <button wire:click="goBack" class="mt-1 text-gray-400 hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </button>

            <!-- Student Info (Dinamis) -->
            <div>
                <!-- Mengambil nama siswa dari relasi, tampilkan 'Anonim' jika kosong -->
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $record->siswa->nama ?? 'Anonim' }}</h1>
                <!-- Sesuaikan field nis dan kelas dengan kolom di tabel siswa kamu -->
                <p class="text-sm text-gray-500 mt-1">NIS {{ $record->siswa->nis ?? '-' }}</p>
                <p class="text-sm text-gray-500">Kelas {{ $record->siswa->kelas_label }} -
                    {{ $record->siswa->jurusan_label }}
                </p>
            </div>
        </div>

        <!-- Action Icons -->
        <div class="flex items-center gap-4 text-gray-400">
            <button wire:click="delete" wire:confirm="Yakin ingin menghapus data konsultasi ini?"
                class="hover:text-red-500 transition-colors" title="Hapus">
                <!-- Icon Delete -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>
            <button wire:click="edit" class="hover:text-brand-teal transition-colors" title="Edit">
                <!-- Icon Edit -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.112l-3.154 1.054a.75.75 0 01-.94-.94l1.054-3.154a4.5 4.5 0 011.112-1.89l13.416-13.416z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125L16.875 4.5" />
                </svg>
            </button>
            <div x-data="{ showExportMenu: false }" class="relative">
                <button @click="showExportMenu = !showExportMenu" class="hover:text-gray-700 transition-colors" title="Cetak">
                    <!-- Icon Print -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                    </svg>
                </button>
                <!-- Export Dropdown -->
                <div x-show="showExportMenu" @click.away="showExportMenu = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                    style="display: none;">
                    <div class="px-3 py-2 border-b border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Export ke Word</p>
                    </div>
                    <a href="{{ route('konsultasi.export', ['id' => $record->id, 'template' => 'form-penanganan-siswa']) }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        Form Penanganan Siswa
                    </a>
                    <a href="{{ route('konsultasi.export', ['id' => $record->id, 'template' => 'komulatif-record']) }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        Komulatif Record
                    </a>
                    <a href="{{ route('konsultasi.export', ['id' => $record->id, 'template' => 'lembar-sosiometri']) }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        Lembar Sosiometri
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        <!-- Left Column: Main Details -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Jenis Layanan -->
            <div>
                <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">Jenis Layanan</h3>
                <p class="text-sm font-bold text-gray-900">{{ $record->jenis_layanan }}</p>
            </div>

            <!-- Tanggal Pelaksanaan -->
            <div>
                <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">Tanggal Pelaksanaan</h3>
                <!-- Memformat tanggal menggunakan Carbon (Contoh: Kamis, 24 Maret 2026) -->
                <p class="text-sm text-gray-600">
                    {{ \Carbon\Carbon::parse($record->tanggal_konsultasi)->locale('id')->translatedFormat('l, d F Y') }}
                </p>
            </div>

            <!-- Deskripsi Masalah -->
            <div>
                <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">Deskripsi Masalah</h3>
                <p class="text-sm text-gray-600 leading-relaxed text-justify">
                    {{ $record->isi_konsultasi }}
                </p>
            </div>

            <!-- Hasil Layanan -->
            <div>
                <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">Hasil Layanan</h3>
                <p class="text-sm text-gray-600 leading-relaxed text-justify">
                    <!-- Gunakan Null Coalescing (??) jika data hasil_tindak_lanjut bisa jadi kosong -->
                    {{ $record->hasil_tindak_lanjut ?? 'Belum ada catatan hasil layanan.' }}
                </p>
            </div>

            <!-- Tindak Lanjut -->
            <div>
                <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">Tindak Lanjut</h3>
                <p class="text-sm text-gray-600 leading-relaxed text-justify">
                    {{ $record->hasil_tindak_lanjut ?? 'Belum ada catatan tindak lanjut.' }}
                </p>
            </div>
        </div>

        <!-- Right Column: Attachments & Counselor Info -->
        <div class="space-y-10">
            <!-- Lampiran (Opsional, jika sistem kamu punya fitur upload) -->
            <div>
                <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">Lampiran</h3>
                @if($record->lampirans && $record->lampirans->isNotEmpty())
                    <div class="flex flex-wrap gap-4">
                        @foreach($record->lampirans as $lampiran)
                            @php
                                $ext = strtolower(pathinfo($lampiran->nama_file, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
                                $fileUrl = asset('storage/' . $lampiran->path_file);
                            @endphp
                            <a href="{{ $fileUrl }}" target="_blank"
                                class="w-28 h-24 bg-gray-50 border border-gray-200 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:bg-gray-100 hover:border-brand-teal transition-all p-2 text-center">
                                @if($isImage)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-purple-500 mb-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-orange-500 mb-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                @endif
                                <span class="text-[10px] text-gray-500 font-medium truncate w-full" title="{{ $lampiran->nama_file }}">
                                    {{ $lampiran->nama_file }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-400">Tidak ada lampiran.</p>
                @endif
            </div>

            <!-- Konselor -->
            <div>
                <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">Konselor</h3>
                <p class="text-lg font-bold text-gray-900 uppercase tracking-wide">
                    {{ $record->gurubk->user->nama ?? '-' }}
                </p>
                <p class="text-[11px] text-gray-500 mt-1">Dicatat oleh sistem</p>
            </div>
        </div>
    </div>

    <div class="px-4 py-2">
        <x-shared.flash-message />
    </div>

    <livewire:partials.konsultasi.konsultasi-modal />
</div>