<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\KasusBkService;

new #[Layout('layouts.app')] class extends Component {

    // ── State untuk Pencarian & Filter ──────────
    public string $search = '';
    public string $filterStatus = '';
    public string $filterPrioritas = '';
    public string $filterKelas = '';
    public string $filterJurusan = '';
    public string $filterJenisKelamin = '';
    public bool $showFilters = false;

    // ── State untuk Fitur Select All ────────────
    public array $selected = [];
    public bool $selectAll = false;

    public function with(): array
    {
        $service = app(KasusBkService::class);

        // Ambil semua data
        $all = $service->all();

        // Opsi filter yang tersedia
        $statusOptions = $all->pluck('status')->filter()->unique()->sort()->values()->toArray();
        $prioritasOptions = $all->pluck('prioritas')->filter()->unique()->sort()->values()->toArray();
        $kelasOptions = $all->pluck('siswa.kelas_label')->filter()->unique()->sort()->values()->toArray();
        $jurusanOptions = $all->pluck('siswa.jurusan_label')->filter()
            ->unique()->map(fn($j) => (string) $j)->sort()->values()->toArray();
        $jenisKelaminOptions = $all->pluck('siswa.jenis_kelamin')->filter()->unique()->values()->toArray();

        // Mulai dari keseluruhan data, lalu terapkan filter
        $data = $all;

        // 1. Filter Pencarian (case-insensitive)
        if ($this->search) {
            $needle = (string) $this->search;
            $data = $data->filter(function ($item) use ($needle) {
                $name = (string) ($item->siswa->nama ?? 'Anonim');
                $judul = (string) ($item->judul ?? '');
                $desc = (string) ($item->deksripsi ?? '');

                return (mb_stripos($name, $needle) !== false)
                    || (mb_stripos($judul, $needle) !== false)
                    || (mb_stripos($desc, $needle) !== false);
            });
        }

        // 2. Filter Status
        if ($this->filterStatus) {
            $data = $data->filter(function ($item) {
                return $item->status === $this->filterStatus;
            });
        }

        // 3. Filter Prioritas
        if ($this->filterPrioritas) {
            $data = $data->filter(function ($item) {
                return $item->prioritas === $this->filterPrioritas;
            });
        }

        // 4. Filter berdasarkan Kelas
        if ($this->filterKelas !== '') {
            $data = $data->filter(fn($item) => (string) ($item->siswa->kelas_label ?? '') === (string) $this->filterKelas);
        }

        // 5. Filter berdasarkan Jurusan
        if ($this->filterJurusan !== '') {
            $data = $data->filter(fn($item) => strcasecmp(($item->siswa->jurusan_label ?? ''), $this->filterJurusan) === 0);
        }

        // 6. Filter berdasarkan Jenis Kelamin
        if ($this->filterJenisKelamin !== '') {
            $data = $data->filter(fn($item) => strcasecmp(($item->siswa->jenis_kelamin ?? ''), $this->filterJenisKelamin) === 0);
        }

        return [
            'records' => $data->values(),
            'statusOptions' => $statusOptions,
            'prioritasOptions' => $prioritasOptions,
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
        $this->dispatch('create-konsultasi');
    }

    public function edit($id)
    {
        $this->dispatch('edit-kasus-bk', id: $id);
    }

    public function delete($id)
    {
        $service = app(KasusBkService::class);
        $service->delete($id);

        session()->flash('success', 'Kasus BK berhasil dihapus!');
        $this->selected = array_diff($this->selected, [(string) $id]);
    }

    public function goToDetail($id)
    {
        $this->redirectRoute('admin.kasus-bk.detail', ['id' => $id], navigate: true);
    }

    public function filterAction()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterPrioritas = '';
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
            Kasus BK (Konsultasi)
    </x-organisms.header>

    {{-- Toolbar --}}
    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="$refresh">
        <x-slot:pagination>
            {{ count($records) }} data
            </x-slot>
    </x-organisms.table-toolbar>

    {{-- Baris Filter Lanjutan --}}
    @if($showFilters)
        <div
            class="px-6 sm:px-8 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-4 text-sm text-gray-600 shrink-0 transition-all">
            <span class="text-gray-500 text-xs font-medium">Filter Data:</span>

            {{-- Filter Status --}}
            <select wire:model.live="filterStatus"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Status</option>
                @foreach($statusOptions ?? [] as $k)
                    <option value="{{ $k }}">{{ $k }}</option>
                @endforeach
            </select>

            {{-- Filter Prioritas --}}
            <select wire:model.live="filterPrioritas"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Prioritas</option>
                @foreach($prioritasOptions ?? [] as $k)
                    <option value="{{ $k }}">{{ $k }}</option>
                @endforeach
            </select>

            {{-- Filter Kelas --}}
            <select wire:model.live="filterKelas"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Kelas</option>
                @foreach($kelasOptions ?? [] as $k)
                    <option value="{{ $k }}">Kelas {{ $k }}</option>
                @endforeach
            </select>

            {{-- Filter Jurusan --}}
            <select wire:model.live="filterJurusan"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Jurusan</option>
                @foreach($jurusanOptions ?? [] as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                @endforeach
            </select>

            {{-- Filter Jenis Kelamin --}}
            <select wire:model.live="filterJenisKelamin"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>

            {{-- Tombol Reset --}}
            @if($search !== '' || $filterStatus !== '' || $filterPrioritas !== '' || $filterKelas !== '' || $filterJurusan !== '' || $filterJenisKelamin !== '')
                <button wire:click="resetFilters"
                    class="text-xs text-brand-teal font-medium hover:text-teal-700 hover:underline transition-colors ml-auto">
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

    <x-organisms.data-table empty="Belum ada data kasus BK.">
        @foreach($records as $record)
            <tr wire:key="konsultasi-{{ $record->id }}" wire:click="goToDetail({{ $record->id }})"
                class="group border-b border-gray-100 transition-all duration-200 h-12 relative cursor-pointer hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] hover:z-10 hover:rounded-md {{ in_array($record->id, $selected) ? 'bg-teal-50/50' : 'bg-white' }}">

                <td class="w-16 text-center align-middle rounded-l-md py-2" onclick="event.stopPropagation()">
                    <input type="checkbox" value="{{ $record->id }}" wire:model.live="selected"
                        class="w-4 h-4 rounded border-gray-300 text-brand-teal focus:ring-brand-teal accent-brand-teal cursor-pointer">
                </td>

                <td class="px-4 py-2 w-1/6 font-semibold text-gray-900 align-middle">
                    <span class="font-semibold text-gray-900 transition-colors duration-200 group-hover:text-blue-600">
                        {{ $record->siswa->nama ?? 'Anonim' }}
                    </span>
                </td>

                <td class="px-4 py-2 w-1/6 font-semibold text-gray-800 align-middle text-xs">
                    {{ $record->judul }}
                </td>

                <td class="px-4 py-2 align-middle text-xs">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ match($record->status)
                            case 'Open' => 'bg-green-100 text-green-700',
                            case 'Pending' => 'bg-yellow-100 text-yellow-700',
                            case 'Closed' => 'bg-blue-100 text-blue-700',
                            default => 'bg-gray-100 text-gray-700'
                        }}">
                        {{ $record->status }}
                    </span>
                </td>

                <td class="px-4 py-2 align-middle text-xs">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ match($record->prioritas)
                            case 'Tinggi' => 'bg-red-100 text-red-700',
                            case 'Sedang' => 'bg-yellow-100 text-yellow-700',
                            case 'Rendah' => 'bg-green-100 text-green-700',
                            default => 'bg-gray-100 text-gray-700'
                        }}">
                        {{ $record->prioritas }}
                    </span>
                </td>

                <td class="px-4 py-2 text-gray-500 max-w-xs align-middle text-xs">
                    <div class="truncate w-full" title="{{ $record->deksripsi }}">{{ $record->deksripsi }}
                    </div>
                </td>

                <td class="px-4 py-2 w-40 text-right align-middle relative rounded-r-md">
                    <span class="group-hover:opacity-0 font-medium text-gray-900 pr-2 transition-opacity text-xs">
                        {{ \Carbon\Carbon::parse($record->tanggal_mulai)->format('d M y') }}
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