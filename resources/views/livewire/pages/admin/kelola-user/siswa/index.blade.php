<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\SiswaService;

new #[Layout('layouts.app')] class extends Component {

    // ── State untuk Pencarian & Filter ──────────
    public string $search = '';
    public string $filterKelas = '';
    public string $filterJurusan = '';
    public string $filterJenisKelamin = '';

    public bool $showFilters = false;

    public array $selected = [];
    public bool $selectAll = false;

    public function with(): array
    {
        $service = app(SiswaService::class);

        $all = $service->getAll();

        $kelasOptions = $all->pluck('kelas_label')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $jurusanOptions = $all->pluck('jurusan_label')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $jenisKelaminOptions = $all->pluck('jenis_kelamin')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $data = $all;

        // Search
        if ($this->search) {

            $needle = strtolower($this->search);

            $data = $data->filter(function ($item) use ($needle) {

                return
                    str_contains(strtolower($item->nama ?? ''), $needle)
                    || str_contains((string)$item->nis, $needle)
                    || str_contains(strtolower($item->kelas_label ?? ''), $needle)
                    || str_contains(strtolower($item->jurusan_label ?? ''), $needle);
            });
        }

        // Filter kelas
        if ($this->filterKelas) {

            $data = $data->where('kelas_label', $this->filterKelas);
        }

        // Filter jurusan
        if ($this->filterJurusan) {

            $data = $data->where('jurusan_label', $this->filterJurusan);
        }

        // Filter jenis kelamin
        if ($this->filterJenisKelamin) {

            $data = $data->where('jenis_kelamin', $this->filterJenisKelamin);
        }

        return [

            'records' => $data->values(),

            'kelasOptions' => $kelasOptions,

            'jurusanOptions' => $jurusanOptions,

            'jenisKelaminOptions' => $jenisKelaminOptions,

        ];
    }

    public function updatedSelectAll($value)
    {
        if ($value) {

            $records = $this->with()['records'];

            $this->selected = $records
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();

        } else {

            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        $recordsCount = $this->with()['records']->count();

        $this->selectAll =
            count($this->selected) === $recordsCount
            && $recordsCount > 0;
    }

    public function create()
    {
        $this->dispatch('create-siswa');
    }

    public function edit($id)
    {
        $this->dispatch('edit-siswa', id: $id);
    }

    public function delete($id)
    {
        $service = app(SiswaService::class);
        $service->delete($id);

        session()->flash('success', 'data berhasil dihapus!');
        $this->selected = array_diff($this->selected, [(string) $id]);
    }

    // ── Method dipanggil oleh Tombol Filter di Toolbar ──
    public function filterAction()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters(): void
    {
        $this->search = '';

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

    <x-organisms.header action="create">
        <x-slot:search>
            <x-molecules.search-input model="search" />
            </x-slot>
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
</div>