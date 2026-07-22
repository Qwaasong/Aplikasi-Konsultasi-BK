<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use App\Services\AlihTanganKasusService;

new class extends Component {

    public ?int $editingId = null;
    public int $step = 1;

    // Data alih tangan
    public $kasus_id = '';
    public $tanggal_alih = '';
    public $nama_penerima = '';
    public $alasan_alih = '';
    public $tindak_lanjut = '';

    // File upload
    public $file = null;
    public $fileName = '';
    public $fileSize = '';
    public $fileError = '';

    // Pencarian kasus
    public $searchKasus = '';
    public $showKasusModal = false;

    public function mount()
    {
        $this->tanggal_alih = date('Y-m-d');
    }

    public function selectKasus($id)
    {
        $this->kasus_id = $id;
        $this->showKasusModal = false;
        $this->searchKasus = '';
    }

    public function openKasusModal()
    {
        $this->showKasusModal = true;
    }

    public function closeKasusModal()
    {
        $this->showKasusModal = false;
    }

    #[Computed]
    public function selectedKasus()
    {
        if (!$this->kasus_id) return null;
        return app(AlihTanganKasusService::class)->getKasusOptions()
            ->firstWhere('id', (int) $this->kasus_id);
    }

    #[Computed]
    public function kasusOptions()
    {
        $all = app(AlihTanganKasusService::class)->getKasusOptions();

        if ($this->searchKasus) {
            $needle = strtolower($this->searchKasus);
            $all = $all->filter(function ($k) use ($needle) {
                return str_contains(strtolower($k['nama_siswa'] ?? ''), $needle)
                    || str_contains(strtolower($k['penanganan'] ?? ''), $needle)
                    || str_contains($k['nis'] ?? '', $needle);
            })->values();
        }

        return $all;
    }

    #[Computed]
    public function guruBkOptions()
    {
        return app(AlihTanganKasusService::class)->getGuruBkOptions();
    }

    #[Computed]
    public function selectedGuruName()
    {
        if (!$this->nama_penerima) return '-';
        $guru = $this->guruBkOptions->firstWhere('id', (int) $this->nama_penerima);
        return $guru ? $guru['nama'] . ' (' . $guru['nip'] . ')' : '-';
    }

    #[Computed]
    public function formattedTanggalAlih()
    {
        if (!$this->tanggal_alih) return '-';
        try {
            return \Carbon\Carbon::parse($this->tanggal_alih)->locale('id')->isoFormat('D MMMM YYYY');
        } catch (\Exception $e) {
            return $this->tanggal_alih;
        }
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

    // --- Step Navigation ---
    public function nextStep()
    {
        if ($this->step === 1) {
            // Validate step 1 fields before proceeding
            $this->validate([
                'kasus_id' => 'required|integer',
                'tanggal_alih' => 'required|date',
                'nama_penerima' => 'required|integer',
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

    // --- File Upload ---
    public function handleFileUpload($event)
    {
        $this->fileError = '';

        try {
            $uploadedFile = $this->file;
            if ($uploadedFile) {
                $this->fileName = $uploadedFile->getClientOriginalName();
                $this->fileSize = $this->formatFileSize($uploadedFile->getSize());
            }
        } catch (\Exception $e) {
            $this->fileError = 'Gagal mengunggah file. Silakan coba lagi.';
        }
    }

    public function removeFile()
    {
        $this->file = null;
        $this->fileName = '';
        $this->fileSize = '';
        $this->fileError = '';
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    // CREATE
    #[On('create-alih-tangan-kasus')]
    public function createAlihTanganKasus()
    {
        $this->resetValidation();
        $this->reset([
            'editingId', 'kasus_id', 'tanggal_alih', 'nama_penerima',
            'alasan_alih', 'tindak_lanjut', 'searchKasus', 'showKasusModal',
            'step', 'file', 'fileName', 'fileSize', 'fileError',
        ]);
        $this->tanggal_alih = date('Y-m-d');
        $this->step = 1;
        $this->dispatch('open-modal', 'form-alih-tangan-kasus');
    }

    // EDIT
    #[On('edit-alih-tangan-kasus')]
    public function loadAlihTanganKasus($id)
    {
        $service = app(AlihTanganKasusService::class);
        $this->resetValidation();
        $this->reset([
            'searchKasus', 'showKasusModal', 'step',
            'file', 'fileName', 'fileSize', 'fileError',
        ]);

        $record = $service->findById((int) $id);

        $this->editingId = $record->id;
        $this->kasus_id = $record->kasus_id;
        $this->tanggal_alih = $record->tanggal_alih
            ? \Carbon\Carbon::parse($record->tanggal_alih)->format('Y-m-d')
            : date('Y-m-d');
        $this->nama_penerima = $record->nama_penerima;
        $this->alasan_alih = $record->alasan_alih;
        $this->tindak_lanjut = $record->tindak_lanjut;
        $this->step = 1;

        $this->dispatch('open-modal', 'form-alih-tangan-kasus');
    }

    // SAVE
    public function save(AlihTanganKasusService $service)
    {
        $rules = [
            'kasus_id' => 'required|integer',
            'tanggal_alih' => 'required|date',
            'nama_penerima' => 'required|integer',
            'alasan_alih' => 'nullable|string',
            'tindak_lanjut' => 'nullable|string',
        ];

        $this->validate($rules);

        $data = [
            'kasus_id' => $this->kasus_id,
            'tanggal_alih' => $this->tanggal_alih,
            'nama_penerima' => $this->nama_penerima,
            'alasan_alih' => $this->alasan_alih,
            'tindak_lanjut' => $this->tindak_lanjut,
        ];

        if ($this->editingId) {
            $service->update($this->editingId, $data);
            session()->flash('success', 'Alih Tangan Kasus berhasil diperbarui!');
        } else {
            $service->create($data);
            session()->flash('success', 'Alih Tangan Kasus berhasil ditambahkan!');
        }

        $this->reset([
            'editingId', 'kasus_id', 'tanggal_alih', 'nama_penerima',
            'alasan_alih', 'tindak_lanjut', 'searchKasus', 'showKasusModal',
            'step', 'file', 'fileName', 'fileSize', 'fileError',
        ]);
        $this->tanggal_alih = date('Y-m-d');
        $this->step = 1;

        $this->dispatch('close-modal', 'form-alih-tangan-kasus');
        $this->dispatch('refreshTable');
    }
}; ?>

<div>
    <x-shared.modal name="form-alih-tangan-kasus" maxWidth="lg">
    <div class="flex flex-col h-full max-h-[80vh]">

        {{-- HEADER --}}
        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">
                {{ $editingId ? 'Edit Alih Tangan Kasus' : 'Tambah Alih Tangan Kasus' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $editingId ? 'Perbarui data alih tangan kasus' : 'Catat alih tangan kasus baru' }}
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

        {{-- STEP CONTENT --}}
        <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">

            {{-- ============================================== --}}
            {{-- STEP 1: Form Fields --}}
            {{-- ============================================== --}}
            @if($step === 1)

                {{-- PILIH KASUS --}}
                <div class="mb-6">
                    <x-atoms.input-label for="kasus_id" size="sm">
                        Kasus yang Dialihkan <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    @if($this->selectedKasus)
                        @php $k = $this->selectedKasus; @endphp
                        <div class="bg-bg-light border border-teal-100/60 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-[14px] font-bold text-gray-900">{{ $k['nama_siswa'] }}</h3>
                                    <p class="text-[12px] text-gray-500 mt-0.5">
                                        {{ $k['penanganan'] }} &middot; {{ $k['kategori'] }}
                                    </p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">
                                        NIS {{ $k['nis'] }} &middot; Kelas {{ $k['kelas_label'] }}
                                        &middot; {{ $k['tanggal_mulai'] }}
                                    </p>
                                </div>
                                <button type="button" wire:click="openKasusModal"
                                    class="text-[13px] font-bold text-gray-500 hover:text-gray-800 transition-colors shrink-0 ml-2">
                                    Ganti
                                </button>
                            </div>
                        </div>
                    @else
                        <button type="button" wire:click="openKasusModal"
                            class="w-full border-2 border-dashed border-gray-300 rounded-lg p-5 text-sm text-gray-500 hover:border-teal-400 hover:text-teal-600 transition-colors text-center">
                            + Pilih Kasus
                        </button>
                    @endif
                    @error('kasus_id') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>

                {{-- Tanggal Alih Tangan --}}
                <div class="mb-6">
                    <x-atoms.input-label for="tanggal_alih" size="sm">
                        Tanggal Alih Tangan <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <x-atoms.text-input id="tanggal_alih" type="date" wire:model="tanggal_alih" size="md" />
                    @error('tanggal_alih')
                        <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Guru BK Penerima --}}
                <div class="mb-6">
                    <x-atoms.input-label for="nama_penerima" size="sm">
                        Guru BK Penerima <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <select id="nama_penerima" wire:model="nama_penerima"
                        class="w-full border border-gray-200 rounded-md px-4 py-3 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">
                        <option value="">Pilih Guru BK Penerima</option>
                        @foreach($this->guruBkOptions as $guru)
                            <option value="{{ $guru['id'] }}">
                                {{ $guru['nama'] }} ({{ $guru['nip'] }})
                            </option>
                        @endforeach
                    </select>
                    @error('nama_penerima')
                        <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Alasan Alih Tangan --}}
                <div class="mb-6">
                    <x-atoms.input-label for="alasan_alih" size="sm">
                        Alasan Alih Tangan
                    </x-atoms.input-label>
                    <textarea id="alasan_alih" wire:model="alasan_alih" rows="3"
                        class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                        placeholder="Contoh: Membutuhkan penanganan psikolog karena memerlukan asesmen lanjutan"></textarea>
                    @error('alasan_alih')
                        <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tindak Lanjut --}}
                <div class="mb-6">
                    <x-atoms.input-label for="tindak_lanjut" size="sm">
                        Tindak Lanjut
                    </x-atoms.input-label>
                    <textarea id="tindak_lanjut" wire:model="tindak_lanjut" rows="3"
                        class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                        placeholder="Contoh: Guru BK melakukan monitoring perkembangan setelah proses alih tangan."></textarea>
                    @error('tindak_lanjut')
                        <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                    @enderror
                </div>

            @endif

            {{-- ============================================== --}}
            {{-- STEP 2: Review / Confirmation --}}
            {{-- ============================================== --}}
            @if($step === 2)
                <div class="space-y-5">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-[13px] text-amber-800 font-medium">
                            Pastikan semua data di bawah ini sudah benar sebelum melanjutkan. Anda bisa kembali ke Langkah 1 untuk mengubah data.
                        </p>
                    </div>

                    {{-- Kasus --}}
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-200">
                            <h4 class="text-[12px] font-bold text-gray-500 uppercase tracking-wider">Kasus yang Dialihkan</h4>
                        </div>
                        <div class="px-4 py-3">
                            @if($this->selectedKasus)
                                @php $k = $this->selectedKasus; @endphp
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ $this->getInitials($k['nama_siswa']) }}
                                    </div>
                                    <div>
                                        <p class="text-[14px] font-bold text-gray-900">{{ $k['nama_siswa'] }}</p>
                                        <p class="text-[12px] text-gray-500">
                                            {{ $k['penanganan'] }} &middot; {{ $k['kategori'] }}
                                        </p>
                                        <p class="text-[11px] text-gray-400">
                                            NIS {{ $k['nis'] }} &middot; Kelas {{ $k['kelas_label'] }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <p class="text-[13px] text-gray-400 italic">Belum ada kasus dipilih</p>
                            @endif
                        </div>
                    </div>

                    {{-- Detail Alih Tangan --}}
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-200">
                            <h4 class="text-[12px] font-bold text-gray-500 uppercase tracking-wider">Detail Alih Tangan</h4>
                        </div>
                        <div class="divide-y divide-gray-100">
                            {{-- Tanggal --}}
                            <div class="px-4 py-3 flex items-center justify-between">
                                <span class="text-[13px] text-gray-500">Tanggal Alih Tangan</span>
                                <span class="text-[13px] font-semibold text-gray-900">{{ $this->formattedTanggalAlih }}</span>
                            </div>
                            {{-- Guru Penerima --}}
                            <div class="px-4 py-3 flex items-center justify-between">
                                <span class="text-[13px] text-gray-500">Guru BK Penerima</span>
                                <span class="text-[13px] font-semibold text-gray-900">{{ $this->selectedGuruName }}</span>
                            </div>
                            {{-- Alasan --}}
                            <div class="px-4 py-3">
                                <span class="text-[13px] text-gray-500 block mb-1">Alasan Alih Tangan</span>
                                @if($alasan_alih)
                                    <p class="text-[13px] text-gray-900 leading-relaxed">{{ $alasan_alih }}</p>
                                @else
                                    <p class="text-[13px] text-gray-400 italic">Tidak ada alasan yang dituliskan</p>
                                @endif
                            </div>
                            {{-- Tindak Lanjut --}}
                            <div class="px-4 py-3">
                                <span class="text-[13px] text-gray-500 block mb-1">Tindak Lanjut</span>
                                @if($tindak_lanjut)
                                    <p class="text-[13px] text-gray-900 leading-relaxed">{{ $tindak_lanjut }}</p>
                                @else
                                    <p class="text-[13px] text-gray-400 italic">Tidak ada tindak lanjut yang dituliskan</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ============================================== --}}
            {{-- STEP 3: File Upload --}}
            {{-- ============================================== --}}
            @if($step === 3)
                <div class="space-y-5">
                    <p class="text-[13px] text-gray-500">
                        Unggah dokumen pendukung terkait proses alih tangan kasus (opsional). Format yang didukung: PDF, DOC, DOCX, JPG, PNG. Maksimal 5MB.
                    </p>

                    @if($fileName)
                        {{-- File Preview --}}
                        <div class="border border-teal-200 bg-teal-50 rounded-lg p-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-semibold text-gray-900 truncate">{{ $fileName }}</p>
                                <p class="text-[11px] text-gray-500">{{ $fileSize }}</p>
                            </div>
                            <button type="button" wire:click="removeFile"
                                class="text-red-400 hover:text-red-600 transition-colors p-1 shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    @else
                        {{-- Upload Zone --}}
                        <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-teal-400 transition-colors overflow-hidden">
                            <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                            </svg>
                            <p class="text-[13px] text-gray-500 mb-1">Seret file ke sini atau klik untuk memilih</p>
                            <p class="text-[11px] text-gray-400">PDF, DOC, DOCX, JPG, PNG &middot; Maks 5MB</p>
                            <input type="file" wire:model="file"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>

                        {{-- Alpine for drag-and-drop visual feedback --}}
                        @script
                        <script>
                            Livewire.on('close-modal', () => {
                                // Cleanup on modal close
                            });
                        </script>
                        @endscript
                    @endif

                    @if($fileError)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-[13px] text-red-700">{{ $fileError }}</span>
                        </div>
                    @endif

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-[12px] text-gray-500 leading-relaxed">
                            <span class="font-semibold text-gray-600">Catatan:</span> Step ini bersifat opsional. Anda bisa langsung menyimpan tanpa mengunggah dokumen. Dokumen dapat ditambahkan atau diperbarui nanti.
                        </p>
                    </div>
                </div>
            @endif

        </div>

        {{-- FOOTER BUTTONS --}}
        <div class="bg-bg-light px-7 py-5 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl gap-3">
            <x-atoms.button variant="secondary" size="md" x-on:click="show = false">Batal</x-atoms.button>

            @if($step === 1)
                <x-atoms.button variant="primary" size="md" wire:click="nextStep">
                    Selanjutnya
                    <svg class="w-4 h-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </x-atoms.button>
            @elseif($step === 2)
                <x-atoms.button variant="secondary" size="md" wire:click="prevStep">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </x-atoms.button>
                <x-atoms.button variant="primary" size="md" wire:click="nextStep">
                    Selanjutnya
                    <svg class="w-4 h-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </x-atoms.button>
            @else
                <x-atoms.button variant="secondary" size="md" wire:click="prevStep">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </x-atoms.button>
                <x-atoms.button variant="primary" size="md" wire:click="save" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Perbarui' : 'Simpan' }}
                    </span>
                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>
                </x-atoms.button>
            @endif
        </div>
    </div>

    {{-- MODAL PILIH KASUS --}}
    <div x-data="{ showKasusMenu: @entangle('showKasusModal') }" x-show="showKasusMenu"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        style="display: none;">
        <div class="bg-white w-full max-w-[600px] rounded-xl shadow-2xl flex flex-col max-h-[80vh] overflow-hidden"
            @click.away="showKasusMenu = false">
            <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0 flex justify-between items-center">
                <div>
                    <h2 class="text-[20px] font-bold text-gray-900 leading-tight">Pilih Kasus</h2>
                    <p class="text-[13px] text-gray-500 mt-0.5">Pilih kasus yang akan dialihkan</p>
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
                        <input type="text" wire:model.live="searchKasus" placeholder="Cari nama siswa, judul kasus, atau NIS..."
                            class="w-full border border-gray-200 rounded-md pl-10 pr-3 py-2 text-[13px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    @forelse($this->kasusOptions as $kasus)
                        <div wire:click="selectKasus({{ $kasus['id'] }})"
                            class="flex items-start gap-3 border border-gray-200 rounded-md p-4 cursor-pointer transition-colors
                                {{ $kasus_id == $kasus['id'] ? 'border-teal-400 bg-teal-50' : 'hover:border-primary hover:bg-bg-light' }}">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-[14px] font-bold text-gray-900">{{ $kasus['nama_siswa'] }}</h4>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ match($kasus['prioritas']) {
                                            'Tinggi' => 'bg-red-100 text-red-700',
                                            'Sedang' => 'bg-yellow-100 text-yellow-700',
                                            default => 'bg-green-100 text-green-700',
                                        } }}">
                                        {{ $kasus['prioritas'] }}
                                    </span>
                                </div>
                                <p class="text-[12px] text-gray-600 mt-0.5 font-medium">{{ $kasus['penanganan'] }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    {{ $kasus['kategori'] }} &middot; {{ $kasus['kelas_label'] }} &middot; {{ $kasus['tanggal_mulai'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 text-sm">
                            {{ $searchKasus ? 'Tidak ada kasus ditemukan.' : 'Tidak ada kasus yang tersedia untuk dialihkan.' }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-2.5">
                <button type="button" wire:click="closeKasusModal"
                    class="px-5 py-2 bg-white border border-gray-200 rounded-md text-[13px] font-bold text-gray-600 hover:bg-gray-50 transition-colors shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    </x-shared.modal>
</div>
