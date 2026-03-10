<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use App\Services\SiswaService;
use App\Services\KonsultasiService;

new class extends Component {
    use WithFileUploads;

    public int $step = 1;
    public ?int $editingId = null;
    public array $existingFiles = [];

    #[Validate('required|integer')]
    public $id_siswa = '';

    #[Validate('required|string')]
    public $jenis_layanan = 'Individu';

    #[Validate('required|date')]
    public $tanggal = '';

    #[Validate('required|string')]
    public $deskripsi_masalah = '';

    #[Validate('nullable|string')]
    public $hasil_layanan = '';

    #[Validate('nullable|string')]
    public $tindak_lanjut = '';

    #[Validate([
        'files' => 'array|max:5',
        'files.*' => 'file|max:5120|mimes:pdf,jpg,png,docx',
    ])]
    public $files = [];

    public $searchSiswa = '';
    public $showStudentModal = false;

    public $jenisLayanan = [
        ['value' => 'Individu', 'label' => 'Individu'],
        ['value' => 'Kelompok', 'label' => 'Kelompok'],
        ['value' => 'Klasikal', 'label' => 'Klasikal'],
    ];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
    }

    public function nextStep()
    {
        $this->validate([
            'id_siswa' => 'required|integer',
            'jenis_layanan' => 'required|string',
            'tanggal' => 'required|date',
            'deskripsi_masalah' => 'required|string',
        ]);
        $this->step = 2;
    }

    public function previousStep()
    {
        $this->step = 1;
    }

    public function selectStudent($id)
    {
        $this->id_siswa = $id;
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

    public function removeFile($index)
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files); // Reset index
    }

    #[Computed]
    public function selectedStudent()
    {
        if (!$this->id_siswa)
            return null;
        return app(SiswaService::class)->findById($this->id_siswa);
    }

    #[Computed]
    public function filteredStudents()
    {
        return app(SiswaService::class)->search($this->searchSiswa, 50);
    }

    public function getInitials($name)
    {
        if (!$name)
            return 'S';
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    public function removeExistingFile($index)
    {
        unset($this->existingFiles[$index]);
        $this->existingFiles = array_values($this->existingFiles);
    }

    #[On('edit-konsultasi')]
    public function loadKonsultasi($id)
    {
        $service = app(KonsultasiService::class);
        $this->resetValidation();
        $this->reset(['files']);

        $record = $service->findById($id);
        $this->editingId = $id;
        $this->id_siswa = $record->id_siswa;
        $this->jenis_layanan = $record->jenis_layanan;
        $this->tanggal = $record->tanggal;
        $this->deskripsi_masalah = $record->deskripsi_masalah;
        $this->hasil_layanan = $record->hasil_layanan;
        $this->tindak_lanjut = $record->tindak_lanjut;
        
        $this->existingFiles = is_array($record->files) ? $record->files : [];
        $this->step = 1;

        $this->dispatch('open-modal', 'tambah-konsultasi');
    }

    public function save(KonsultasiService $service)
    {
        $this->validate();

        $data = [
            'id_siswa' => $this->id_siswa,
            'jenis_layanan' => $this->jenis_layanan,
            'tanggal' => $this->tanggal,
            'deskripsi_masalah' => $this->deskripsi_masalah,
            'hasil_layanan' => $this->hasil_layanan,
            'tindak_lanjut' => $this->tindak_lanjut,
        ];

        // Mulai dengan existing files yang dipertahankan
        $finalFiles = $this->existingFiles;

        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                $ext = strtolower($file->extension());
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $finalFiles[] = $file->store('data/images', 'public');
                } else {
                    $finalFiles[] = $file->store('data/documents', 'public');
                }
            }
        }

        // Terapkan json encoding file list
        $data['files'] = !empty($finalFiles) ? $finalFiles : null;

        if ($this->editingId) {
            $service->update($this->editingId, $data);
            session()->flash('success', 'Konsultasi berhasil diperbarui!');
        } else {
            $service->create($data);
            session()->flash('success', 'Konsultasi berhasil ditambahkan!');
        }

        $this->reset(['editingId', 'id_siswa', 'jenis_layanan', 'tanggal', 'deskripsi_masalah', 'hasil_layanan', 'tindak_lanjut', 'files', 'existingFiles', 'searchSiswa', 'showStudentModal']);
        $this->step = 1;
        $this->tanggal = date('Y-m-d');

        $this->dispatch('close-modal', 'tambah-konsultasi');
        $this->dispatch('refreshTable');
    }
}; ?>

<div>
    <x-shared.modal name="tambah-konsultasi" maxWidth="lg">
    <div class="flex flex-col h-full max-h-[80vh]">
        <!-- Header -->
        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">Konsultasi</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $editingId ? 'Edit Konsultasi' : 'Tambah Konsultasi' }}</p>
        </div>

        <!-- Body (Scrollable) -->
        <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">

            <!-- ==================== LANGKAH 1 ==================== -->
            <div class="{{ $step === 1 ? 'block' : 'hidden' }}">
                <!-- Progress Bar 1 -->
                <div class="mb-6">
                    <p class="text-[14px] font-bold text-primary mb-2.5">Langkah 1 Dari 2</p>
                    <div class="flex gap-2.5">
                        <!-- Bar 1 Terisi -->
                        <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>
                        <!-- Bar 2 Kosong -->
                        <div class="h-2.5 w-1/2 bg-gray-200/80 rounded-full"></div>
                    </div>
                </div>

                <!-- Siswa Section -->
                <div class="mb-6">
                    <x-atoms.input-label for="jenis_layanan" size="sm">Siswa</x-atoms.input-label>

                    @if($this->selectedStudent)
                        <div class="bg-bg-light border border-teal-100/60 rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-[45px] h-[45px] bg-icon-bg text-primary rounded-full flex items-center justify-center font-bold text-[16px]">
                                    {{ $this->getInitials($this->selectedStudent->nama_lengkap ?? $this->selectedStudent->nama) }}
                                </div>
                                <div>
                                    <h3 class="text-[14px] font-bold text-gray-900">
                                        {{ $this->selectedStudent->nama_lengkap ?? $this->selectedStudent->nama }}</h3>
                                    <p class="text-[12px] text-gray-400 mt-0.5">Kelas {{ $this->selectedStudent->kelas }}
                                        {{ $this->selectedStudent->jurusan }} - NIS {{ $this->selectedStudent->nis }}</p>
                                </div>
                            </div>
                            <button type="button" wire:click="openStudentModal"
                                class="text-[13px] font-bold text-gray-500 hover:text-gray-800 transition-colors">
                                Ganti
                            </button>
                        </div>
                    @else
                        <div
                            class="bg-bg-light border border-teal-100/60 rounded-lg p-5 flex flex-col items-center justify-center text-center">
                            <div
                                class="w-[56px] h-[56px] bg-icon-bg rounded-full flex items-center justify-center mb-3 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-7 h-7">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <h3 class="text-[15px] font-bold text-gray-700 mb-1">Tidak Ada Siswa Yang Dipilih</h3>
                            <p class="text-[13px] text-gray-400 mb-4">Pilih Siswa Untuk Melanjutkan</p>
                            <button type="button" wire:click="openStudentModal"
                                class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-md text-[13px] font-semibold transition-colors">
                                Pilih Siswa
                            </button>
                        </div>
                        @error('id_siswa') <span
                        class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Jenis Layanan & Tanggal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                    <div>
                        <x-atoms.input-label for="jenis_layanan" size="sm">Jenis Layanan</x-atoms.input-label>
                        <x-molecules.input-dropdown id="jenis_layanan" wire:model="jenis_layanan" size="md" :options="$jenisLayanan"/>
                        @error('jenis_layanan') <span
                        class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-atoms.input-label for="tanggal" size="sm">Tanggal</x-atoms.input-label>
                        <x-atoms.text-input id="tanggal" type="date" wire:model="tanggal" size="md" :options="$tanggal"/>
                        @error('tanggal') <span
                        class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Textareas -->
                <div class="mb-5">
                    <x-atoms.input-label for="deskripsi_masalah" size="sm">Deskripsi Masalah</x-atoms.input-label>
                    <textarea id="deskripsi_masalah" wire:model="deskripsi_masalah" rows="4"
                        class="w-full border border-gray-200 rounded-md p-4 text-[14.5px] text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm leading-relaxed"
                        placeholder="Ceritakan gambaran umum masalah yang dihadapi oleh siswa secara lengkap..."></textarea>
                    @error('deskripsi_masalah') <span
                    class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-5">
                    <x-atoms.input-label for="hasil_layanan" size="sm">Hasil Layanan</x-atoms.input-label>
                    <textarea id="hasil_layanan" wire:model="hasil_layanan" rows="4"
                        class="w-full border border-gray-200 rounded-md p-4 text-[14.5px] text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm leading-relaxed"
                        placeholder="Ceritakan bagaimana hasil setelah layanan diberikan..."></textarea>
                </div>

                <div class="mb-2">
                    <x-atoms.input-label for="tindak_lanjut" size="sm">Tindak Lanjut</x-atoms.input-label>
                    <textarea id="tindak_lanjut" wire:model="tindak_lanjut" rows="4"
                        class="w-full border border-gray-200 rounded-md p-4 text-[14.5px] text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm leading-relaxed"
                        placeholder="Langkah konkrit apa yang akan diambil setelah ini..."></textarea>
                </div>
            </div>

            <!-- ==================== LANGKAH 2 ==================== -->
            <div class="{{ $step === 2 ? 'block' : 'hidden' }}">
                <!-- Progress Bar 2 -->
                <div class="mb-6">
                    <p class="text-[14px] font-bold text-primary mb-2.5">Langkah 2 Dari 2</p>
                    <div class="flex gap-2.5">
                        <!-- Bar 1 Terisi (Telah Dilewati) -->
                        <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>
                        <!-- Bar 2 Terisi (Langkah Saat Ini) -->
                        <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>
                    </div>
                </div>

                <!-- Upload File Section -->
                <div class="mb-6">
                    <label class="block text-[14px] font-bold text-gray-700 mb-2">Pilih Siswa</label>

                    <div x-data="{ isDropping: false }" x-on:dragover.prevent="isDropping = true"
                        x-on:dragleave.prevent="isDropping = false"
                        x-on:drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                        x-on:click="$refs.fileInput.click()"
                        class="bg-bg-light border-2 py-16 px-6 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-[#e9f3f5] transition-colors border-dashed rounded-xl"
                        :class="isDropping ? 'bg-[#e9f3f5] border-primary' : 'border-icon-bg/40'">
                        <input type="file" wire:model="files" multiple x-ref="fileInput" class="hidden">

                        <div class="w-[84px] h-[84px] bg-icon-bg/90 rounded-full flex items-center justify-center mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="#044B5F" class="w-10 h-10">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                            </svg>
                        </div>
                        <h3 class="text-[16px] font-bold text-gray-800 mb-2">Tarik Dan Lepas file disini atau Klik untuk
                            Unggah</h3>
                        <p class="text-[14px] text-gray-400">Hingga 5 file , Maks 5 MB per file (PDF, JPG, PNG, DOCX)
                        </p>
                    </div>
                    @error('file') <span class="text-red-500 text-[13px] font-medium mt-2 block">{{ $message }}</span>
                    @enderror
                </div>

                @if($files && count($files) > 0)
                    <!-- Uploaded File Card -->
                    <div class="mb-4">
                        <h4 class="text-[14.5px] font-bold text-gray-700 mb-3">File Terunggah</h4>
                        <div class="flex flex-col gap-3">
                            @foreach($files as $index => $fileObj)
                                <div
                                    class="border border-gray-200 rounded-xl p-4 flex items-center justify-between bg-white shadow-sm">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                class="w-7 h-7">
                                                <path
                                                    d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z" />
                                                <path
                                                    d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
                                            </svg>
                                        </div>
                                        <div class="flex flex-col overflow-hidden max-w-[320px]">
                                            <p class="text-[15px] font-bold text-gray-800 truncate">
                                                {{ $fileObj->getClientOriginalName() }}</p>
                                            <p class="text-[13.5px] text-gray-500 font-medium tracking-wide">
                                                {{ round($fileObj->getSize() / 1024 / 1024, 2) }} MB</p>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removeFile({{ $index }})"
                                        class="text-gray-400 hover:text-red-500 transition-colors p-2 shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- List Existing File -->
                @if(!empty($existingFiles))
                    <div class="mb-4">
                        <h4 class="text-[14.5px] font-bold text-gray-700 mb-3">File Lama Tersimpan</h4>
                        <div class="flex flex-col gap-3">
                            @foreach($existingFiles as $index => $path)
                                @php
                                    // Parse file details
                                    $fileName = basename($path);
                                    $isImage = in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']);
                                    $fileUrl = asset('storage/' . $path);
                                @endphp
                                <div class="border border-gray-200 rounded-xl p-4 flex items-center justify-between bg-white shadow-sm hover:border-blue-300 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <!-- Document/Image Icon Placeholder -->
                                        @if($isImage)
                                        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                              </svg>                                              
                                        </div>
                                        @else
                                        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7">
                                                <path d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z" />
                                                <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
                                            </svg>
                                        </div>
                                        @endif

                                        <div class="flex flex-col overflow-hidden max-w-[320px]">
                                            <p class="text-[15px] font-bold text-gray-800 truncate">
                                                <a href="{{ $fileUrl }}" target="_blank" class="hover:text-blue-600 transition-colors">{{ $fileName }}</a>
                                            </p>
                                            <p class="text-[13.5px] text-gray-400 font-medium tracking-wide">
                                                {{ $isImage ? 'Gambar' : 'Dokumen' }} • Tersimpan
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removeExistingFile({{ $index }})"
                                        class="text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors p-2 shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <!-- Footer -->
        <div class="bg-bg-light px-7 py-5 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl">
            <!-- Footer Buttons Step 1 -->
            <div class="{{ $step === 1 ? 'flex' : 'hidden' }} gap-3">
                <x-atoms.button variant="secondary" size="md" x-on:click="show = false">Batal</x-atoms.button>
                <x-atoms.button wire:click="nextStep">Langkah Terakhir : Upload File</x-atoms.button>
            </div>

            <!-- Footer Buttons Step 2 (Based on User Reference) -->
            <div class="{{ $step === 2 ? 'flex' : 'hidden' }} gap-3">
                <x-atoms.button variant="secondary" size="md" x-on:click="show = false" wire:click="previousStep">Batal</x-atoms.button>
                <x-atoms.button wire:click="save">Simpan</x-atoms.button>
            </div>
        </div>
    </div>

    <!-- Modal Pilih Siswa -->
    <div x-data="{ showStudentMenu: @entangle('showStudentModal') }" x-show="showStudentMenu"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
        <div class="bg-white w-full max-w-[500px] rounded-xl shadow-2xl flex flex-col max-h-[80vh] overflow-hidden"
            @click.away="showStudentMenu = false">
            <!-- Header Modal Siswa -->
            <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0 flex justify-between items-center">
                <div>
                    <h2 class="text-[20px] font-bold text-gray-900 leading-tight">Pilih Siswa</h2>
                    <p class="text-[13px] text-gray-500 mt-0.5">Semua Siswa</p>
                </div>
            </div>

            <!-- Body Modal Siswa -->
            <div class="px-6 py-5 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">
                <!-- Search & Filter Bar -->
                <div class="flex gap-3 mb-5">
                    <div class="relative grow">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live="searchSiswa" placeholder="Cari Nama Atau NIS"
                            class="w-full border border-gray-200 rounded-md pl-10 pr-3 py-2 text-[13px] text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">
                    </div>
                </div>

                <!-- List Siswa -->
                <div class="flex flex-col gap-3">
                    @forelse($this->filteredStudents as $siswa)
                        <div wire:click="selectStudent({{ $siswa->id }})"
                            class="student-card border border-gray-200 rounded-md p-4 cursor-pointer hover:border-primary hover:bg-bg-light transition-colors {{ $id_siswa == $siswa->id ? 'border-primary bg-bg-light' : '' }}">
                            <h4 class="text-[14px] font-bold text-gray-900">{{ $siswa->nama_lengkap ?? $siswa->nama }}</h4>
                            <p class="text-[12px] text-gray-500 mt-1">NIS: {{ $siswa->nis }} <span class="ml-2">Kelas:
                                    {{ $siswa->kelas }} {{ $siswa->jurusan }}</span></p>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 text-sm">Tidak ada siswa ditemukan.</div>
                    @endforelse
                </div>
            </div>

            <!-- Footer Modal Siswa -->
            <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-2.5">
                <button type="button" wire:click="closeStudentModal"
                    class="px-5 py-2 bg-white border border-gray-200 rounded-md text-[13px] font-bold text-gray-600 hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</x-shared.modal>
</div>