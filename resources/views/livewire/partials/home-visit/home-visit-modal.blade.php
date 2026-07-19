<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use App\Models\DataSiswa;
use App\Services\SiswaService;
use App\Services\HomeVisitService;
use App\Services\LampiranService;

new class extends Component {
    use WithFileUploads;

    public ?int $editingId = null;
    public int $step = 1;

    // ── DATA KUNJUNGAN RUMAH (sesuai tabel home_visits) ──
    #[Validate('required|date')]
    public $tanggal_kunjungan = '';

    #[Validate('required|string')]
    public $uraian_masalah = '';

    #[Validate('required|string')]
    public $penanganan = '';

    #[Validate('nullable|string')]
    public $tindak_lanjut = '';

    #[Validate('required|in:diproses,ditunda,dibatalkan')]
    public $status = 'diproses';

    // ── SISWA ────────────────────────────────────────────
    #[Validate('required|integer')]
    public $siswa_id = '';

    // ── LAMPIRAN ─────────────────────────────────────────
    public $newFiles = [];
    public array $existingLampiran = [];
    public array $deletedLampiran = [];

    public $searchSiswa = '';
    public $showStudentModal = false;

    public function mount()
    {
        $this->tanggal_kunjungan = date('Y-m-d');
    }

    public function nextStep()
    {
        $this->validate();
        $this->step = 2;
    }

    public function previousStep()
    {
        $this->step = 1;
    }

    public function selectStudent($id)
    {
        $this->siswa_id = (int) $id;
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

    public function getInitials($name)
    {
        if (!$name) return 'S';
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    // ── LAMPIRAN ACTIONS ─────────────────────────────────
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

    // ── CREATE ───────────────────────────────────────────
    #[On('create-home-visit')]
    public function createHomeVisit()
    {
        $this->resetValidation();
        $this->reset([
            'editingId', 'siswa_id', 'uraian_masalah',
            'penanganan', 'tindak_lanjut', 'status',
            'newFiles', 'existingLampiran', 'deletedLampiran',
        ]);
        $this->tanggal_kunjungan = date('Y-m-d');
        $this->status = 'diproses';
        $this->step = 1;
        $this->dispatch('open-modal', 'form-home-visit');
    }

    // ── EDIT ─────────────────────────────────────────────
    #[On('edit-home-visit')]
    public function loadHomeVisit($id)
    {
        $service = app(HomeVisitService::class);
        $this->resetValidation();

        $record = $service->findById($id);

        $this->editingId = $record->id;
        $this->tanggal_kunjungan = \Carbon\Carbon::parse($record->tanggal_kunjungan)->format('Y-m-d');
        $this->uraian_masalah = $record->uraian_masalah;
        $this->penanganan = $record->penanganan;
        $this->tindak_lanjut = $record->tindak_lanjut;
        $this->status = $record->status;
        $this->siswa_id = $record->kasus?->siswa_id ?? '';

        // Load existing lampiran
        if ($record->kasus && $record->kasus->lampirans) {
            $this->existingLampiran = $record->kasus->lampirans->map(fn($l) => [
                'id' => $l->id,
                'nama_file' => $l->nama_file,
                'path_file' => $l->path_file,
                'tipe_file' => $l->tipe_file,
            ])->toArray();
        }

        $this->dispatch('open-modal', 'form-home-visit');
    }

    // ── SAVE ─────────────────────────────────────────────
    public function save()
    {
        $this->validate();

        $data = [
            'siswa_id' => $this->siswa_id,
            'tanggal_kunjungan' => $this->tanggal_kunjungan,
            'uraian_masalah' => $this->uraian_masalah,
            'penanganan' => $this->penanganan,
            'tindak_lanjut' => $this->tindak_lanjut,
            'status' => $this->status,
        ];

        $service = app(HomeVisitService::class);
        $lampiranService = app(LampiranService::class);

        if ($this->editingId) {
            $record = $service->update($this->editingId, $data);

            // Hapus lampiran yang ditandai
            if (!empty($this->deletedLampiran)) {
                $lampiranService->deleteMultiple($this->deletedLampiran);
            }

            // Simpan lampiran baru
            if (!empty($this->newFiles) && $record->kasus_id) {
                $lampiranService->storeLampirans($record->kasus_id, $this->newFiles, 'kunjungan');
            }

            session()->flash('success', 'Kunjungan Rumah berhasil diperbarui.');
        } else {
            $record = $service->create($data);

            // Simpan lampiran
            if (!empty($this->newFiles) && $record->kasus_id) {
                $lampiranService->storeLampirans($record->kasus_id, $this->newFiles, 'kunjungan');
            }

            session()->flash('success', 'Kunjungan Rumah berhasil ditambahkan.');
        }

        $this->reset([
            'editingId', 'siswa_id', 'uraian_masalah',
            'penanganan', 'tindak_lanjut',
            'newFiles', 'existingLampiran', 'deletedLampiran',
        ]);
        $this->tanggal_kunjungan = date('Y-m-d');
        $this->status = 'diproses';
        $this->step = 1;

        $this->dispatch('close-modal', 'form-home-visit');
        $this->dispatch('refreshTable');
    }
}; ?>

<div>
    <x-shared.modal name="form-home-visit" maxWidth="lg">
    <div class="flex flex-col h-full max-h-[80vh]">

        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">
                {{ $editingId ? 'Edit Kunjungan Rumah' : 'Tambah Kunjungan Rumah' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $editingId ? 'Perbarui data kunjungan rumah' : 'Catat hasil kunjungan rumah baru' }}
            </p>
        </div>

        <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">

            {{-- Step Progress --}}
            @if(!$editingId)
            <div class="mb-6">
                <p class="text-[14px] font-bold text-primary mb-2.5">Langkah {{ $step }} Dari 2</p>
                <div class="flex gap-2.5">
                    <div class="h-2.5 w-1/2 {{ $step >= 1 ? 'bg-primary' : 'bg-gray-200/80' }} rounded-full"></div>
                    <div class="h-2.5 w-1/2 {{ $step >= 2 ? 'bg-primary' : 'bg-gray-200/80' }} rounded-full"></div>
                </div>
            </div>
            @endif

            {{-- STEP 1: Data Utama --}}
            <div class="{{ $step === 1 || $editingId ? 'block' : 'hidden' }}">

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

            {{-- TANGGAL KUNJUNGAN --}}
            <div class="mb-6">
                <x-atoms.input-label for="tanggal_kunjungan" size="sm">
                    Tanggal Kunjungan <span class="text-red-500">*</span>
                </x-atoms.input-label>
                <x-atoms.text-input id="tanggal_kunjungan" type="date" wire:model="tanggal_kunjungan" size="md" />
                @error('tanggal_kunjungan') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
            </div>

            {{-- PENANGANAN (judul) --}}
            <div class="mb-6">
                <x-atoms.input-label for="penanganan" size="sm">
                    Judul / Penanganan <span class="text-red-500">*</span>
                </x-atoms.input-label>
                <x-atoms.text-input id="penanganan" wire:model="penanganan" size="md" placeholder="Masukkan judul kunjungan..." />
                @error('penanganan') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
            </div>

            {{-- URAIAN MASALAH (hasil kunjungan) --}}
            <div class="mb-6">
                <x-atoms.input-label for="uraian_masalah" size="sm">
                    Hasil Kunjungan <span class="text-red-500">*</span>
                </x-atoms.input-label>
                <textarea id="uraian_masalah" wire:model="uraian_masalah" rows="3"
                    class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                    placeholder="Tuliskan hasil kunjungan rumah..."></textarea>
                @error('uraian_masalah') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
            </div>

            {{-- TINDAK LANJUT --}}
            <div class="mb-6">
                <x-atoms.input-label for="tindak_lanjut" size="sm">
                    Tindak Lanjut
                </x-atoms.input-label>
                <textarea id="tindak_lanjut" wire:model="tindak_lanjut" rows="2"
                    class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                    placeholder="Tuliskan tindak lanjut (opsional)..."></textarea>
                @error('tindak_lanjut') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
            </div>

            {{-- STATUS --}}
            <div class="mb-6">
                <x-atoms.input-label for="status" size="sm">
                    Status <span class="text-red-500">*</span>
                </x-atoms.input-label>
                <select id="status" wire:model="status"
                    class="w-full border border-gray-200 rounded-md px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    <option value="diproses">Diproses</option>
                    <option value="ditunda">Ditunda</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
                @error('status') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
            </div>

            </div>{{-- END STEP 1 --}}

            {{-- STEP 2: Lampiran --}}
            <div class="{{ $step === 2 || $editingId ? 'block' : 'hidden' }}">

            {{-- LAMPIRAN --}}
            <div class="mb-6">
                <x-atoms.input-label for="file" size="sm">
                    Lampiran Pendukung
                </x-atoms.input-label>

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

            </div>{{-- END STEP 2 --}}

        </div>

        {{-- FOOTER ACTIONS --}}
        <div class="bg-bg-light px-7 py-5 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl gap-3">
            @if(!$editingId)
            <div class="{{ $step === 1 ? 'flex' : 'hidden' }} gap-3">
                <x-atoms.button variant="secondary" size="md" x-on:click="show = false">Batal</x-atoms.button>
                <x-atoms.button wire:click="nextStep">Lanjut ke Lampiran</x-atoms.button>
            </div>
            <div class="{{ $step === 2 ? 'flex' : 'hidden' }} gap-3">
                <x-atoms.button variant="secondary" size="md" wire:click="previousStep">Kembali</x-atoms.button>
                <x-atoms.button wire:click="save" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Simpan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </x-atoms.button>
            </div>
            @else
            <div class="flex gap-3">
                <x-atoms.button variant="secondary" size="md" x-on:click="show = false">Batal</x-atoms.button>
                <x-atoms.button wire:click="save" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Perbarui</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </x-atoms.button>
            </div>
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
                        <input type="text" wire:model.live="searchSiswa" placeholder="Cari Nama Atau NIS"
                            class="w-full border border-gray-200 rounded-md pl-10 pr-3 py-2 text-[13px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">
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
                <button type="button" wire:click="closeStudentModal"
                    class="px-5 py-2 bg-white border border-gray-200 rounded-md text-[13px] font-bold text-gray-600 hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
    </x-shared.modal>
</div>
