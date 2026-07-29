<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Services\KonferensiKasusService;
use App\Services\LampiranService;

new class extends Component {
    use WithFileUploads;

    public ?int $editingId = null;
    public int $step = 1;

    // Form Fields
    public $kasus_id = '';
    public $tanggal_konferensi = '';
    public $tempat_pertemuan = '';

    // Peserta
    public array $peserta = [];

    // Lampiran
    public $newFiles = [];
    public array $existingLampiran = [];
    public array $deletedLampiran = [];

    public $searchKasus = '';
    public $showKasusModal = false;

    public function mount()
    {
        $this->tanggal_konferensi = date('Y-m-d');
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $errors = [];

            if (empty($this->kasus_id)) {
                $errors['kasus_id'] = 'Pilih kasus terlebih dahulu.';
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

    public function selectKasus(int $id)
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
    public function kasusOptions()
    {
        $options = app(\App\Services\KonferensiKasusService::class)->getKasusOptions();
        if (empty($this->searchKasus)) return $options;
        $needle = strtolower($this->searchKasus);
        return $options->filter(fn($k) =>
            str_contains(strtolower($k['nama_siswa']), $needle) ||
            str_contains(strtolower($k['penanganan']), $needle) ||
            str_contains(strtolower($k['nis']), $needle)
        )->values();
    }

    #[Computed]
    public function selectedKasus()
    {
        if (!$this->kasus_id) return null;
        return app(\App\Services\KonferensiKasusService::class)
            ->getKasusOptions()
            ->firstWhere('id', (int) $this->kasus_id);
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
    public function addPesertaRow(string $nama = '', string $peran = 'Lainnya')
    {
        $this->peserta[] = ['nama_peserta' => $nama, 'peran_peserta' => $peran];
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
            'editingId', 'kasus_id', 'tempat_pertemuan', 'peserta',
            'searchKasus', 'showKasusModal', 'newFiles',
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
        $this->kasus_id = $record->kasus_id;
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
    public function save(
        \App\Handlers\KonferensiKasus\CreateKonferensiKasusHandler $createHandler,
        \App\Handlers\KonferensiKasus\UpdateKonferensiKasusHandler $updateHandler,
    ) {
        $data = [
            'kasus_id' => $this->kasus_id,
            'tanggal_konferensi' => $this->tanggal_konferensi,
            'tempat_pertemuan' => $this->tempat_pertemuan ?: null,
        ];

        $pesertaData = array_map(fn($p) => [
            'nama_peserta' => $p['nama_peserta'],
            'peran_peserta' => $p['peran_peserta'],
        ], $this->peserta);

        $lampiranService = app(\App\Services\LampiranService::class);

        $result = $this->editingId
            ? $updateHandler->handle($data, ['id' => $this->editingId, 'peserta_data' => $pesertaData])
            : $createHandler->handle($data, ['peserta_data' => $pesertaData]);

        if ($result->success) {
            $record = $result->data;

            // Handle lampiran for update
            if ($this->editingId) {
                if (!empty($this->deletedLampiran)) {
                    $lampiranService->deleteMultiple($this->deletedLampiran);
                }
            }

            // Simpan lampiran baru
            if (!empty($this->newFiles) && $record->kasus_id) {
                $lampiranService->storeLampirans($record->kasus_id, $this->newFiles, 'konferensi');
            }

            session()->flash('success', $result->message);
            if ($result->eventClass) {
                event(new $result->eventClass(...$result->eventPayload));
            }
        } else {
            session()->flash('error', $result->message);
        }

        $this->reset([
            'editingId', 'kasus_id', 'tempat_pertemuan', 'peserta',
            'searchKasus', 'showKasusModal', 'newFiles',
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

                    {{-- KASUS PICKER --}}
                    <div class="mb-6">
                        <x-atoms.input-label for="kasus_id" size="sm">
                            Pilih Kasus <span class="text-red-500">*</span>
                        </x-atoms.input-label>

                        @if($this->selectedKasus)
                            <div class="flex items-center justify-between border border-gray-200 rounded-xl p-4 bg-gray-50">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-11 h-11 rounded-full bg-teal-500/10 text-teal-700 flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ strtoupper(substr($this->selectedKasus['nama_siswa'], 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $this->selectedKasus['nama_siswa'] }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $this->selectedKasus['penanganan'] }} &middot; {{ $this->selectedKasus['kategori'] }}</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">NIS {{ $this->selectedKasus['nis'] }} &middot; {{ $this->selectedKasus['kelas_label'] }}</p>
                                    </div>
                                </div>
                                <button type="button" wire:click="$set('kasus_id', '')" class="text-xs text-gray-400 hover:text-red-500 ml-2 shrink-0">Ganti</button>
                            </div>
                        @else
                            <button type="button" wire:click="openKasusModal"
                                class="w-full border border-dashed border-gray-300 rounded-xl p-4 text-left text-sm text-gray-500 hover:border-teal-400 hover:bg-teal-50/50 transition">
                                + Klik untuk memilih kasus
                            </button>
                        @endif
                        @error('kasus_id') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
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

                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4"
                             x-data="{ nama: '', peran: 'Lainnya' }">
                            <div class="flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="block text-[12px] font-medium text-gray-500 mb-1">Nama Peserta</label>
                                    <input type="text"
                                        x-model="nama"
                                        x-ref="namaInput"
                                        x-on:keydown.enter.prevent="
                                            if (nama.trim() !== '') {
                                                $wire.addPesertaRow(nama.trim(), peran);
                                                nama = '';
                                            }
                                        "
                                        placeholder="Nama peserta..."
                                        class="w-full border border-gray-200 rounded-md px-3 py-2 text-[13px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                                    />
                                </div>
                                <div class="w-40">
                                    <label class="block text-[12px] font-medium text-gray-500 mb-1">Peran</label>
                                    <select x-model="peran"
                                        class="w-full border border-gray-200 rounded-md px-3 py-2 text-[13px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary bg-white">
                                        <option value="Guru BK">Guru BK</option>
                                        <option value="Wali Kelas">Wali Kelas</option>
                                        <option value="Kepala Sekolah">Kepala Sekolah</option>
                                        <option value="Orang Tua">Orang Tua</option>
                                        <option value="Siswa">Siswa</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <button type="button"
                                    x-on:click="
                                        if (nama.trim() !== '') {
                                            $wire.addPesertaRow(nama.trim(), peran);
                                            nama = '';
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

                    {{-- Kasus --}}
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Kasus</p>
                        @if($this->selectedKasus)
                            <p class="text-[14px] text-gray-900 font-medium">{{ $this->selectedKasus['nama_siswa'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $this->selectedKasus['penanganan'] }} &middot; {{ $this->selectedKasus['kategori'] }}</p>
                        @else
                            <p class="text-[14px] text-gray-400">-</p>
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

        {{-- Kasus Modal --}}
        <div x-data="{ showKasusMenu: @entangle('showKasusModal') }"
            x-show="showKasusMenu"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            @click.away="showKasusMenu = false"
            style="display: none;">

            <div class="bg-white rounded-xl shadow-2xl w-full max-w-[600px] mx-4 max-h-[80vh] flex flex-col overflow-hidden"
                @click.stop>

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900 text-[15px]">Pilih Kasus</h3>
                    <button wire:click="closeKasusModal" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                </div>

                {{-- Search --}}
                <div class="px-5 pt-4 pb-2">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input type="text" wire:model.live="searchKasus"
                            placeholder="Cari nama siswa, judul kasus, atau NIS..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400" />
                    </div>
                </div>

                {{-- List --}}
                <div class="flex-1 overflow-y-auto px-5 pb-4 space-y-2">
                    @forelse($this->kasusOptions as $k)
                        <div wire:click="selectKasus({{ $k['id'] }})"
                            class="border border-gray-200 rounded-xl p-4 cursor-pointer hover:border-teal-400 hover:bg-teal-50/30 transition-all {{ $kasus_id == $k['id'] ? 'border-teal-400 bg-teal-50 shadow-sm' : '' }}">

                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-10 h-10 rounded-full bg-teal-500/10 text-teal-700 flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ strtoupper(substr($k['nama_siswa'], 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $k['nama_siswa'] }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $k['penanganan'] }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold shrink-0
                                    {{ match($k['prioritas']) {
                                        'Tinggi' => 'bg-red-100 text-red-700',
                                        'Sedang' => 'bg-yellow-100 text-yellow-700',
                                        default => 'bg-green-100 text-green-700',
                                    } }}">
                                    {{ $k['prioritas'] }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2 mt-2 ml-[52px] text-xs text-gray-400">
                                <span>{{ $k['kategori'] }}</span>
                                <span>&middot;</span>
                                <span>{{ $k['kelas_label'] }}</span>
                                <span>&middot;</span>
                                <span>NIS {{ $k['nis'] }}</span>
                                <span>&middot;</span>
                                <span>{{ $k['tanggal_mulai'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-sm text-gray-400">
                                @if(!empty($searchKasus)) Tidak ada kasus ditemukan. @else Tidak ada kasus yang tersedia. @endif
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Footer --}}
                <div class="px-5 py-3 border-t border-gray-100 flex justify-end">
                    <button wire:click="closeKasusModal"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </x-shared.modal>
</div>
