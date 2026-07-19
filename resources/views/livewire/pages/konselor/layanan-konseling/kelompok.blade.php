<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Services\BimbinganKelompokService;

new #[Layout('layouts.app')] class extends Component {

    // ── State untuk Pencarian & Filter ──────────
    public string $search = '';
    public string $filterKelas = '';
    public string $filterJurusan = '';
    public string $filterJenisKelamin = '';
    public bool $showFilters = false;

    // ── State untuk Fitur Select All ────────────
    public array $selected = [];
    public bool $selectAll = false;

    public function with(): array
    {
        $service = app(BimbinganKelompokService::class);
        $all = $service->getAll();

        // Opsi filter dari data
        $kelasOptions = $all->pluck('siswa')
            ->flatten()
            ->pluck('siswa.kelas_label')
            ->filter()->unique()->sort()->values()->toArray();
        $jurusanOptions = $all->pluck('siswa')
            ->flatten()
            ->pluck('siswa.jurusan_label')
            ->filter()->unique()->sort()->values()->toArray();
        $jenisKelaminOptions = $all->pluck('siswa')
            ->flatten()
            ->pluck('siswa.jenis_kelamin')
            ->filter()->unique()->values()->toArray();

        $data = $all;

        // 1. Filter Pencarian
        if ($this->search) {
            $needle = (string) $this->search;
            $data = $data->filter(function ($item) use ($needle) {
                return mb_stripos($item->uraian_masalah ?? '', $needle) !== false
                    || mb_stripos($item->guruBk?->user?->nama ?? '', $needle) !== false;
            });
        }

        // 2. Filter Kelas (cek apakah ada siswa dari kelas tersebut)
        if ($this->filterKelas !== '') {
            $data = $data->filter(function ($item) {
                return $item->siswa->contains(function ($peserta) {
                    return ($peserta->siswa->kelas_label ?? '') === $this->filterKelas;
                });
            });
        }

        // 3. Filter Jurusan
        if ($this->filterJurusan !== '') {
            $data = $data->filter(function ($item) {
                return $item->siswa->contains(function ($peserta) {
                    return strcasecmp(($peserta->siswa->jurusan_label ?? ''), $this->filterJurusan) === 0;
                });
            });
        }

        // 4. Filter Jenis Kelamin
        if ($this->filterJenisKelamin !== '') {
            $data = $data->filter(function ($item) {
                return $item->siswa->contains(function ($peserta) {
                    return strcasecmp(($peserta->siswa->jenis_kelamin ?? ''), $this->filterJenisKelamin) === 0;
                });
            });
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

    public function create()
    {
        $this->dispatch('create-bimbingan-kelompok');
    }

    #[On('refreshTable')]
    public function refreshTable($id = null)
    {
        // Computed akan auto-refresh
    }

    public function edit($id)
    {
        $this->dispatch('edit-bimbingan-kelompok', id: $id);
    }

    public function delete($id)
    {
        app(BimbinganKelompokService::class)->delete($id);
        $this->selected = array_diff($this->selected, [(string) $id]);
        session()->flash('success', 'Layanan konseling kelompok berhasil dihapus!');
    }

    public function goToDetail($id)
    {
        $this->redirectRoute('konselor.layanan-konseling.kelompok.detail', ['id' => $id], navigate: true);
    }
}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-white h-full" x-data="{ loading: false }"
    x-on:click="if ($event.target.closest('button[wire\\:click^=\'edit\'], button[wire\\:click=\'create\']')) loading = true"
    x-on:open-modal.window="loading = false" x-on:close-modal.window="loading = false">

    {{-- Header --}}
    <x-organisms.header action="create">
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>
        Layanan Konseling Kelompok
    </x-organisms.header>

    {{-- Toolbar --}}
    <x-organisms.table-toolbar onFilter="filterAction" onRefresh="$refresh">
        <x-slot:pagination>
            {{ count($records) }} data
        </x-slot:pagination>
    </x-organisms.table-toolbar>

    {{-- Advanced Filters --}}
    @if($showFilters)
        <div class="px-6 sm:px-8 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-4 text-sm text-gray-600 shrink-0 transition-all">
            <span class="text-gray-500 text-xs font-medium">Filter Data:</span>

            <select wire:model.live="filterKelas"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Kelas</option>
                @foreach($kelasOptions ?? [] as $k)
                    <option value="{{ $k }}">Kelas {{ $k }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterJurusan"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua Jurusan</option>
                @foreach($jurusanOptions ?? [] as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterJenisKelamin"
                class="text-xs border border-gray-200 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-brand-teal w-28 sm:w-36 pr-6 flex-shrink-0 bg-white cursor-pointer">
                <option value="">Semua</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>

            @if($search !== '' || $filterKelas !== '' || $filterJurusan !== '' || $filterJenisKelamin !== '')
                <button wire:click="resetFilters"
                    class="text-xs text-brand-teal font-medium hover:text-teal-700 hover:underline transition-colors ml-auto">
                    Reset Semua
                </button>
            @endif
        </div>
    @endif

    {{-- Selected indicator --}}
    @if(count($selected) > 0)
        <div class="px-6 py-2 bg-teal-50 border-b border-teal-100 flex justify-between items-center text-sm">
            <span class="text-xs font-medium text-brand-teal">{{ count($selected) }} data dipilih</span>
            <button wire:click="$set('selected', [])" class="text-xs text-gray-500 hover:text-gray-700">Batal Pilih</button>
        </div>
    @endif

    {{-- Flash Message --}}
    <div class="px-4 py-2">
        <x-shared.flash-message />
    </div>

    {{-- Data Table --}}
    <x-organisms.data-table
        :headers="['', 'Tanggal', 'Uraian Masalah', 'Peserta', 'Guru BK', 'Aksi']"
        empty="Belum ada data layanan konseling kelompok.">
        @forelse($records as $record)
            <tr wire:key="bk-{{ $record->id }}" wire:click="goToDetail({{ $record->id }})"
                class="group border-b border-gray-100 transition-all duration-200 h-12 relative cursor-pointer hover:shadow-[0_2px_10px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] hover:z-10 hover:rounded-md {{ in_array($record->id, $selected) ? 'bg-teal-50/50' : 'bg-white' }}">

                <td class="w-16 text-center align-middle rounded-l-md py-2" onclick="event.stopPropagation()">
                    <input type="checkbox" value="{{ $record->id }}" wire:model.live="selected"
                        class="w-4 h-4 rounded border-gray-300 text-brand-teal focus:ring-brand-teal accent-brand-teal cursor-pointer">
                </td>

                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap align-middle">
                    {{ \Carbon\Carbon::parse($record->tanggal_layanan)->isoFormat('D MMM Y') }}
                </td>

                <td class="px-4 py-2 font-semibold max-w-xs truncate align-middle">
                    <a href="{{ route('konselor.layanan-konseling.kelompok.detail', $record->id) }}" wire:navigate
                        class="text-gray-900 transition-colors duration-200 hover:text-blue-600">
                        {{ $record->uraian_masalah }}
                    </a>
                </td>

                <td class="px-4 py-2 text-sm text-gray-600 align-middle">
                    @php $pesertaCount = $record->siswa->count(); @endphp
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        {{ $pesertaCount }} peserta
                    </span>
                </td>

                <td class="px-4 py-2 text-sm text-gray-600 align-middle">
                    {{ $record->guruBk?->user?->nama ?? '-' }}
                </td>

                <td class="px-4 py-2 text-right align-middle relative rounded-r-md">
                    <span class="group-hover:opacity-0 font-medium text-gray-900 pr-2 transition-opacity text-xs">
                        {{ \Carbon\Carbon::parse($record->tanggal_layanan)->format('d M y') }}
                    </span>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <div class="flex items-center gap-1" onclick="event.stopPropagation()">
                            <x-atoms.action-button color="blue" title="Edit" wire:click="edit({{ $record->id }})">
                                <x-atoms.icon variant="edit" size="sm" />
                            </x-atoms.action-button>
                            <x-atoms.action-button color="red" title="Hapus" wire:click="delete({{ $record->id }})"
                                wire:confirm="Yakin ingin menghapus layanan konseling kelompok ini?">
                                <x-atoms.icon variant="delete" size="sm" />
                            </x-atoms.action-button>
                        </div>
                    </div>
                </td>

            </tr>
        @empty
        @endforelse
    </x-organisms.data-table>

    <livewire:partials.layanan-konseling.layanan-konseling-kelompok-modal />

</div>
