<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use App\Services\i\SiswaService;
use App\Services\e\PengunduranDiriService;

new class extends Component {

    public ?int $editingId = null;
    public int $step = 1;

    // ── DATA PENGUNDURAN DIRI ─────────────────────
    #[Validate('required|integer', message: 'Siswa wajib dipilih.')]
    public $siswa_id = '';

    #[Validate('required|string|max:255', message: 'Nama orang tua/wali wajib diisi.')]
    public string $nama_ortu_wali = '';

    #[Validate('required|string', message: 'Alamat orang tua/wali wajib diisi.')]
    public string $alamat_ortu_wali = '';

    #[Validate('required|string', message: 'Alasan pengunduran diri wajib diisi.')]
    public string $alasan_pengunduran = '';

    #[Validate('required|date', message: 'Tanggal pengunduran wajib diisi.')]
    public string $tanggal_pengunduran = '';

    // ── SISWA ─────────────────────────────────────
    public string $searchSiswa = '';
    public bool $showStudentModal = false;

    // ── FILE UPLOAD (STEP 3) ──────────────────────
    public $uploadedFiles = [];

    public function mount()
    {
        $this->tanggal_pengunduran = date('Y-m-d');
    }

    // ── STEP NAVIGATION ───────────────────────────
    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'siswa_id'            => 'required|integer',
                'nama_ortu_wali'      => 'required|string|max:255',
                'alamat_ortu_wali'    => 'required|string',
                'alasan_pengunduran'  => 'required|string',
                'tanggal_pengunduran' => 'required|date',
            ]);
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

    // ── SISWA SELECTION ───────────────────────────
    public function selectStudent(int $id)
    {
        $this->siswa_id = $id;
        $this->showStudentModal = false;
        $this->searchSiswa = '';
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
    public function selectedStudent()
    {
        if (!$this->siswa_id) return null;
        return app(SiswaService::class)->findById($this->siswa_id);
    }

    #[Computed]
    public function filteredStudents()
    {
        return app(SiswaService::class)->search($this->searchSiswa, 50);
    }

    public function getInitials(?string $name): string
    {
        if (!$name) return 'S';
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    // ── FILE HANDLING ─────────────────────────────
    public function removeFile(int $index)
    {
        unset($this->uploadedFiles[$index]);
        $this->uploadedFiles = array_values($this->uploadedFiles);
    }

    // ── CREATE ────────────────────────────────────
    #[On('create-pengunduran-diri')]
    public function createPengunduranDiri()
    {
        $this->resetValidation();
        $this->reset([
            'siswa_id', 'nama_ortu_wali', 'alamat_ortu_wali',
            'alasan_pengunduran', 'uploadedFiles',
        ]);
        $this->editingId = null;
        $this->tanggal_pengunduran = date('Y-m-d');
        $this->step = 1;

        $this->dispatch('open-modal', 'form-pengunduran-diri');
    }

    // ── EDIT ──────────────────────────────────────
    #[On('edit-pengunduran-diri')]
    public function loadPengunduranDiri(int $id)
    {
        $service = app(PengunduranDiriService::class);
        $this->resetValidation();

        $record = $service->findById($id);

        $this->editingId = $id;
        $this->siswa_id = $record->siswa_id;
        $this->nama_ortu_wali = $record->nama_ortu_wali;
        $this->alamat_ortu_wali = $record->alamat_ortu_wali;
        $this->alasan_pengunduran = $record->alasan_pengunduran;
        $this->tanggal_pengunduran = \Carbon\Carbon::parse($record->tanggal_pengunduran)->format('Y-m-d');
        $this->uploadedFiles = [];
        $this->step = 1;

        $this->dispatch('open-modal', 'form-pengunduran-diri');
    }

    // ── SAVE ──────────────────────────────────────
    public function save(PengunduranDiriService $service)
    {
        $this->validate();

        $data = [
            'siswa_id'            => $this->siswa_id,
            'nama_ortu_wali'      => $this->nama_ortu_wali,
            'alamat_ortu_wali'    => $this->alamat_ortu_wali,
            'alasan_pengunduran'  => $this->alasan_pengunduran,
            'tanggal_pengunduran' => $this->tanggal_pengunduran,
        ];

        if ($this->editingId) {
            $service->update($this->editingId, $data);
            session()->flash('success', 'Data pengunduran diri berhasil diperbarui!');
        } else {
            $service->create($data);
            session()->flash('success', 'Data pengunduran diri berhasil ditambahkan!');
        }

        $this->reset([
            'siswa_id', 'nama_ortu_wali', 'alamat_ortu_wali',
            'alasan_pengunduran', 'uploadedFiles',
        ]);
        $this->editingId = null;
        $this->tanggal_pengunduran = date('Y-m-d');
        $this->step = 1;

        $this->dispatch('close-modal', 'form-pengunduran-diri');
        $this->dispatch('refreshTable');
    }
}; ?>

<div>
    <x-shared.modal name="form-pengunduran-diri" maxWidth="lg">
    <div class="flex flex-col h-full max-h-[80vh]">

        {{-- HEADER --}}
        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">
                {{ $editingId ? 'Edit Pengunduran Diri' : 'Tambah Pengunduran Diri' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $editingId ? 'Perbarui data pengunduran diri siswa' : 'Catat pengunduran diri siswa baru' }}
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

                {{-- SISWA --}}
                <div class="mb-6">
                    <x-atoms.input-label for="id_siswa" size="sm">
                        Siswa <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    @if($this->selectedStudent)
                        <div class="bg-bg-light border border-teal-100/60 rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-[45px] h-[45px] bg-icon-bg text-primary rounded-full flex items-center justify-center font-bold text-[16px]">
                                    {{ $this->getInitials($this->selectedStudent->nama_lengkap ?? $this->selectedStudent->nama) }}
                                </div>
                                <div>
                                    <h3 class="text-[14px] font-bold text-gray-900">
                                        {{ $this->selectedStudent->nama_lengkap ?? $this->selectedStudent->nama }}</h3>
                                    <p class="text-[12px] text-gray-400 mt-0.5">Kelas {{ $this->selectedStudent->kelas_label }}
                                        {{ $this->selectedStudent->jurusan_label }} - NIS {{ $this->selectedStudent->nis }}</p>
                                </div>
                            </div>
                            <button type="button" wire:click="openStudentModal" class="text-[13px] font-bold text-gray-500 hover:text-gray-800 transition-colors">
                                Ganti
                            </button>
                        </div>
                    @else
                        <div class="bg-bg-light border border-teal-100/60 rounded-lg p-5 flex flex-col items-center justify-center text-center">
                            <div class="w-[56px] h-[56px] bg-icon-bg rounded-full flex items-center justify-center mb-3 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <h3 class="text-[15px] font-bold text-gray-700 mb-1">Tidak Ada Siswa Yang Dipilih</h3>
                            <p class="text-[13px] text-gray-400 mb-4">Pilih Siswa Untuk Melanjutkan</p>
                            <button type="button" wire:click="openStudentModal" class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-md text-[13px] font-semibold transition-colors">
                                Pilih Siswa
                            </button>
                        </div>
                        @error('siswa_id') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                    @endif
                </div>

                {{-- TANGGAL PENGUNDURAN --}}
                <div class="mb-6">
                    <x-atoms.input-label for="tanggal_pengunduran" size="sm">
                        Tanggal Pengunduran <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <x-atoms.text-input id="tanggal_pengunduran" type="date" wire:model="tanggal_pengunduran" size="md" />
                    @error('tanggal_pengunduran') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>

                {{-- NAMA ORANG TUA / WALI --}}
                <div class="mb-6">
                    <x-atoms.input-label for="nama_ortu_wali" size="sm">
                        Nama Orang Tua / Wali <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <x-atoms.text-input id="nama_ortu_wali" wire:model="nama_ortu_wali" size="md" placeholder="Masukkan nama orang tua atau wali" />
                    @error('nama_ortu_wali') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>

                {{-- ALAMAT ORANG TUA / WALI --}}
                <div class="mb-6">
                    <x-atoms.input-label for="alamat_ortu_wali" size="sm">
                        Alamat Orang Tua / Wali <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <textarea id="alamat_ortu_wali" wire:model="alamat_ortu_wali" rows="3"
                        class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                        placeholder="Masukkan alamat orang tua atau wali"></textarea>
                    @error('alamat_ortu_wali') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>

                {{-- ALASAN PENGUNDURAN DIRI --}}
                <div class="mb-6">
                    <x-atoms.input-label for="alasan_pengunduran" size="sm">
                        Alasan Pengunduran Diri <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <textarea id="alasan_pengunduran" wire:model="alasan_pengunduran" rows="4"
                        class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                        placeholder="Tuliskan alasan pengunduran diri siswa..."></textarea>
                    @error('alasan_pengunduran') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
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

                    {{-- SISWA --}}
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Siswa</p>
                        @if($this->selectedStudent)
                            <div class="flex items-center gap-3">
                                <div class="w-[38px] h-[38px] bg-icon-bg text-primary rounded-full flex items-center justify-center font-bold text-[13px]">
                                    {{ $this->getInitials($this->selectedStudent->nama_lengkap ?? $this->selectedStudent->nama) }}
                                </div>
                                <div>
                                    <p class="text-[14px] font-bold text-gray-900">{{ $this->selectedStudent->nama_lengkap ?? $this->selectedStudent->nama }}</p>
                                    <p class="text-[12px] text-gray-500">Kelas {{ $this->selectedStudent->kelas_label }} {{ $this->selectedStudent->jurusan_label }} - NIS {{ $this->selectedStudent->nis }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-[13px] text-red-500 italic">Belum dipilih</p>
                        @endif
                    </div>

                    {{-- TANGGAL PENGUNDURAN --}}
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Tanggal Pengunduran</p>
                        <p class="text-[14px] text-gray-900 font-medium">
                            {{ $tanggal_pengunduran ? \Carbon\Carbon::parse($tanggal_pengunduran)->translatedFormat('d M Y') : '-' }}
                        </p>
                    </div>

                    {{-- NAMA ORANG TUA / WALI --}}
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Nama Orang Tua / Wali</p>
                        <p class="text-[14px] text-gray-900 font-medium">{{ $nama_ortu_wali ?: '-' }}</p>
                    </div>

                    {{-- ALAMAT ORANG TUA / WALI --}}
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Alamat Orang Tua / Wali</p>
                        <p class="text-[14px] text-gray-900 whitespace-pre-line">{{ $alamat_ortu_wali ?: '-' }}</p>
                    </div>

                    {{-- ALASAN PENGUNDURAN --}}
                    <div class="px-5 py-4">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Alasan Pengunduran Diri</p>
                        <p class="text-[14px] text-gray-900 whitespace-pre-line">{{ $alasan_pengunduran ?: '-' }}</p>
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
                    <p class="text-[12px] text-gray-400 mb-3">Unggah dokumen pendukung seperti surat pengunduran diri, foto, atau berkas lainnya (opsional).</p>

                    {{-- DROP ZONE --}}
                    <div x-data="{ isDragging: false }"
                         x-on:dragover.prevent="isDragging = true"
                         x-on:dragleave.prevent="isDragging = false"
                         x-on:drop.prevent="isDragging = false; $refs.fileInput.click()"
                         class="border-2 border-dashed rounded-xl p-8 text-center transition-colors
                                border-primary/30 bg-primary/5"
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

        {{-- FOOTER ACTIONS --}}
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

    {{-- MODAL PEMILIHAN SISWA --}}
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
                    <p class="text-[13px] text-gray-500 mt-0.5">Semua Siswa</p>
                </div>
            </div>

            <div class="px-6 py-5 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">
                <div class="flex gap-3 mb-5">
                    <div class="relative grow">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                        </div>
                        <input type="text" wire:model.live="searchSiswa" placeholder="Cari Nama Atau NIS" class="w-full border border-gray-200 rounded-md pl-10 pr-3 py-2 text-[13px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    @forelse($this->filteredStudents as $siswa)
                        <div wire:click="selectStudent({{ $siswa->id }})"
                            class="border border-gray-200 rounded-md p-4 cursor-pointer hover:border-primary hover:bg-bg-light transition-colors {{ $siswa_id == $siswa->id ? 'border-primary bg-bg-light' : '' }}">
                            <h4 class="text-[14px] font-bold text-gray-900">{{ $siswa->nama_lengkap ?? $siswa->nama }}</h4>
                            <p class="text-[12px] text-gray-500 mt-1">NIS: {{ $siswa->nis }} <span class="ml-2">Kelas: {{ $siswa->kelas_label }}</span></p>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 text-sm">Tidak ada siswa ditemukan.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-2.5">
                <button type="button" wire:click="closeStudentModal" class="px-5 py-2 bg-white border border-gray-200 rounded-md text-[13px] font-bold text-gray-600 hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
    </x-shared.modal>
</div>
