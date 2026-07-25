<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Services\SiswaService;
use App\Services\KonferensiKasusService;
use App\Services\LampiranService;

new class extends Component {
    use WithFileUploads;

    public ?int $editingId = null;
    public int $step = 1;

    // Form Fields
    public $siswa_id = '';
    public $tanggal_konferensi = '';
    public $tempat_pertemuan = '';

    // Peserta
    public array $peserta = [];

    // Lampiran
    public $newFiles = [];
    public array $existingLampiran = [];
    public array $deletedLampiran = [];

    public $searchSiswa = '';
    public $showStudentModal = false;

    public function mount()
    {
        $this->tanggal_konferensi = date('Y-m-d');
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $errors = [];

            if (empty($this->siswa_id)) {
                $errors['siswa_id'] = 'Pilih siswa terlebih dahulu.';
            }
            if (empty($this->tanggal_konferensi)) {
                $errors['tanggal_konferensi'] = 'Tanggal konferensi wajib diisi.';
            }
            if (empty($this->peserta)) {
                $errors['peserta'] = 'Minimal harus ada 1 peserta.';
            }

            foreach ($this->peserta as $idx => $p) {
                if (empty(trim($p['nama_peserta'] ?? ''))) {
                    $errors["peserta.$idx.nama_peserta"] = 'Nama peserta wajib diisi.';
                }
                if (empty(trim($p['peran_peserta'] ?? ''))) {
                    $errors["peserta.$idx.peran_peserta"] = 'Peran peserta wajib diisi.';
                }
            }

            if (!empty($errors)) {
                foreach ($errors as $key => $msg) {
                    $this->addError($key, $msg);
                }
                return;
            }

            $this->resetValidation();
            $this->step = 2;
        } elseif ($this->step === 2) {
            $this->step = 3;
        }
    }

    public function previousStep()
    {
        if ($this->step === 3) {
            $this->step = 2;
        } elseif ($this->step === 2) {
            $this->step = 1;
        }
    }

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

    // Peserta actions
    public function addPesertaRow()
    {
        $this->peserta[] = ['nama_peserta' => '', 'peran_peserta' => 'Lainnya'];
    }

    public function removePesertaRow(int $index)
    {
        unset($this->peserta[$index]);
        $this->peserta = array_values($this->peserta);
    }

    // Lampiran actions
    public function updatedNewFiles()
    {
        $this->validateOnly('newFiles.*', [
            'newFiles.*' => 'file|max:12288|mimes:pdf,jpg,png,jpeg,docx',
        ]);
    }

    public function removeNewFile($index)
    {
        unset($this->newFiles[$index]);
        $this->newFiles = array_values($this->newFiles);
    }

    public function removeExistingLampiran($index)
    {
        if (isset($this->existingLampiran[$index])) {
            $this->deletedLampiran[] = $this->existingLampiran[$index]['id'];
            unset($this->existingLampiran[$index]);
            $this->existingLampiran = array_values($this->existingLampiran);
        }
    }

    // CREATE
    #[On('create-konferensi-kasus')]
    public function createKonferensiKasus()
    {
        $this->resetValidation();
        $this->reset([
            'editingId', 'siswa_id', 'tempat_pertemuan', 'peserta',
            'searchSiswa', 'showStudentModal', 'newFiles',
            'existingLampiran', 'deletedLampiran',
        ]);
        $this->tanggal_konferensi = date('Y-m-d');
        $this->step = 1;
        $this->dispatch('open-modal', 'form-konferensi-kasus');
    }

    // EDIT
    #[On('edit-konferensi-kasus')]
    public function loadKonferensiKasus(int $id)
    {
        $this->resetValidation();
        $service = app(KonferensiKasusService::class);
        $record = $service->findById($id);

        $this->editingId = $id;
        $this->siswa_id = $record->kasus?->siswa_id;
        $this->tanggal_konferensi = $record->tanggal_konferensi
            ? \Carbon\Carbon::parse($record->tanggal_konferensi)->format('Y-m-d')
            : date('Y-m-d');
        $this->tempat_pertemuan = $record->tempat_pertemuan;

        $this->peserta = $record->peserta->map(fn($p) => [
            'nama_peserta' => $p->nama_peserta,
            'peran_peserta' => $p->peran_peserta,
        ])->toArray();

        // Load existing lampiran
        if ($record->kasus && $record->kasus->lampirans) {
            $this->existingLampiran = $record->kasus->lampirans->map(fn($l) => [
                'id' => $l->id,
                'nama_file' => $l->nama_file,
                'path_file' => $l->path_file,
                'tipe_file' => $l->tipe_file,
            ])->toArray();
        }

        $this->step = 1;
        $this->dispatch('open-modal', 'form-konferensi-kasus');
    }

    // SAVE
    public function save()
    {
        $service = app(KonferensiKasusService::class);

        $data = [
            'siswa_id' => $this->siswa_id,
            'tanggal_konferensi' => $this->tanggal_konferensi,
            'tempat_pertemuan' => $this->tempat_pertemuan ?: null,
        ];

        $pesertaData = array_map(fn($p) => [
            'nama_peserta' => $p['nama_peserta'],
            'peran_peserta' => $p['peran_peserta'],
        ], $this->peserta);

        $lampiranService = app(LampiranService::class);

        if ($this->editingId) {
            $record = $service->update($this->editingId, $data, $pesertaData);

            if (!empty($this->deletedLampiran)) {
                $lampiranService->deleteMultiple($this->deletedLampiran);
            }

            if (!empty($this->newFiles) && $record->kasus_id) {
                $lampiranService->storeLampirans($record->kasus_id, $this->newFiles, 'konferensi');
            }

            session()->flash('success', 'Konferensi kasus berhasil diperbarui!');
        } else {
            $record = $service->create($data, $pesertaData);

            if (!empty($this->newFiles) && $record->kasus_id) {
                $lampiranService->storeLampirans($record->kasus_id, $this->newFiles, 'konferensi');
            }

            session()->flash('success', 'Konferensi kasus berhasil ditambahkan!');
        }

        $this->reset([
            'editingId', 'siswa_id', 'tempat_pertemuan', 'peserta',
            'searchSiswa', 'showStudentModal', 'newFiles',
            'existingLampiran', 'deletedLampiran',
        ]);
        $this->tanggal_konferensi = date('Y-m-d');
        $this->step = 1;

        $this->dispatch('close-modal', 'form-konferensi-kasus');
        $this->dispatch('refreshTable');
    }
}; ?>

<div>
    <x-shared.modal name="form-konferensi-kasus" maxWidth="lg">
        <div class="flex flex-col h-full max-h-[80vh]">

            {{-- Modal Header --}}
            <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
                <h2 class="text-base font-bold text-gray-900 leading-tight">
                    {{ $editingId ? 'Edit Konferensi Kasus' : 'Tambah Konferensi Kasus' }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $editingId ? 'Perbarui data konferensi kasus' : 'Catat konferensi kasus baru' }}
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

            {{-- Modal Body --}}
            <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">

                {{-- ============================================================ --}}
                {{-- STEP 1: Data Utama --}}
                {{-- ============================================================ --}}
                <div class="{{ $step === 1 ? 'block' : 'hidden' }}">

                    {{-- SISWA --}}
                    <div class="mb-6">
                        <x-atoms.input-label for="siswa_id" size="sm">
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
                                            {{ $this->selectedStudent->nama_lengkap ?? $this->selectedStudent->nama }}
                                        </h3>
                                        <p class="text-[12px] text-gray-400 mt-0.5">
                                            Kelas {{ $this->selectedStudent->kelas_label }}
                                            {{ $this->selectedStudent->jurusan_label }} - NIS {{ $this->selectedStudent->nis }}
                                        </p>
                                    </div>
                                </div>
                                <button type="button" wire:click="openStudentModal"
                                    class="text-[13px] font-bold text-gray-500 hover:text-gray-800 transition-colors">
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
                                <button type="button" wire:click="openStudentModal"
                                    class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-md text-[13px] font-semibold transition-colors">
                                    Pilih Siswa
                                </button>
                            </div>
                        @endif
                        @error('siswa_id') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- PESERTA KONFERENSI --}}
                    <div class="mb-6">
                        <x-atoms.input-label for="peserta" size="sm">
                            Peserta Konferensi <span class="text-red-500">*</span>
                        </x-atoms.input-label>

                        @if(!empty($peserta))
                            <div class="flex flex-col gap-2 mb-3">
                                @foreach($peserta as $idx => $p)
                                    <div class="flex items-center gap-2 bg-teal-50 border border-teal-200 rounded-lg px-3 py-2">
                                        <div class="flex-1 min-w-0">
                                            <span class="font-medium text-teal-800 text-sm">{{ $p['nama_peserta'] }}</span>
                                            <span class="text-xs text-teal-500 ml-2">({{ $p['peran_peserta'] }})</span>
                                        </div>
                                        <button type="button" wire:click="removePesertaRow({{ $idx }})"
                                            class="text-teal-500 hover:text-red-500 transition-colors shrink-0">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error("peserta.$idx.nama_peserta")
                                        <span class="text-red-500 text-[12px] mt-0.5 block">{{ $message }}</span>
                                    @enderror
                                @endforeach
                            </div>
                        @endif

                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                            <div class="flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="block text-[12px] font-medium text-gray-500 mb-1">Nama Peserta</label>
                                    <input type="text" x-data
                                        x-ref="namaInput"
                                        x-on:keydown.enter.prevent="
                                            $wire.addPesertaRow();
                                            $nextTick(() => { if ($refs.namaInput) $refs.namaInput.value = ''; });
                                        "
                                        placeholder="Nama peserta..."
                                        class="w-full border border-gray-200 rounded-md px-3 py-2 text-[13px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                                    />
                                </div>
                                <div class="w-40">
                                    <label class="block text-[12px] font-medium text-gray-500 mb-1">Peran</label>
                                    <select x-ref="peranSelect"
                                        class="w-full border border-gray-200 rounded-md px-3 py-2 text-[13px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary bg-white">
                                        <option value="Guru BK">Guru BK</option>
                                        <option value="Wali Kelas">Wali Kelas</option>
                                        <option value="Kepala Sekolah">Kepala Sekolah</option>
                                        <option value="Orang Tua">Orang Tua</option>
                                        <option value="Siswa">Siswa</option>
                                        <option value="Lainnya" selected>Lainnya</option>
                                    </select>
                                </div>
                                <button type="button"
                                    x-on:click="
                                        nama = $refs.namaInput.value;
                                        peran = $refs.peranSelect.value;
                                        if (nama.trim() !== '') {
                                            idx = $wire.peserta.length;
                                            $wire.addPesertaRow();
                                            $wire.set('peserta.' + idx + '.nama_peserta', nama.trim());
                                            $wire.set('peserta.' + idx + '.peran_peserta', peran);
                                            $refs.namaInput.value = '';
                                        }
                                    "
                                    class="px-4 py-2 bg-teal-600 text-white rounded-md text-[13px] font-semibold hover:bg-teal-700 transition-colors shrink-0">
                                    Tambah
                                </button>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-2">Tekan Enter atau klik Tambah untuk menambah peserta</p>
                        </div>

                        @error('peserta')
                            <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- TANGGAL KONFERENSI --}}
                    <div class="mb-6">
                        <x-atoms.input-label for="tanggal_konferensi" size="sm">
                            Tanggal Konferensi <span class="text-red-500">*</span>
                        </x-atoms.input-label>
                        <x-atoms.text-input id="tanggal_konferensi" type="date" wire:model="tanggal_konferensi" size="md" />
                        @error('tanggal_konferensi')
                            <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- TEMPAT PERTEMUAN --}}
                    <div class="mb-6">
                        <x-atoms.input-label for="tempat_pertemuan" size="sm">
                            Tempat Pertemuan
                        </x-atoms.input-label>
                        <x-atoms.text-input id="tempat_pertemuan" wire:model="tempat_pertemuan" size="md"
                            placeholder="Misal: Ruang BK, Aula, dll." />
                        @error('tempat_pertemuan')
                            <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                        @enderror
                    </div>

                </div>{{-- END STEP 1 --}}

                {{-- ============================================================ --}}
                {{-- STEP 2: Review / Konfirmasi --}}
                {{-- ============================================================ --}}
                <div class="{{ $step === 2 ? 'block' : 'hidden' }}">

                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <h3 class="text-[15px] font-bold text-gray-900">Konfirmasi Data</h3>
                        </div>
                        <p class="text-[13px] text-gray-500 ml-10">Pastikan semua data yang dimasukkan sudah benar sebelum menyimpan.</p>
                    </div>

                    {{-- Siswa --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-3">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            <span class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide">Siswa</span>
                        </div>
                        @if($this->selectedStudent)
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-icon-bg text-primary rounded-full flex items-center justify-center font-bold text-[14px] shrink-0">
                                    {{ $this->getInitials($this->selectedStudent->nama_lengkap ?? $this->selectedStudent->nama) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[14px] font-bold text-gray-900">
                                        {{ $this->selectedStudent->nama_lengkap ?? $this->selectedStudent->nama }}
                                    </p>
                                    <p class="text-[12px] text-gray-400 mt-0.5">
                                        NIS {{ $this->selectedStudent->nis }} - Kelas {{ $this->selectedStudent->kelas_label }} {{ $this->selectedStudent->jurusan_label }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <p class="text-[13px] text-red-500 font-medium">Belum memilih siswa</p>
                        @endif
                    </div>

                    {{-- Peserta --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-3">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                            <span class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide">Peserta Konferensi</span>
                            <span class="text-[11px] text-gray-400 ml-auto">{{ count($peserta) }} orang</span>
                        </div>
                        @if(!empty($peserta))
                            <div class="flex flex-col gap-2">
                                @foreach($peserta as $p)
                                    <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-lg px-3 py-2.5">
                                        <div class="w-8 h-8 bg-teal-100 text-teal-700 rounded-full flex items-center justify-center text-[12px] font-bold shrink-0">
                                            {{ $this->getInitials($p['nama_peserta']) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[13px] font-semibold text-gray-800 truncate">{{ $p['nama_peserta'] }}</p>
                                        </div>
                                        <span class="text-[11px] font-medium text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full shrink-0">
                                            {{ $p['peran_peserta'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[13px] text-red-500 font-medium">Belum ada peserta</p>
                        @endif
                    </div>

                    {{-- Tanggal & Tempat --}}
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                <span class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide">Tanggal</span>
                            </div>
                            <p class="text-[14px] font-bold text-gray-900">
                                {{ $tanggal_konferensi ? \Carbon\Carbon::parse($tanggal_konferensi)->locale('id')->isoFormat('D MMMM YYYY') : '-' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                <span class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide">Tempat</span>
                            </div>
                            <p class="text-[14px] font-bold text-gray-900">
                                {{ $tempat_pertemuan ?: '-' }}
                            </p>
                            @if(!$tempat_pertemuan)
                                <p class="text-[11px] text-gray-400 mt-0.5">Tidak diisi</p>
                            @endif
                        </div>
                    </div>

                </div>{{-- END STEP 2 --}}

                {{-- ============================================================ --}}
                {{-- STEP 3: Lampiran --}}
                {{-- ============================================================ --}}
                <div class="{{ $step === 3 ? 'block' : 'hidden' }}">

                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            <h3 class="text-[15px] font-bold text-gray-900">Lampiran Pendukung</h3>
                        </div>
                        <p class="text-[13px] text-gray-500 ml-10">Unggah dokumen pendukung (opsional). Anda bisa langsung menyimpan tanpa lampiran.</p>
                    </div>

                    {{-- LAMPIRAN --}}
                    <div class="mb-6">

                        {{-- Existing Lampiran --}}
                        @if(!empty($existingLampiran))
                            <div class="mb-3 space-y-2">
                                <p class="text-[12px] font-medium text-gray-500">File Tersimpan:</p>
                                @foreach($existingLampiran as $idx => $l)
                                    <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                                        <div class="flex items-center gap-2 overflow-hidden">
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            <span class="text-sm text-gray-700 truncate">{{ $l['nama_file'] }}</span>
                                        </div>
                                        <button type="button" wire:click="removeExistingLampiran({{ $idx }})"
                                            class="text-red-400 hover:text-red-600 shrink-0 ml-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Upload New Files --}}
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-teal-400 hover:bg-gray-50 transition-colors"
                            x-data="{ isDropping: false }"
                            x-on:dragover.prevent="isDropping = true"
                            x-on:dragleave.prevent="isDropping = false"
                            x-on:drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                            x-on:click="$refs.fileInput.click()">
                            <input type="file" wire:model="newFiles" multiple x-ref="fileInput" class="hidden">
                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                            </svg>
                            <p class="text-sm text-gray-500">Klik atau tarik file ke sini (PDF, JPG, DOCX, maks 12MB)</p>
                            @error('newFiles.*') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- List new files --}}
                        @if(!empty($newFiles))
                            <div class="mt-3 space-y-2">
                                <p class="text-[12px] font-medium text-gray-500">File Baru:</p>
                                @foreach($newFiles as $idx => $file)
                                    <div class="flex items-center justify-between bg-teal-50 border border-teal-200 rounded-lg px-3 py-2">
                                        <span class="text-sm text-teal-800 truncate">{{ $file->getClientOriginalName() }}</span>
                                        <button type="button" wire:click="removeNewFile({{ $idx }})"
                                            class="text-red-400 hover:text-red-600 shrink-0 ml-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>{{-- END STEP 3 --}}

            </div>{{-- END MODAL BODY --}}

            {{-- Modal Footer --}}
            <div class="bg-bg-light px-7 py-5 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl gap-3">

                {{-- Step 1 Footer --}}
                <div class="{{ $step === 1 ? 'flex' : 'hidden' }} gap-3">
                    <x-atoms.button variant="secondary" size="md" x-on:click="show = false">Batal</x-atoms.button>
                    <x-atoms.button variant="primary" size="md" wire:click="nextStep">
                        Selanjutnya
                        <svg class="w-4 h-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </x-atoms.button>
                </div>

                {{-- Step 2 Footer --}}
                <div class="{{ $step === 2 ? 'flex' : 'hidden' }} gap-3">
                    <x-atoms.button variant="secondary" size="md" wire:click="previousStep">
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali
                    </x-atoms.button>
                    <x-atoms.button variant="primary" size="md" wire:click="nextStep">
                        Selanjutnya
                        <svg class="w-4 h-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </x-atoms.button>
                </div>

                {{-- Step 3 Footer --}}
                <div class="{{ $step === 3 ? 'flex' : 'hidden' }} gap-3">
                    <x-atoms.button variant="secondary" size="md" wire:click="previousStep">
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali
                    </x-atoms.button>
                    <x-atoms.button variant="primary" size="md" wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            {{ $editingId ? 'Perbarui' : 'Simpan' }}
                        </span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </x-atoms.button>
                </div>

            </div>
        </div>

        {{-- STUDENT SELECTION MODAL --}}
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
                        <p class="text-[13px] text-gray-500 mt-0.5">Pilih siswa untuk konferensi kasus</p>
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
                            <div wire:click="selectStudent({{ $siswa->id }})"
                                class="flex items-center gap-3 border border-gray-200 rounded-md p-4 cursor-pointer transition-colors
                                    {{ $siswa_id == $siswa->id ? 'border-teal-400 bg-teal-50' : 'hover:border-primary hover:bg-bg-light' }}">
                                <div class="flex-1">
                                    <h4 class="text-[14px] font-bold text-gray-900">
                                        {{ $siswa->nama_lengkap ?? $siswa->nama }}
                                    </h4>
                                    <p class="text-[12px] text-gray-500 mt-1">
                                        NIS: {{ $siswa->nis }} <span class="ml-2">Kelas: {{ $siswa->kelas_label }}</span>
                                    </p>
                                </div>
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
