<?php

use App\Services\SiswaService;
use App\Constants\GlobalMessages;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination, WithFileUploads;

    // ── Filter ──────────────────────────────
    public string $search             = '';
    public string $filterKelas        = '';
    public string $filterJurusan      = '';
    public string $filterJenisKelamin = '';
    public int    $perPage            = 35;

    // ── Form state ──────────────────────────
    public ?int   $editingId    = null;
    public bool   $showForm     = false;

    #[Validate('required|integer|min:1')]
    public string $nis           = '';

    #[Validate('required|string|max:255')]
    public string $nama          = '';

    #[Validate('required|integer|in:10,11,12')]
    public string $kelas         = '';

    #[Validate('required|in:Laki-laki,Perempuan')]
    public string $jenis_kelamin = 'Laki-laki';

    #[Validate('required|string|max:50')]
    public string $jurusan       = '';

    #[Validate('required|string|max:20')]
    public string $periode_ajaran = '';

    // ── Import ──────────────────────────────
    public bool   $showImportModal  = false;
    public        $importFile       = null;
    public array  $importErrors     = [];
    public ?int   $importedCount    = null;

    // ── Export ──────────────────────────────
    public bool   $showExportModal    = false;
    public string $exportKelas        = '';
    public string $exportJurusan      = '';
    public string $exportPeriode      = '';
    public ?int   $exportPreviewCount = null;

    // ── Options untuk dropdown ───────────────
    public array $kelasOptions    = [10, 11, 12];
    public array $jenisKelaminOptions = [
        ['value' => 'Laki-laki', 'label' => 'Laki-laki'],
        ['value' => 'Perempuan', 'label' => 'Perempuan'],
    ];

    // ─────────────────────────────────────────
    // LIFECYCLE
    // ─────────────────────────────────────────

    public function mount(): void
    {
        $this->periode_ajaran = $this->defaultPeriode();
    }

    // ─────────────────────────────────────────
    // DATA UNTUK TEMPLATE
    // ─────────────────────────────────────────

    public function with(): array
    {
        $service = app(SiswaService::class);

        $filters = [
            'search'        => $this->search,
            'kelas'         => $this->filterKelas ?: null,
            'jurusan'       => $this->filterJurusan ?: null,
            'jenis_kelamin' => $this->filterJenisKelamin ?: null,
            'per_page'      => $this->perPage,
        ];

        return [
            'records'       => $service->getPaginated($filters),
            'filterOptions' => $service->getFilterOptions(),
            'stats'         => $service->getStats(),
        ];
    }

    // ─────────────────────────────────────────
    // FORM ACTIONS
    // ─────────────────────────────────────────

    public function create(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm  = true;
    }

    public function edit(int $id): void
    {
        $siswa = app(SiswaService::class)->findById($id);

        $this->editingId       = $id;
        $this->nis             = (string) $siswa->nis;
        $this->nama            = $siswa->nama;
        $this->kelas           = (string) $siswa->kelas;
        $this->jenis_kelamin   = $siswa->jenis_kelamin;
        $this->jurusan         = $siswa->jurusan;
        $this->periode_ajaran  = $siswa->periode_ajaran;

        $this->showForm = true;
    }

    public function save(SiswaService $service): void
    {
        $this->validate();

        $data = [
            'nis'            => (int) $this->nis,
            'nama'           => $this->nama,
            'kelas'          => (int) $this->kelas,
            'jenis_kelamin'  => $this->jenis_kelamin,
            'jurusan'        => strtoupper($this->jurusan),
            'periode_ajaran' => $this->periode_ajaran,
        ];

        try {
            if ($this->editingId) {
                $service->update($this->editingId, $data);
                session()->flash('success', GlobalMessages::SUCCESS_UPDATE);
            } else {
                $service->create($data);
                session()->flash('success', GlobalMessages::SUCCESS_SAVE);
            }

            $this->showForm = false;
            $this->resetForm();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // NIS duplikat → tampilkan error di field nis
            $this->addError('nis', $e->errors()['nis'][0] ?? GlobalMessages::ERROR_VALIDATION);
        }
    }

    public function delete(int $id, SiswaService $service): void
    {
        $service->delete($id);
        session()->flash('success', GlobalMessages::SUCCESS_DELETE);
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    // ─────────────────────────────────────────
    // IMPORT
    // ─────────────────────────────────────────

    public function openImport(): void
    {
        $this->importFile    = null;
        $this->importErrors  = [];
        $this->importedCount = null;
        $this->showImportModal = true;
    }

    public function processImport(SiswaService $service): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,xlsx,xls|max:5120',
        ]);

        try {
            $result = $service->importFromFile($this->importFile);

            $this->importedCount = $result['imported'];
            $this->importErrors  = $result['errors'];

            if ($result['imported'] > 0) {
                session()->flash('success', "Berhasil mengimpor {$result['imported']} data siswa.");
            }

            if (empty($result['errors'])) {
                $this->showImportModal = false;
            }
        } catch (\InvalidArgumentException $e) {
            $this->addError('importFile', $e->getMessage());
        }
    }

    // ─────────────────────────────────────────
    // EXPORT
    // ─────────────────────────────────────────

    /**
     * Buka modal export dan hitung preview jumlah data
     * sesuai filter yang dipilih.
     */
    public function openExport(SiswaService $service): void
    {
        $this->exportKelas        = '';
        $this->exportJurusan      = '';
        $this->exportPeriode      = '';
        $this->exportPreviewCount = $service->getStats()['total'];
        $this->showExportModal    = true;
    }

    /**
     * Dipanggil setiap kali salah satu filter export berubah
     * agar preview count selalu akurat.
     */
    public function updatingExportKelas(string $value): void
    {
        $this->refreshExportPreview($value, $this->exportJurusan, $this->exportPeriode);
    }

    public function updatingExportJurusan(string $value): void
    {
        $this->refreshExportPreview($this->exportKelas, $value, $this->exportPeriode);
    }

    public function updatingExportPeriode(string $value): void
    {
        $this->refreshExportPreview($this->exportKelas, $this->exportJurusan, $value);
    }

    /**
     * Eksekusi download CSV berdasarkan filter di modal export.
     */
    public function exportCsv(SiswaService $service): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filters = [
            'kelas'          => $this->exportKelas   ?: null,
            'jurusan'        => $this->exportJurusan ?: null,
            'periode_ajaran' => $this->exportPeriode ?: null,
        ];

        $csv      = $service->exportToCsv($filters);
        $filename = 'data-siswa-' . now()->format('Ymd-His') . '.csv';

        $this->showExportModal = false;

        return response()->streamDownload(
            fn () => print($csv),
            $filename,
            ['Content-Type' => 'text/csv']
        );
    }

    // ─────────────────────────────────────────
    // FILTER
    // ─────────────────────────────────────────

    public function resetFilters(): void
    {
        $this->search             = '';
        $this->filterKelas        = '';
        $this->filterJurusan      = '';
        $this->filterJenisKelamin = '';
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    // ─────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────

    private function refreshExportPreview(string $kelas, string $jurusan, string $periode): void
    {
        $service = app(SiswaService::class);

        $filters = [
            'kelas'          => $kelas   ?: null,
            'jurusan'        => $jurusan ?: null,
            'periode_ajaran' => $periode ?: null,
            'per_page'       => 99999,
        ];

        $this->exportPreviewCount = $service->getPaginated($filters)->total();
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->nis            = '';
        $this->nama           = '';
        $this->kelas          = '';
        $this->jenis_kelamin  = 'Laki-laki';
        $this->jurusan        = '';
        $this->periode_ajaran = $this->defaultPeriode();
        $this->editingId      = null;
    }

    private function defaultPeriode(): string
    {
        $year = (int) now()->format('Y');
        $month = (int) now()->format('m');
        // Semester genap dimulai Januari; ganjil Juli
        $startYear = $month >= 7 ? $year : $year - 1;
        return $startYear . '/' . ($startYear + 1);
    }
}; ?>

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
                @foreach($filterOptions['kelas'] as $k)
                    <option value="{{ $k }}">Kelas {{ $k }}</option>
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
                class="text-xs border border-gray-200 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-brand-teal">
                <option value="">Semua</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
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

                        {{-- Jenis Kelamin --}}
                        <td class="px-4 py-2 align-middle">
                            <span @class([
                                'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                'bg-blue-100 text-blue-700'   => $siswa->jenis_kelamin === 'Laki-laki',
                                'bg-pink-100 text-pink-700'   => $siswa->jenis_kelamin === 'Perempuan',
                            ])>
                                {{ $siswa->jenis_kelamin }}
                            </span>
                        </td>

                        {{-- Periode Ajaran --}}
                        <td class="px-4 py-2 text-xs text-gray-500 align-middle">
                            {{ $siswa->periode_ajaran }}
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

                    {{-- Kelas + Jenis Kelamin (2 kolom) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-atoms.input-label for="kelas" size="sm">Kelas *</x-atoms.input-label>
                            <x-molecules.input-dropdown
                                id="kelas"
                                wire:model="kelas"
                                size="md"
                                :options="collect($kelasOptions)->map(fn($k) => ['value' => (string)$k, 'label' => 'Kelas ' . $k])->toArray()"
                            />
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

                    {{-- Jurusan + Periode (2 kolom) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-atoms.input-label for="jurusan" size="sm">Jurusan *</x-atoms.input-label>
                            <x-atoms.text-input
                                id="jurusan"
                                type="text"
                                wire:model="jurusan"
                                placeholder="Contoh: RPL"
                                size="md"
                            />
                            @error('jurusan')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-atoms.input-label for="periode_ajaran" size="sm">Periode Ajaran *</x-atoms.input-label>
                            <x-atoms.text-input
                                id="periode_ajaran"
                                type="text"
                                wire:model="periode_ajaran"
                                placeholder="Contoh: 2024/2025"
                                size="md"
                            />
                            @error('periode_ajaran')
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
                        <code class="block">nis | nama | kelas | jenis_kelamin | jurusan | periode_ajaran</code>
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
                            @foreach($filterOptions['kelas'] as $k)
                                <option value="{{ $k }}">Kelas {{ $k }}</option>
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

</div>
