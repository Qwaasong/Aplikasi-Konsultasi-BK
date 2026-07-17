<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\Konsultasi;
use App\Services\HomeVisitService;

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
            ->where('jenis_layanan', 'Kunjungan Rumah')
            ->where(function ($query) {
                $query->whereHas('siswa.user', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                })
                ->orWhere('judul', 'like', '%' . $this->search . '%');
            })
            ->take(5)
            ->get();
    }

    public function mount($id)
    {
        $service = app(HomeVisitService::class);

        $this->record = $service->findById($id);
    }

    public function goBack()
    {
        return redirect()->route('konselor.kunjungan-rumah.index');
    }

    public function edit()
    {
        $this->dispatch('edit-home-visit', id: $this->record->id);
    }

    public function delete()
    {
        app(HomeVisitService::class)->delete($this->record->id);

        session()->flash('success','Data kunjungan rumah berhasil dihapus.');

        return redirect()->route('konselor.kunjungan-rumah.index');
    }
};

?>

<div class="flex-1 flex flex-col min-w-0 bg-white min-h-screen p-8 lg:p-12">

    {{-- Header --}}
    <div class="-mt-8 lg:-mt-12 -mx-8 lg:-mx-12 mb-10">

        <x-organisms.header>

            <x-slot:search>

                <div class="relative w-full z-50">

                    <x-molecules.search-input model="search"/>

                    @if(strlen($search) >= 2)

                        <div
                            class="absolute top-full left-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden min-w-[320px]">

                            @forelse($this->searchResults as $result)

                                <a
                                    href="{{ route('konselor.home-visit.detail',$result->id) }}"
                                    wire:navigate
                                    class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">

                                    <div class="font-medium text-gray-900">

                                        {{ $result->siswa->nama ?? '-' }}

                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">

                                        {{ $result->judul }}

                                        •

                                        {{ optional($result->tanggal_konsultasi)->format('d M Y') }}

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


    {{-- Header Detail --}}

    <div class="flex flex-col sm:flex-row justify-between gap-4 mb-12">

        <div class="flex gap-4">

            <button
                wire:click="goBack"
                class="mt-1 text-gray-400 hover:text-gray-700">

                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                    class="w-6 h-6">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>

                </svg>

            </button>

            <div>

                <h1 class="text-2xl font-bold text-gray-900">

                    {{ $record->siswa->nama ?? '-' }}

                </h1>

                <p class="text-sm text-gray-500 mt-1">

                    NIS {{ $record->siswa->nis ?? '-' }}

                </p>

                <p class="text-sm text-gray-500">

                    Kelas

                    {{ $record->siswa->kelas_label }}

                    -

                    {{ $record->siswa->jurusan_label }}

                </p>

            </div>

        </div>


        <div class="flex items-center gap-4 text-gray-400">

            {{-- Delete --}}

            <button
                wire:click="delete"
                wire:confirm="Yakin ingin menghapus data ini?"
                class="p-2 rounded-lg hover:bg-red-50 hover:text-red-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>

            </button>

            {{-- Edit --}}

            <button
                wire:click="edit"
                 class="p-2 rounded-lg hover:bg-teal-50 hover:text-brand-teal transition">

                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.112l-3.154 1.054a.75.75 0 01-.94-.94l1.054-3.154a4.5 4.5 0 011.112-1.89l13.416-13.416z"/>

                </svg>

            </button>

        </div>

    </div>


    {{-- Content --}}

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        {{-- Kolom kiri dimulai di bagian 2 --}}
        <div class="lg:col-span-2 space-y-10">

    {{-- Judul --}}
    <div>
        <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">
            Judul Kunjungan Rumah
        </h3>

        <p class="text-lg font-bold text-gray-900">
            {{ $record->judul }}
        </p>
    </div>


    {{-- Tanggal Kunjungan --}}
    <div>

        <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">
            Tanggal Kunjungan
        </h3>

        <p class="text-sm text-gray-600">

            {{ optional($record->tanggal_konsultasi)->locale('id')->translatedFormat('l, d F Y') }}

        </p>

    </div>


    {{-- Hasil Kunjungan --}}
    <div>

        <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">
            Hasil Kunjungan
        </h3>

        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">

            <p class="text-sm leading-7 text-gray-700 whitespace-pre-line text-justify">

                {{ $record->isi_konsultasi ?: 'Belum ada hasil kunjungan.' }}

            </p>

        </div>

    </div>


    {{-- Hasil Tindak Lanjut --}}
    <div>

        <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">
            Hasil Tindak Lanjut
        </h3>

        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">

            <p class="text-sm leading-7 text-gray-700 whitespace-pre-line text-justify">

                {{ $record->hasil_tindak_lanjut ?: 'Belum ada hasil tindak lanjut.' }}

            </p>

        </div>

    </div>


    {{-- Ringkasan --}}
    <div>

        <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">
            Ringkasan Informasi
        </h3>

        <div class="grid md:grid-cols-2 gap-5">

            <div class="border border-gray-200 rounded-xl p-5">

                <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-2">
                    Jenis Layanan
                </p>

                <p class="font-semibold text-gray-900">
                    {{ $record->jenis_layanan }}
                </p>

            </div>

            <div class="border border-gray-200 rounded-xl p-5">

                <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-2">
                    Dicatat Pada
                </p>

                <p class="font-semibold text-gray-900">

                    {{ optional($record->created_at)->translatedFormat('d F Y H:i') }}

                </p>

            </div>

        </div>

    </div>

</div>


{{-- Sidebar --}}

<div class="space-y-8">
        {{-- Status --}}
    <div>

        <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">
            Status
        </h3>

        <span
            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold

            @if($record->status == 'Open')
                bg-blue-100 text-blue-700
            @elseif($record->status == 'Diproses')
                bg-yellow-100 text-yellow-700
            @else
                bg-green-100 text-green-700
            @endif
        ">
            {{ $record->status }}
        </span>

    </div>


    {{-- Prioritas --}}
    <div>

        <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">
            Prioritas
        </h3>

        <span
            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold

            @if($record->prioritas == 'Tinggi')
                bg-red-100 text-red-700

            @elseif($record->prioritas == 'Sedang')
                bg-yellow-100 text-yellow-700

            @else
                bg-green-100 text-green-700
            @endif

        ">
            {{ $record->prioritas }}
        </span>

    </div>


    {{-- Guru BK --}}
    <div>

        <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">
            Guru BK
        </h3>

        <div class="border border-gray-200 rounded-xl p-5">

            <p class="text-lg font-bold text-gray-900">

                {{ $record->gurubk->user->nama ?? '-' }}

            </p>

            <p class="text-xs text-gray-500 mt-1">

                Guru Bimbingan dan Konseling

            </p>

        </div>

    </div>


    {{-- Lampiran --}}
    <div>

        <h3 class="text-[11px] font-bold text-gray-800 uppercase tracking-wider mb-3">

            Lampiran

        </h3>

        @if($record->lampirans && $record->lampirans->count())

            <div class="space-y-3">

                @foreach($record->lampirans as $lampiran)

                    @php

                        $url = asset('storage/'.$lampiran->path_file);

                        $ext = strtolower(pathinfo($lampiran->nama_file, PATHINFO_EXTENSION));

                        $image = in_array($ext,['jpg','jpeg','png']);

                    @endphp

                    <a
                        href="{{ $url }}"
                        target="_blank"
                        class="flex items-center gap-4 border border-gray-200 rounded-xl p-4 hover:border-primary hover:bg-gray-50 transition">

                        <div
                            class="w-12 h-12 rounded-xl flex items-center justify-center

                            {{ $image
                                ? 'bg-purple-100 text-purple-600'
                                : 'bg-orange-100 text-orange-600' }}">

                            @if($image)

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    class="w-6 h-6">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159"/>

                                </svg>

                            @else

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                    class="w-6 h-6">

                                    <path d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875A1.875 1.875 0 0112.75 7.125V5.25A3.75 3.75 0 009 1.5H5.625z"/>

                                </svg>

                            @endif

                        </div>

                        <div class="overflow-hidden">

                            <p class="font-semibold text-sm text-gray-800 truncate">

                                {{ $lampiran->nama_file }}

                            </p>

                            <p class="text-xs text-gray-500">

                                Klik untuk membuka file

                            </p>

                        </div>

                    </a>

                @endforeach

            </div>

        @else

            <div class="border border-dashed border-gray-300 rounded-xl p-8 text-center">

                <p class="text-sm text-gray-400">

                    Tidak ada lampiran.

                </p>

            </div>

        @endif

    </div>

</div>

</div>

<div class="px-4 py-4">

    <x-shared.flash-message />

</div>

<livewire:partials.home-visit.home-visit-modal />

</div>