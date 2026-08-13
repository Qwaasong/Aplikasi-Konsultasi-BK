<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Services\MasterData\KelasService;
use App\Services\MasterData\JurusanService;
use App\Services\MasterData\SekolahService;
use App\Services\User\PegawaiService;

new class extends Component
{
    public bool $editMode = false;
    public ?int $kelasId = null;

    public ?int $sekolah_id = null;
    public ?int $jurusan_id = null;
    public string $nama_kelas = '';
    public string $tingkat = '';
    public ?int $wali_kelas_id = null;

    public function rules()
    {
        return [
            'jurusan_id'     => ['required', 'exists:jurusan,id'],
            'nama_kelas'     => ['required', 'string', 'max:100'],
            'tingkat'        => ['required', 'string'],
            'wali_kelas_id'  => ['nullable', 'exists:pegawai,id'],
        ];
    }

    public function with(): array
    {
        return [
            'sekolahOptions' => app(SekolahService::class)->getAll(),
            'jurusanOptions' => app(JurusanService::class)->getAll(),
            'pegawaiOptions' => app(PegawaiService::class)->getAll(),
        ];
    }

    #[On('create-kelas')]
    public function create()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->dispatch('open-modal', 'form-kelas');
    }

    #[On('edit-kelas')]
    public function edit($id)
    {
        $this->resetForm();
        $record = app(KelasService::class)->findById($id);

        if (!$record) return;

        $this->kelasId = $record->id;
        $this->jurusan_id = $record->jurusan_id;
        $this->nama_kelas = $record->nama_kelas;
        $this->tingkat = $record->tingkat;
        $this->wali_kelas_id = $record->wali_kelas_id;
        $this->sekolah_id = $record->jurusan?->sekolah_id;

        $this->editMode = true;
        $this->dispatch('open-modal', 'form-kelas');
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->editMode) {
            app(KelasService::class)->update($this->kelasId, $validated);
            session()->flash('success', 'Data kelas berhasil diperbarui.');
        } else {
            app(KelasService::class)->create($validated);
            session()->flash('success', 'Data kelas berhasil ditambahkan.');
        }

        $this->close();
    }

    public function close()
    {
        $this->resetForm();
        $this->dispatch('close-modal', 'form-kelas');
    }

    private function resetForm()
    {
        $this->reset(['kelasId', 'sekolah_id', 'jurusan_id', 'nama_kelas', 'tingkat', 'wali_kelas_id']);
        $this->resetValidation();
        $this->editMode = false;
    }
};

?>

<x-shared.modal name="form-kelas" maxWidth="xl">
    <div class="flex flex-col h-full max-h-[80vh]">

        {{-- HEADER --}}
        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">
                {{ $editMode ? 'Edit Kelas' : 'Tambah Kelas' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $editMode ? 'Perbarui data kelas' : 'Catat data kelas baru' }}
            </p>
        </div>

        {{-- BODY --}}
        <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">
            <div class="space-y-5">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Sekolah --}}
                    <div>
                        <x-atoms.input-label for="sekolah_id_kelas" size="sm">Sekolah <span class="text-red-500">*</span></x-atoms.input-label>
                        <select id="sekolah_id_kelas" wire:model.live="sekolah_id" class="w-full border border-gray-200 rounded-md px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            <option value="">Pilih Sekolah</option>
                            @foreach($sekolahOptions as $sekolah)
                                <option value="{{ $sekolah->id }}">{{ $sekolah->nama_sekolah }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Jurusan --}}
                    <div>
                        <x-atoms.input-label for="jurusan_id_kelas" size="sm">Jurusan <span class="text-red-500">*</span></x-atoms.input-label>
                        <select id="jurusan_id_kelas" wire:model="jurusan_id" class="w-full border border-gray-200 rounded-md px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            <option value="">Pilih Jurusan</option>
                            @foreach($jurusanOptions->where('sekolah_id', $sekolah_id) as $jurusan)
                                <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                            @endforeach
                        </select>
                        @error('jurusan_id')
                            <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Nama Kelas --}}
                <div>
                    <x-atoms.input-label for="nama_kelas" size="sm">Nama Kelas <span class="text-red-500">*</span></x-atoms.input-label>
                    <x-atoms.text-input id="nama_kelas" wire:model="nama_kelas" size="md" placeholder="e.g., XII RPL 1" />
                    @error('nama_kelas')
                        <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Tingkat --}}
                    <div>
                        <x-atoms.input-label for="tingkat" size="sm">Tingkat <span class="text-red-500">*</span></x-atoms.input-label>
                        <select id="tingkat" wire:model="tingkat" class="w-full border border-gray-200 rounded-md px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            <option value="">Pilih Tingkat</option>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>
                        @error('tingkat')
                            <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Wali Kelas --}}
                    <div>
                        <x-atoms.input-label for="wali_kelas_id" size="sm">Wali Kelas</x-atoms.input-label>
                        <select id="wali_kelas_id" wire:model="wali_kelas_id" class="w-full border border-gray-200 rounded-md px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            <option value="">Pilih Wali Kelas</option>
                            @foreach($pegawaiOptions as $pegawai)
                                <option value="{{ $pegawai->id }}">{{ $pegawai->user?->nama }}</option>
                            @endforeach
                        </select>
                        @error('wali_kelas_id')
                            <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- FOOTER --}}
        <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-3">
            <x-atoms.button variant="secondary" wire:click="close">
                Batal
            </x-atoms.button>

            <x-atoms.button wire:click="save">
                {{ $editMode ? 'Simpan Perubahan' : 'Tambah Kelas' }}
            </x-atoms.button>
        </div>

    </div>
</x-shared.modal>
