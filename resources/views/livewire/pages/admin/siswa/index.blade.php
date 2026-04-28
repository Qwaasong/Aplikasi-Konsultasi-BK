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
    public string $search       = '';
    public string $filterKelas  = '';
    public string $filterJurusan = '';
    public string $filterJenisKelamin = '';

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
            'search'       => $this->search,
            'kelas'        => $this->filterKelas ?: null,
            'jurusan'      => $this->filterJurusan ?: null,
            'jenis_kelamin' => $this->filterJenisKelamin ?: null,
            'per_page'     => 15,
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

    public function exportCsv(SiswaService $service): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filters = [
            'search'       => $this->search,
            'kelas'        => $this->filterKelas ?: null,
            'jurusan'      => $this->filterJurusan ?: null,
            'jenis_kelamin' => $this->filterJenisKelamin ?: null,
        ];

        $csv      = $service->exportToCsv($filters);
        $filename = 'data-siswa-' . now()->format('Ymd-His') . '.csv';

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

    // ─────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────

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
                wire:click="exportCsv"
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
                class="text-xs border border-gray-200 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-brand-teal">
                <option value="">Semua Kelas</option>
                @foreach($filterOptions['kelas'] as $k)
                    <option value="{{ $k }}">Kelas {{ $k }}</option>
                @endforeach
            </select>

            {{-- Jurusan --}}
            <select wire:model.live="filterJurusan"
                class="text-xs border border-gray-200 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-brand-teal">
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
    <x-organisms.data-table empty="Belum ada data siswa.">
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

    {{-- Pagination --}}
    <div class="px-6 py-3 border-t border-gray-100 shrink-0">
        {{ $records->links() }}
    </div>


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

</div>