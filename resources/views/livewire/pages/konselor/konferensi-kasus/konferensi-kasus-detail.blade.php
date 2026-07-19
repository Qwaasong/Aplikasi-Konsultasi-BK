<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\KonferensiKasus;
use App\Services\KonferensiKasusService;

new #[Layout('layouts.app')] class extends Component {

    public $record;

    public $search = '';

    #[On('refreshTable')]
    public function refreshRecord()
    {
        if ($this->record) {
            $this->record = app(KonferensiKasusService::class)->findById($this->record->id);
        }
    }

    #[Computed]
    public function searchResults()
    {
        if (strlen($this->search) < 2) {
            return [];
        }

        return KonferensiKasus::with('kasus.siswa.user')
            ->where('uraian_masalah', 'like', '%' . $this->search . '%')
            ->take(5)
            ->get();
    }

    public function mount($id)
    {
        $this->record = app(KonferensiKasusService::class)->findById($id);
    }

    public function goBack()
    {
        return redirect()->route('konselor.konferensi-kasus.index');
    }

    public function edit()
    {
        $this->dispatch('edit-konferensi-kasus', id: (int) $this->record->id);
    }

    public function delete()
    {
        app(KonferensiKasusService::class)->delete($this->record->id);
        session()->flash('success', 'Konferensi kasus berhasil dihapus.');
        return redirect()->route('konselor.konferensi-kasus.index');
    }
}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 min-h-screen p-6 lg:p-10">

    {{-- Top Navigation Header Wrapper --}}
    <div class="-mt-6 lg:-mt-10 -mx-6 lg:-mx-10 mb-8">
        <x-organisms.header>
            <x-slot:search>
                <div class="relative w-full max-w-md z-50">
                    <x-molecules.search-input model="search" />

                    @if(strlen($search) >= 2)
                        <div class="absolute top-full left-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden w-full">
                            @forelse($this->searchResults as $result)
                                <a href="{{ route('konselor.konferensi-kasus.detail', $result->id) }}" wire:navigate
                                    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors">
                                    <div class="text-sm font-semibold text-gray-800 truncate">{{ $result->uraian_masalah }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $result->kasus?->siswa?->nama ?? '-' }} &middot; {{ \Carbon\Carbon::parse($result->tanggal_konferensi)->format('d M Y') }}</div>
                                </a>
                            @empty
                                <div class="px-4 py-3 text-sm text-gray-500">Tidak ada data ditemukan.</div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </x-slot:search>
        </x-organisms.header>
    </div>

    {{-- Header Detail --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <button wire:click="goBack" class="w-10 h-10 rounded-full flex items-center justify-center border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </button>
            <div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">Konferensi Kasus</span>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">Konferensi Kasus</h1>
            </div>
        </div>

        <div class="flex items-center gap-3 self-end sm:self-auto">
            {{-- Edit --}}
            <button wire:click="edit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-teal-700 bg-teal-50 hover:bg-teal-100 rounded-xl transition border border-teal-100/50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.112l-3.154 1.054a.75.75 0 01-.94-.94l1.054-3.154a4.5 4.5 0 011.112-1.89l13.416-13.416z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125L16.875 4.5" />
                </svg>
                Edit
            </button>
            {{-- Delete --}}
            <button wire:click="delete" wire:confirm="Yakin ingin menghapus konferensi kasus ini?"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition border border-red-100/50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                Hapus
            </button>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Kolom Kiri --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Siswa --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-purple-50 text-purple-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Data Siswa</h3>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-500/10 text-indigo-700 flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($record->kasus?->siswa?->nama ?? 'S', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-base font-bold text-gray-900">{{ $record->kasus?->siswa?->nama ?? '-' }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">
                            NIS {{ $record->kasus?->siswa?->nis ?? '-' }}
                            &middot; Kelas {{ $record->kasus?->siswa?->kelas_label ?? '-' }}
                            &middot; {{ $record->kasus?->siswa?->jurusan_label ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Uraian Masalah --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Uraian Masalah</h3>
                </div>
                <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line text-justify">
                    {{ $record->uraian_masalah ?: 'Tidak ada uraian masalah yang dicantumkan.' }}
                </p>
            </div>

            {{-- Penanganan --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-teal-50 text-teal-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Tindakan Penanganan</h3>
                </div>
                <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line text-justify">
                    {{ $record->penanganan ?: 'Tidak ada catatan penanganan.' }}
                </p>
            </div>

            {{-- Tindak Lanjut --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                    <div class="p-2 rounded-xl bg-sky-50 text-sky-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3-3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Rencana Tindak Lanjut</h3>
                </div>
                <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line text-justify">
                    {{ $record->tindak_lanjut ?: 'Belum ada catatan tindak lanjut.' }}
                </p>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Informasi Layanan --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 text-[15px] border-b border-gray-100 pb-4 mb-4">Informasi Konferensi</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Tanggal</span>
                        <span class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($record->tanggal_konferensi)->locale('id')->translatedFormat('d F Y') }}
                        </span>
                    </div>
                    @if($record->tempat_pertemuan)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Tempat</span>
                        <span class="font-semibold text-gray-800">{{ $record->tempat_pertemuan }}</span>
                    </div>
                    @endif
                    <div class="border-t border-gray-50 pt-4 mt-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-2">Kasus BK Terkait</span>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">{{ $record->kasus?->penanganan ?? '-' }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ match($record->kasus?->status) {
                                        'Open' => 'bg-green-100 text-green-700',
                                        'Pending' => 'bg-yellow-100 text-yellow-700',
                                        'Closed' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    } }}">
                                    {{ $record->kasus?->status ?? '-' }}
                                </span>
                                <span class="text-xs text-gray-500">Prioritas: {{ $record->kasus?->prioritas ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Peserta --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">
                    <h3 class="font-bold text-gray-900 text-[15px]">Peserta Konferensi</h3>
                    <span class="px-2.5 py-0.5 text-xs font-bold bg-indigo-50 text-indigo-700 rounded-full border border-indigo-100">
                        {{ $record->peserta->count() }} Orang
                    </span>
                </div>

                @if($record->peserta->isNotEmpty())
                    <div class="space-y-3 max-h-[360px] overflow-y-auto pr-1" style="scrollbar-width: thin;">
                        @foreach($record->peserta as $peserta)
                            @php
                                $nama = $peserta->nama_peserta ?? 'Peserta';
                                $words = explode(' ', trim($nama));
                                $initials = count($words) >= 2 
                                    ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                    : strtoupper(substr($nama, 0, 2));
                            @endphp
                            <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-indigo-100 hover:bg-indigo-50/10 transition-all">
                                <div class="w-10 h-10 rounded-full bg-indigo-500/10 text-indigo-700 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ $initials }}
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-semibold text-gray-800 truncate" title="{{ $nama }}">
                                        {{ $nama }}
                                    </p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">{{ $peserta->peran_peserta }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="border border-dashed border-gray-200 rounded-xl p-8 text-center bg-gray-50/50">
                        <p class="text-sm text-gray-400 font-medium">Tidak ada peserta yang terdaftar.</p>
                    </div>
                @endif
            </div>

            {{-- Lampiran --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 text-[15px] border-b border-gray-100 pb-4 mb-4">Lampiran Pendukung</h3>

                @if($record->kasus?->lampirans && $record->kasus->lampirans->count())
                    <div class="space-y-3">
                        @foreach($record->kasus->lampirans as $lampiran)
                            @php
                                $url = asset('storage/'.$lampiran->path_file);
                                $ext = strtolower(pathinfo($lampiran->nama_file, PATHINFO_EXTENSION));
                                $image = in_array($ext,['jpg','jpeg','png']);
                            @endphp
                            <a href="{{ $url }}" target="_blank"
                                class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-indigo-100 hover:bg-indigo-50/10 transition-all">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                                    {{ $image ? 'bg-purple-50 text-purple-600' : 'bg-orange-50 text-orange-600' }}">
                                    @if($image)
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="overflow-hidden">
                                    <p class="font-semibold text-sm text-gray-800 truncate" title="{{ $lampiran->nama_file }}">
                                        {{ $lampiran->nama_file }}
                                    </p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">Klik untuk melihat file</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="border border-dashed border-gray-200 rounded-xl p-8 text-center bg-gray-50/50">
                        <p class="text-sm text-gray-400 font-medium">Tidak ada lampiran pendukung.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Flash Message & Modal --}}
    <div class="mt-6">
        <x-shared.flash-message />
    </div>

    <livewire:partials.konferensi-kasus.konferensi-kasus-modal />
</div>
