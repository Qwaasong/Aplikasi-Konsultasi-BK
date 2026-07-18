<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use App\Services\SiswaService;
use App\Services\KonsultasiService;
use App\Services\BimbinganIndividuService;
use App\Models\KategoriKasus;
use App\Models\TahunAjaran;

new class extends Component {
    use WithFileUploads;

    public int $step = 1;
    public ?int $editingId = null;

    #[Validate('required|integer')]
    public $siswa_id = '';

    #[Validate('required|integer')]
    public $tahun_ajaran_id = '';

    #[Validate('required|date')]
    public $tanggal_layanan = '';

    #[Validate('required|string|max:255')]
    public $topik = '';

    #[Validate('required|string')]
    public $tujuan = '';

    #[Validate('required|string')]
    public $hasil_tindak_lanjut = '';

    #[Validate([
        'files' => 'array|max:5',
        'files.*' => 'file|max:12288|mimes:pdf,jpg,png,docx',
    ])]
    public $files = [];
    public $newFiles = [];

    public $searchSiswa = '';
    public $showStudentModal = false;

    public function mount()
    {
        $this->tanggal_layanan = date('Y-m-d');
        $this->tahun_ajaran_id = TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id')
            ?? '';
    }

    public function nextStep()
    {
        $this->validate([
            'tahun_ajaran_id'     => 'required|integer',
            'tanggal_layanan'     => 'required|date',
            'topik'               => 'required|string|max:255',
            'tujuan'              => 'required|string',
            'hasil_tindak_lanjut' => 'required|string',
        ]);
        $this->step = 2;
    }

    public function previousStep()
    {
        $this->step = 1;
    }

    public function selectStudent($id)
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

    public function updatedNewFiles()
    {
        $this->validateOnly('newFiles.*', [
            'newFiles.*' => 'file|max:12288|mimes:pdf,jpg,png,docx',
        ]);

        foreach ($this->newFiles as $file) {
            if (count($this->files) < 5) {
                $this->files[] = $file;
            }
        }
        $this->newFiles = [];
    }

    public function removeFile($index)
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files); 
    }

    #[Computed]
    public function selectedStudent()
    {
        if (!$this->siswa_id) return null;
        return app(SiswaService::class)->findById($this->siswa_id);
    }

    #[Computed]
    public function kategoriOptions()
    {
        $options = KategoriKasus::all()->map(fn($item) => [
            'value' => $item->id,
            'label' => $item->nama_kategori,
        ])->toArray();

        array_unshift($options, ['value' => '', 'label' => 'Pilih Kategori (Opsional)']);
        return $options;
    }

    #[Computed]
    public function filteredStudents()
    {
        return app(SiswaService::class)->search($this->searchSiswa, 50);
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

    public function removeExistingFile($index)
    {
        unset($this->existingFiles[$index]);
        $this->existingFiles = array_values($this->existingFiles);
    }

    // ── EVENT UNTUK MODE TAMBAH ─────────────────────────────────
    #[On('create-bimbingan-individu')]
    public function createBimbinganIndividu()
    {
        $this->resetValidation();
        $this->reset([
            'editingId',
            'tahun_ajaran_id',
            'topik',
            'tujuan',
            'hasil_tindak_lanjut',
        ]);
        
        $this->tanggal_layanan = date('Y-m-d');
        $this->step = 1;

        $this->dispatch('open-modal', 'form-bimbingan-individu');
    }

    // ── EVENT UNTUK MODE EDIT ───────────────────────────────────
    #[On('edit-bimbingan-individu')]
    public function loadBimbinganIndividu($id)
    {
        $service = app(BimbinganIndividuService::class);
        $this->resetValidation();

        $record = $service->findById($id);

        $this->editingId = $id;
        $this->tahun_ajaran_id = $record->tahun_ajaran_id;

        $this->tanggal_layanan = \Carbon\Carbon::parse(
            $record->tanggal_layanan
        )->format('Y-m-d');

        $this->topik = $record->topik;
        $this->tujuan = $record->tujuan;
        $this->hasil_tindak_lanjut = $record->hasil_tindak_lanjut;
            $this->step = 1;

            $this->dispatch('open-modal', 'form-bimbingan-individu');
        }

    // ── SIMPAN (CREATE ATAU UPDATE) ─────────────────────────────
    public function save(BimbinganIndividuService $service)
    {
        $this->validate();

        $data = [
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
            'tanggal_layanan' => $this->tanggal_layanan,
            'topik' => $this->topik,
            'tujuan' => $this->tujuan,
            'hasil_tindak_lanjut' => $this->hasil_tindak_lanjut,
        ];

        if ($this->editingId) {
            $service->update($this->editingId, $data, $this->existingFiles, $this->files);
            session()->flash('success', 'Layanan Konseling Individu  berhasil diperbarui!');
        } else {
            $service->create($data, $this->files);
            session()->flash('success', 'Layanan Konseling Individu berhasil ditambahkan!');
        }

        $this->reset([
            'editingId',
            'tahun_ajaran_id',
            'topik',
            'tujuan',
            'hasil_tindak_lanjut',
        ]);
        $this->tanggal_layanan = date('Y-m-d');
        $this->step = 1;

        $this->dispatch('close-modal', 'form-bimbingan-individu');
        $this->dispatch('refreshTable');
    }
}; ?>

<div>
    {{-- Perhatikan nama modalnya sekarang menjadi "form-bimbingan-individu" --}}
    <x-shared.modal name="form-bimbingan-individu" maxWidth="lg">
    <div class="flex flex-col h-full max-h-[80vh]">
        
        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">
                {{ $editingId ? 'Edit Layanan Konseling Individu' : 'Tambah Layanan Konseling Individu' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $editingId ? 'Perbarui data Layanan Konseling Individu siswa' : 'Catat layanan Layanan Konseling Individu siswa baru' }}
            </p>
        </div>

        <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">

            <div class="{{ $step === 1 ? 'block' : 'hidden' }}">
                <div class="mb-6">
                    <p class="text-[14px] font-bold text-primary mb-2.5">Langkah 1 Dari 2</p>
                    <div class="flex gap-2.5">
                        <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>
                        <div class="h-2.5 w-1/2 bg-gray-200/80 rounded-full"></div>
                    </div>
                </div>

                <div class="mb-6">
                    <x-atoms.input-label for="id_siswa" size="sm">Siswa</x-atoms.input-label>
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

                    {{-- Tanggal Layanan --}}
                    <div class="mb-6">
                        <x-atoms.input-label for="tanggal_layanan" size="sm">
                            Tanggal Layanan
                        </x-atoms.input-label>

                        <x-atoms.text-input
                            id="tanggal_layanan"
                            type="date"
                            wire:model="tanggal_layanan"
                            size="md" />

                        @error('tanggal_layanan')
                        <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>

                    {{-- Tahun Ajaran --}}
                    <div class="mb-6">
                        <x-atoms.input-label for="tahun_ajaran_id" size="sm">
                            Tahun Ajaran
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
                        <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>

                    {{-- Topik --}}
                    <div class="mb-6">
                        <x-atoms.input-label for="topik" size="sm">
                            Topik
                        </x-atoms.input-label>

                        <x-atoms.text-input
                            id="topik"
                            wire:model="topik"
                            size="md"
                            placeholder="Contoh : Bullying di Sekolah" />

                        @error('topik')
                        <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>

                    {{-- Tujuan --}}
                    <div class="mb-6">
                        <x-atoms.input-label for="tujuan" size="sm">
                           Tujuan Layanan
                        </x-atoms.input-label>

                        <textarea
                            id="tujuan"
                            wire:model="tujuan"
                            rows="3"
                            class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                            placeholder="Tuliskan tujuan layanan kelompok..."></textarea>

                        @error('tujuan')
                        <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>

                    {{-- Hasil dan Tindak Lanjut --}}
                    <div class="mb-6">
                        <x-atoms.input-label for="hasil_tindak_lanjut" size="sm">
                           Hasil dan Tindak Lanjut
                        </x-atoms.input-label>

                        <textarea
                            id="hasil_tindak_lanjut"
                            wire:model="hasil_tindak_lanjut"
                            rows="3"
                            class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                            placeholder="Tuliskan hasil layanan dan tindak lanjut..."></textarea>

                        @error('hasil_tindak_lanjut')
                        <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>

            <div class="{{ $step === 2 ? 'block' : 'hidden' }}">
                <div class="mb-6">
                    <p class="text-[14px] font-bold text-primary mb-2.5">Langkah 2 Dari 2</p>
                    <div class="flex gap-2.5">
                        <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>
                        <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-[14px] font-bold text-gray-700 mb-2">Pilih File Tambahan</label>
                    <div x-data="{ isDropping: false }" x-on:dragover.prevent="isDropping = true" x-on:dragleave.prevent="isDropping = false" x-on:drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))" x-on:click="$refs.fileInput.click()" class="bg-bg-light border-2 py-16 px-6 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-[#e9f3f5] transition-colors border-dashed rounded-xl" :class="isDropping ? 'bg-[#e9f3f5] border-primary' : 'border-icon-bg/40'">
                        <input type="file" wire:model="newFiles" multiple x-ref="fileInput" class="hidden">
                        <div class="w-[84px] h-[84px] bg-icon-bg/90 rounded-full flex items-center justify-center mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#044B5F" class="w-10 h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                            </svg>
                        </div>
                        <h3 class="text-[16px] font-bold text-gray-800 mb-2">Tarik Dan Lepas file disini atau Klik untuk Unggah</h3>
                        <p class="text-[14px] text-gray-400">Hingga 5 file , Maks 12 MB per file (PDF, JPG, PNG, DOCX)</p>
                    </div>
                    @error('files.*') <span class="text-red-500 text-[13px] font-medium mt-2 block">{{ $message }}</span> @enderror
                </div>

                @if($files && count($files) > 0)
                    <div class="mb-4">
                        <h4 class="text-[14.5px] font-bold text-gray-700 mb-3">File Baru Terunggah</h4>
                        <div class="flex flex-col gap-3">
                            @foreach($files as $index => $fileObj)
                                <div class="border border-gray-200 rounded-xl p-4 flex items-center justify-between bg-white shadow-sm">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7">
                                                <path d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z" />
                                                <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
                                            </svg>
                                        </div>
                                        <div class="flex flex-col overflow-hidden max-w-[320px]">
                                            <p class="text-[15px] font-bold text-gray-800 truncate">{{ $fileObj->getClientOriginalName() }}</p>
                                            <p class="text-[13.5px] text-gray-500 font-medium tracking-wide">{{ round($fileObj->getSize() / 1024 / 1024, 2) }} MB</p>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removeFile({{ $index }})" class="text-gray-400 hover:text-red-500 transition-colors p-2 shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($existingFiles))
                    <div class="mb-4">
                        <h4 class="text-[14.5px] font-bold text-gray-700 mb-3">File Lama Tersimpan</h4>
                        <div class="flex flex-col gap-3">
                            @foreach($existingFiles as $index => $path)
                                @php
                                    $fileName = basename($path);
                                    $isImage = in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']);
                                    $fileUrl = asset('storage/' . $path);
                                @endphp
                                <div class="border border-gray-200 rounded-xl p-4 flex items-center justify-between bg-white shadow-sm hover:border-blue-300 transition-colors">
                                    <div class="flex items-center gap-4">
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
                                            <p class="text-[15px] font-bold text-gray-800 truncate"><a href="{{ $fileUrl }}" target="_blank" class="hover:text-blue-600 transition-colors">{{ $fileName }}</a></p>
                                            <p class="text-[13.5px] text-gray-400 font-medium tracking-wide">{{ $isImage ? 'Gambar' : 'Dokumen' }} • Tersimpan</p>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removeExistingFile({{ $index }})" class="text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors p-2 shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <div class="bg-bg-light px-7 py-5 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl">
            <div class="{{ $step === 1 ? 'flex' : 'hidden' }} gap-3">
                <x-atoms.button variant="secondary" size="md" x-on:click="show = false">Batal</x-atoms.button>
                <x-atoms.button wire:click="nextStep">Langkah Terakhir : Upload File</x-atoms.button>
            </div>

            <div class="{{ $step === 2 ? 'flex' : 'hidden' }} gap-3">
                <x-atoms.button variant="secondary" size="md" wire:click="previousStep">Kembali</x-atoms.button>
                <x-atoms.button wire:click="save">
                    {{ $editingId ? 'Perbarui Layanan Konseling Individu' : 'Simpan Layanan Konseling Individu' }}
                </x-atoms.button>
            </div>
        </div>
    </div>

    <div x-data="{ showStudentMenu: @entangle('showStudentModal') }" x-show="showStudentMenu"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
        <div class="bg-white w-full max-w-[500px] rounded-xl shadow-2xl flex flex-col max-h-[80vh] overflow-hidden" @click.away="showStudentMenu = false">
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
                        <input type="text" wire:model.live="searchSiswa" placeholder="Cari Nama Atau NIS" class="w-full border border-gray-200 rounded-md pl-10 pr-3 py-2 text-[13px] text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    @forelse($this->filteredStudents as $siswa)
                        <div wire:click="selectStudent({{ $siswa->id }})" class="student-card border border-gray-200 rounded-md p-4 cursor-pointer hover:border-primary hover:bg-bg-light transition-colors {{ $siswa_id == $siswa->id ? 'border-primary bg-bg-light' : '' }}">
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