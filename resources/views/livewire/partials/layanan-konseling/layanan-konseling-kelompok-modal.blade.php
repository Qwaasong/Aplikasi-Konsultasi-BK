<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use App\Models\DataSiswa;
use App\Services\SiswaService;
use App\Services\BimbinganKelompokService;
use App\Models\TahunAjaran;

new class extends Component {

    public ?int $editingId = null;
    public int $step = 1;

    // ── DATA BIMBINGAN KELOMPOK ──────────────────────────
    #[Validate('required|date')]
    public $tanggal_layanan = '';

    #[Validate('required|integer')]
    public $tahun_ajaran_id = '';

    // ── PESERTA (MULTIPLE STUDENTS) ──────────────────────
    #[Validate('required|array|min:1')]
    public array $siswa_ids = [];

    public $searchSiswa = '';
    public $showStudentModal = false;

    // ── FILE UPLOAD (STEP 3) ─────────────────────────────
    public $uploadedFiles = [];

    public function mount()
    {
        $this->tanggal_layanan = date('Y-m-d');
        $this->tahun_ajaran_id = TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id')
            ?? '';
    }

    // ── STEP NAVIGATION ──────────────────────────────────
    public function nextStep()
    {
        if ($this->step === 1) {
            // Validate only step-1 fields before advancing
            $this->validate([
                'siswa_ids'        => 'required|array|min:1',
                'tanggal_layanan'  => 'required|date',
                'tahun_ajaran_id'  => 'required|integer',
            ]);

            // Additional manual check: every siswa_id must be numeric
            foreach ($this->siswa_ids as $idx => $id) {
                if (!is_numeric($id)) {
                    $this->addError('siswa_ids', "Peserta ke-" . ($idx + 1) . " tidak valid.");
                    return;
                }
            }
        }

        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    // ── SISWA SELECTION ──────────────────────────────────
    public function selectStudent($id)
    {
        if (!in_array($id, $this->siswa_ids)) {
            $this->siswa_ids[] = (int) $id;
        }
        $this->showStudentModal = false;
        $this->searchSiswa = '';
    }

    public function removeStudent($id)
    {
        $this->siswa_ids = array_values(array_filter($this->siswa_ids, fn($v) => $v !== (int) $id));
    }

    public function openStudentModal()
    {
        $this->showStudentModal = true;
    }

    public function closeStudentModal()
    {
        $this->showStudentModal = false;
    }

    #[Computed]
    public function selectedStudents()
    {
        if (empty($this->siswa_ids)) return collect();
        return DataSiswa::with(['user', 'kelas.jurusan'])
            ->whereIn('id', $this->siswa_ids)
            ->get();
    }

    #[Computed]
    public function filteredStudents()
    {
        return app(SiswaService::class)->search($this->searchSiswa, 50);
    }

    #[Computed]
    public function tahunAjaranLabel()
    {
        if (!$this->tahun_ajaran_id) return '-';
        $ta = TahunAjaran::find($this->tahun_ajaran_id);
        if (!$ta) return '-';
        return $ta->tahun . ' - ' . $ta->semester . ($ta->status_aktif ? ' (Aktif)' : '');
    }

    public function getInitials($name)
    {
        if (!$name) return 'S';
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    // ── CREATE ────────────────────────────────────────────
    #[On('create-bimbingan-kelompok')]
    public function createBimbinganKelompok()
    {
        $this->resetValidation();
        $this->reset([
            'editingId', 'tahun_ajaran_id',
            'siswa_ids', 'uploadedFiles',
        ]);
        $this->step = 1;
        $this->tanggal_layanan = date('Y-m-d');
        $this->dispatch('open-modal', 'form-bimbingan-kelompok');
    }

    // ── EDIT ──────────────────────────────────────────────
    #[On('edit-bimbingan-kelompok')]
    public function loadBimbinganKelompok($id)
    {
        $service = app(BimbinganKelompokService::class);
        $this->resetValidation();

        $record = $service->findById($id);

        $this->editingId = $id;
        $this->tahun_ajaran_id = $record->tahun_ajaran_id;
        $this->tanggal_layanan = \Carbon\Carbon::parse($record->tanggal_layanan)->format('Y-m-d');
        $this->siswa_ids = $record->siswa->pluck('siswa_id')->toArray();
        $this->uploadedFiles = [];
        $this->step = 1;

        $this->dispatch('open-modal', 'form-bimbingan-kelompok');
    }

    // ── SAVE ──────────────────────────────────────────────
    public function save(BimbinganKelompokService $service)
    {
        $this->validate();

        // Validasi manual tiap item siswa_ids harus integer
        foreach ($this->siswa_ids as $idx => $id) {
            if (!is_numeric($id)) {
                $this->addError('siswa_ids', "Peserta ke-" . ($idx + 1) . " tidak valid.");
                return;
            }
        }

        $data = [
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
            'tanggal_layanan' => $this->tanggal_layanan,
        ];

        if ($this->editingId) {
            $service->update($this->editingId, $data, $this->siswa_ids);
            session()->flash('success', 'Layanan Konseling Kelompok berhasil diperbarui!');
        } else {
            $service->create($data, $this->siswa_ids);
            session()->flash('success', 'Layanan Konseling Kelompok berhasil ditambahkan!');
        }

        $this->reset([
            'editingId', 'tahun_ajaran_id',
            'siswa_ids', 'uploadedFiles',
        ]);
        $this->step = 1;
        $this->tanggal_layanan = date('Y-m-d');

        $this->dispatch('close-modal', 'form-bimbingan-kelompok');
        $this->dispatch('refreshTable');
    }
}; ?>

<div>
    <x-shared.modal name="form-bimbingan-kelompok" maxWidth="lg">
    <div class="flex flex-col h-full max-h-[80vh]">

        {{-- HEADER ───────────────────────────────────────--}}
        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">
                {{ $editingId ? 'Edit Layanan Konseling Kelompok' : 'Tambah Layanan Konseling Kelompok' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $editingId ? 'Perbarui data layanan konseling kelompok' : 'Catat layanan konseling kelompok baru' }}
            </p>

            {{-- PROGRESS BAR --}}
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-600">Langkah {{ $step }} Dari 3</span>
                    <span class="text-xs text-gray-400">
                        @if($step === 1) Isi Data
                        @elseif($step === 2) Review
                        @else Unggah Berkas
                        @endif
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary h-2 rounded-full transition-all duration-500 ease-in-out"
                         style="width: {{ ($step / 3) * 100 }}%"></div>
                </div>
                <div class="flex justify-between mt-1.5">
                    @foreach([1, 2, 3] as $s)
                        <div class="flex items-center gap-1">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold
                                {{ $step >= $s ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                                {{ $s }}
                            </div>
                            <span class="text-[10px] {{ $step >= $s ? 'text-primary font-semibold' : 'text-gray-400' }}">
                                @if($s === 1) Data
                                @elseif($s === 2) Review
                                @else Berkas
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- SCROLLABLE CONTENT --}}
        <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">

            {{-- ═══════════════════════════════════════════════
                 STEP 1: FORM DATA
                 ═══════════════════════════════════════════════ --}}
            @if($step === 1)

                {{-- PESERTA (MULTIPLE SISWA) --}}
                <div class="mb-6">
                    <x-atoms.input-label for="siswa_ids" size="sm">
                        Peserta Siswa <span class="text-red-500">*</span>
                    </x-atoms.input-label>

                    {{-- Selected students chips --}}
                    @if($this->selectedStudents->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($this->selectedStudents as $siswa)
                                <div class="inline-flex items-center gap-2 bg-teal-50 border border-teal-200 rounded-full px-3 py-1.5 text-sm">
                                    <span class="font-medium text-teal-800">
                                        {{ $siswa->nama_lengkap ?? $siswa->nama }}
                                    </span>
                                    <span class="text-xs text-teal-500">({{ $siswa->kelas_label }})</span>
                                    <button type="button" wire:click="removeStudent({{ $siswa->id }})"
                                        class="text-teal-500 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <button type="button" wire:click="openStudentModal"
                        class="w-full border-2 border-dashed border-gray-300 rounded-lg p-4 text-sm text-gray-500 hover:border-teal-400 hover:text-teal-600 transition-colors">
                        + Tambah Peserta Siswa
                    </button>
                    @error('siswa_ids') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>

                {{-- TANGGAL LAYANAN --}}
                <div class="mb-6">
                    <x-atoms.input-label for="tanggal_layanan" size="sm">
                        Tanggal Layanan <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <x-atoms.text-input id="tanggal_layanan" type="date" wire:model="tanggal_layanan" size="md" />
                    @error('tanggal_layanan')
                        <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- TAHUN AJARAN --}}
                <div class="mb-6">
                    <x-atoms.input-label for="tahun_ajaran_id" size="sm">
                        Tahun Ajaran <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <select id="tahun_ajaran_id" wire:model="tahun_ajaran_id"
                        class="w-full border border-gray-200 rounded-md px-4 py-2 text-sm
                               focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        <option value="">Pilih Tahun Ajaran</option>
                        @foreach(TahunAjaran::orderByDesc('tahun')->get() as $ta)
                            <option value="{{ $ta->id }}">
                                {{ $ta->tahun }} - {{ $ta->semester }} {{ $ta->status_aktif ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('tahun_ajaran_id')
                        <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                    @enderror
                </div>

            {{-- ═══════════════════════════════════════════════
                 STEP 2: REVIEW / CONFIRMATION
                 ═══════════════════════════════════════════════ --}}
            @elseif($step === 2)

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-blue-700">Pastikan semua data yang Anda masukkan sudah benar sebelum melanjutkan.</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">

                    {{-- PESERTA SISWA --}}
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Peserta Siswa</p>
                        @if($this->selectedStudents->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($this->selectedStudents as $siswa)
                                    <div class="flex items-center gap-3">
                                        <div class="w-[34px] h-[34px] bg-icon-bg text-primary rounded-full flex items-center justify-center font-bold text-[11px] flex-shrink-0">
                                            {{ $this->getInitials($siswa->nama_lengkap ?? $siswa->nama) }}
                                        </div>
                                        <div>
                                            <p class="text-[13px] font-bold text-gray-900">{{ $siswa->nama_lengkap ?? $siswa->nama }}</p>
                                            <p class="text-[11px] text-gray-500">Kelas {{ $siswa->kelas_label }} - NIS {{ $siswa->nis }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[13px] text-red-500 italic">Belum ada peserta dipilih</p>
                        @endif
                    </div>

                    {{-- TANGGAL LAYANAN --}}
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Tanggal Layanan</p>
                        <p class="text-[14px] text-gray-900 font-medium">
                            {{ $tanggal_layanan ? \Carbon\Carbon::parse($tanggal_layanan)->translatedFormat('d M Y') : '-' }}
                        </p>
                    </div>

                    {{-- TAHUN AJARAN --}}
                    <div class="px-5 py-4">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Tahun Ajaran</p>
                        <p class="text-[14px] text-gray-900 font-medium">{{ $this->tahunAjaranLabel }}</p>
                    </div>

                </div>

            {{-- ═══════════════════════════════════════════════
                 STEP 3: FILE UPLOAD
                 ═══════════════════════════════════════════════ --}}
            @elseif($step === 3)

                <div class="mb-6">
                    <x-atoms.input-label size="sm">
                        Unggah Berkas Pendukung
                    </x-atoms.input-label>
                    <p class="text-[12px] text-gray-400 mb-3">Unggah dokumen pendukung seperti surat, foto, atau berkas lainnya (opsional).</p>

                    {{-- DROP ZONE --}}
                    <div x-data="{ isDragging: false }"
                         x-on:dragover.prevent="isDragging = true"
                         x-on:dragleave.prevent="isDragging = false"
                         x-on:drop.prevent="isDragging = false; $refs.fileInput.click()"
                         class="border-2 border-dashed rounded-xl p-8 text-center transition-colors border-primary/30 bg-primary/5"
                         :class="{ 'border-primary bg-primary/10': isDragging }">
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-icon-bg rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                </svg>
                            </div>
                            <p class="text-[13px] font-semibold text-gray-700 mb-1">Seret & lepas file ke sini</p>
                            <p class="text-[12px] text-gray-400 mb-3">atau klik untuk memilih file</p>
                            <label class="cursor-pointer bg-white border border-gray-200 hover:border-primary px-4 py-2 rounded-md text-[12px] font-semibold text-gray-600 hover:text-primary transition-colors shadow-sm">
                                Pilih File
                                <input type="file" x-ref="fileInput" wire:model="uploadedFiles" multiple
                                       class="hidden" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            </label>
                        </div>
                    </div>
                    @error('uploadedFiles') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror

                    {{-- UPLOADED FILES LIST --}}
                    @if(!empty($uploadedFiles))
                        <div class="mt-4 space-y-2">
                            @foreach($uploadedFiles as $index => $file)
                                <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                        </svg>
                                        <span class="text-[13px] text-gray-700 truncate">{{ $file->getClientOriginalName() }}</span>
                                        <span class="text-[11px] text-gray-400 flex-shrink-0">({{ round($file->getSize() / 1024, 1) }} KB)</span>
                                    </div>
                                    <button type="button" wire:click="removeFile({{ $index }})"
                                            class="text-gray-400 hover:text-red-500 transition-colors ml-3 flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            @endif

        </div>

        {{-- FOOTER ACTIONS ──────────────────────────────────--}}
        <div class="bg-bg-light px-7 py-5 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl gap-3">
            <x-atoms.button variant="secondary" size="md" x-on:click="show = false">Batal</x-atoms.button>

            @if($step === 1)
                {{-- Step 1: Batal + Selanjutnya --}}
                <x-atoms.button variant="primary" size="md" wire:click="nextStep">
                    Selanjutnya
                    <svg class="w-4 h-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </x-atoms.button>

            @elseif($step === 2)
                {{-- Step 2: Kembali + Selanjutnya --}}
                <x-atoms.button variant="secondary" size="md" wire:click="prevStep">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </x-atoms.button>
                <x-atoms.button variant="primary" size="md" wire:click="nextStep">
                    Selanjutnya
                    <svg class="w-4 h-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </x-atoms.button>

            @elseif($step === 3)
                {{-- Step 3: Kembali + Simpan/Perbarui --}}
                <x-atoms.button variant="secondary" size="md" wire:click="prevStep">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </x-atoms.button>
                <x-atoms.button wire:click="save" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Perbarui' : 'Simpan' }}
                    </span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </x-atoms.button>
            @endif
        </div>
    </div>

    {{-- MODAL PEMILIHAN SISWA ──────────────────────────────--}}
    <div x-data="{ showStudentMenu: @entangle('showStudentModal') }" x-show="showStudentMenu"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        style="display: none;">
        <div class="bg-white w-full max-w-[500px] rounded-xl shadow-2xl flex flex-col max-h-[80vh] overflow-hidden"
            @click.away="showStudentMenu = false">
            <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0 flex justify-between items-center">
                <div>
                    <h2 class="text-[20px] font-bold text-gray-900 leading-tight">Pilih Siswa</h2>
                    <p class="text-[13px] text-gray-500 mt-0.5">Pilih siswa untuk ditambahkan sebagai peserta</p>
                </div>
            </div>

            <div class="px-6 py-5 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">
                <div class="flex gap-3 mb-5">
                    <div class="relative grow">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live="searchSiswa" placeholder="Cari Nama Atau NIS"
                            class="w-full border border-gray-200 rounded-md pl-10 pr-3 py-2 text-[13px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    @forelse($this->filteredStudents as $siswa)
                        @php $isSelected = in_array($siswa->id, $siswa_ids) @endphp
                        <div wire:click="selectStudent({{ $siswa->id }})"
                            class="flex items-center gap-3 border border-gray-200 rounded-md p-4 cursor-pointer transition-colors
                                {{ $isSelected ? 'border-teal-400 bg-teal-50' : 'hover:border-primary hover:bg-bg-light' }}">
                            <div class="flex-1">
                                <h4 class="text-[14px] font-bold text-gray-900">
                                    {{ $siswa->nama_lengkap ?? $siswa->nama }}
                                </h4>
                                <p class="text-[12px] text-gray-500 mt-1">
                                    NIS: {{ $siswa->nis }} <span class="ml-2">Kelas: {{ $siswa->kelas_label }}</span>
                                </p>
                            </div>
                            @if($isSelected)
                                <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 text-sm">Tidak ada siswa ditemukan.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-2.5">
                <button type="button" wire:click="closeStudentModal"
                    class="px-5 py-2 bg-white border border-gray-200 rounded-md text-[13px] font-bold text-gray-600 hover:bg-gray-50 transition-colors shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    </x-shared.modal>
</div>
