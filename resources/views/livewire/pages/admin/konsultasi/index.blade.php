<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\KonsultasiService;

new #[Layout('layouts.app')] class extends Component {

    // ── State untuk Pencarian & Filter ──────────
    public string $search = '';
    public string $filterJenisLayanan = '';
    public string $filterFormat = ''; // Tambahan filter: individu, klasikal, kelompok
    public string $filterKelas = '';
    public string $filterJurusan = '';
    public string $filterJenisKelamin = '';
    public bool $showFilters = false; // State untuk menyembunyikan/menampilkan baris filter

    // ── State untuk Fitur Select All ────────────
    public array $selected = [];
    public bool $selectAll = false;

    public function with(): array
    {
        $service = app(KonsultasiService::class);

        // Ambil semua data dulu — opsi filter diambil dari keseluruhan data
        $all = $service->getAll();

        // Opsi filter yang tersedia
        $layananOptions = $all->pluck('jenis_layanan')->filter()->unique()->sort()->values()->toArray();
        $kelasOptions = $all->pluck('siswa.kelas')->filter()->unique()->sort()->values()->toArray();
        $jurusanOptions = $all->pluck('siswa.jurusan')->filter()
            ->unique()->map(fn($j) => (string) $j)->sort()->values()->toArray();
        $jenisKelaminOptions = $all->pluck('siswa.jenis_kelamin')->filter()->unique()->values()->toArray();

        // Mulai dari keseluruhan data, lalu terapkan filter secara berurutan
        $data = $all;

        // 1. Filter Pencarian (case-insensitive)
        if ($this->search) {
            $needle = (string) $this->search;
            $data = $data->filter(function ($item) use ($needle) {
                $name  = (string) ($item->siswa->nama ?? 'Anonim');
                $jenis = (string) ($item->jenis_layanan ?? '');
                $desc  = (string) ($item->deskripsi_masalah ?? '');

                return (mb_stripos($name, $needle) !== false)
                    || (mb_stripos($jenis, $needle) !== false)
                    || (mb_stripos($desc, $needle) !== false);
            });
        }

        // 2. Filter Jenis Layanan
        if ($this->filterJenisLayanan) {
            $data = $data->filter(function ($item) {
                return $item->jenis_layanan === $this->filterJenisLayanan;
            });
        }

        // 3. Filter Format Layanan (Individu, Klasikal, Kelompok)
        if ($this->filterFormat) {
            $data = $data->filter(function ($item) {
                return strtolower($item->format_layanan ?? '') === strtolower($this->filterFormat);
            });
        }

        // 4. Filter berdasarkan Kelas (ambil dari relation siswa)
        if ($this->filterKelas !== '') {
            $data = $data->filter(fn($item) => (string) ($item->siswa->kelas ?? '') === (string) $this->filterKelas);
        }

        // 5. Filter berdasarkan Jurusan (normalisasi besar kecil)
        if ($this->filterJurusan !== '') {
            $data = $data->filter(fn($item) => strcasecmp(($item->siswa->jurusan ?? ''), $this->filterJurusan) === 0);
        }

        // 6. Filter berdasarkan Jenis Kelamin
        if ($this->filterJenisKelamin !== '') {
            $data = $data->filter(fn($item) => strcasecmp(($item->siswa->jenis_kelamin ?? ''), $this->filterJenisKelamin) === 0);
        }

        return [
            'records' => $data->values(),
            'layananOptions' => $layananOptions,
            'kelasOptions' => $kelasOptions,
            'jurusanOptions' => $jurusanOptions,
            'jenisKelaminOptions' => $jenisKelaminOptions,
        ];
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $records = $this->with()['records'];
            $this->selected = $records->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        $recordsCount = $this->with()['records']->count();
        $this->selectAll = (count($this->selected) === $recordsCount && $recordsCount > 0);
    }

    public function create()
    {
        $this->dispatch('open-modal', 'tambah-konsultasi');
    }

    public function edit($id)
    {
        $this->dispatch('edit-konsultasi', id: $id);
    }

    public function delete($id)
    {
        $service = app(KonsultasiService::class);
        $service->delete($id);

        session()->flash('success', 'Konsultasi berhasil dihapus!');
        $this->selected = array_diff($this->selected, [(string)$id]);
    }

    public function goToDetail($id)
    {
        $this->redirectRoute('konsultasi.detail', ['id' => $id], navigate: true);
    }

    // ── Method dipanggil oleh Tombol Filter di Toolbar ──
    public function filterAction()
    {
        // Toggle (buka/tutup) area filter
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterJenisLayanan = '';
        $this->filterFormat = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';
        $this->filterJenisKelamin = '';
        $this->selected = []; 
        $this->selectAll = false;
    }
}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full" x-data="{ loading: false }"
    x-on:click="if ($event.target.closest('button[wire\\:click^=\'edit\'], button[wire\\:click=\'create\']')) loading = true"
    x-on:open-modal.window="loading = false" x-on:close-modal.window="loading = false">

    {{-- Header --}}
    <x-organisms.header action="create">
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot>
        Konsultasi
    </x-organisms.header>

    {{-- Toolbar Bawaan --}}
    {{-- Refresh dan Filter sudah nyambung ke sini --}}
    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="$refresh">
        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot>
    </x-organisms.table-toolbar>

    {{-- Baris Filter Lanjutan (Akan Terbuka Jika Ikon Filter di Toolbar Diklik) --}}
    @if($showFilters)
        <div class="px-6 sm:px-8 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-4 text-sm text-gray-600 shrink-0 transition-all">
            <span class="text-gray-500 text-xs font-medium">Filter Data:</span>
            
            {{-- Filter 1: Format Layanan --}}
            <select wire:model.live="filterFormat"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-36 bg-white cursor-pointer pr-6 flex-shrink-0">
                <option value="">Semua Format</option>
                <option value="Individu">Individu</option>
                <option value="Klasikal">Klasikal</option>
                <option value="Kelompok">Kelompok</option>
            </select>

            {{-- Filter 1b: Jenis Layanan --}}
            <select wire:model.live="filterJenisLayanan"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Layanan</option>
                @foreach($layananOptions ?? [] as $k)
                    <option value="{{ $k }}">{{ $k }}</option>
                @endforeach
            </select>

            {{-- Filter 2: Kelas --}}
            <select wire:model.live="filterKelas"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Kelas</option>
                @foreach($kelasOptions ?? [] as $k)
                    <option value="{{ $k }}">Kelas {{ $k }}</option>
                @endforeach
            </select>

            {{-- Filter 3: Jurusan --}}
            <select wire:model.live="filterJurusan"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Jurusan</option>
                @foreach($jurusanOptions ?? [] as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                @endforeach
            </select>

            {{-- Filter 4: Jenis Kelamin --}}
            <select wire:model.live="filterJenisKelamin"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>

            {{-- Tombol Reset --}}
            @if($search !== '' || $filterJenisLayanan !== '' || $filterFormat !== '' || $filterKelas !== '' || $filterJurusan !== '' || $filterJenisKelamin !== '')
                <button wire:click="resetFilters" class="text-xs text-brand-teal font-medium hover:text-teal-700 hover:underline transition-colors ml-auto">
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

    <x-organisms.data-table empty="Belum ada data konsultasi.">
        @foreach($records as $record)
            <tr wire:key="konsultasi-{{ $record->id }}" wire:click="goToDetail({{ $record->id }})"
                class="group border-b border-gray-100 transition-all duration-200 h-12 relative cursor-pointer hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] hover:z-10 hover:rounded-md {{ in_array($record->id, $selected) ? 'bg-teal-50/50' : 'bg-white' }}">

                <td class="w-16 text-center align-middle rounded-l-md py-2" onclick="event.stopPropagation()">
                    <input type="checkbox" value="{{ $record->id }}" wire:model.live="selected"
                        class="w-4 h-4 rounded border-gray-300 text-brand-teal focus:ring-brand-teal accent-brand-teal cursor-pointer">
                </td>

                <td class="px-4 py-2 w-1/6 font-semibold text-gray-900 align-middle">
                    <a href="{{ route('konsultasi.detail', $record->id) }}" class="group block px-4 py-2 w-full h-full">
                        <span class="font-semibold text-gray-900 transition-colors duration-200 group-hover:text-blue-600">
                            {{ $record->siswa->nama ?? 'Anonim' }}
                        </span>
                    </a>
                </td>
                
                {{-- Menampilkan Badge Format & Jenis Layanan --}}
                <td class="px-4 py-2 w-1/4 font-semibold text-gray-800 align-middle text-xs">
                    {{ $record->jenis_layanan }}
                </td>
                
                <td class="px-4 py-2 text-gray-500 max-w-xs align-middle text-xs">
                    <div class="truncate w-full" title="{{ $record->deskripsi_masalah }}">{{ $record->deskripsi_masalah }}
                    </div>
                </td>

                <td class="px-4 py-2 w-40 text-right align-middle relative rounded-r-md">
                    <span class="group-hover:opacity-0 font-medium text-gray-900 pr-2 transition-opacity text-xs">
                        {{ \Carbon\Carbon::parse($record->tanggal)->format('d M y') }}
                    </span>

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

    <livewire:partials.konsultasi.konsultasi-modal />

    <div x-show="loading" style="display: none;" ...>
        </div>
</div>