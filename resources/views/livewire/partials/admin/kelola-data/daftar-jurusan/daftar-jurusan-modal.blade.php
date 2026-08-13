<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Services\MasterData\JurusanService;
use App\Services\MasterData\SekolahService;

new class extends Component
{
    public bool $editMode = false;
    public ?int $jurusanId = null;

    public ?int $sekolah_id = null;
    public string $kode_jurusan = '';
    public string $nama_jurusan = '';

    public function rules()
    {
        return [
            'sekolah_id' => ['required', 'exists:sekolah,id'],
            'kode_jurusan' => ['required', 'string', 'max:20'],
            'nama_jurusan' => ['required', 'string', 'max:255'],
        ];
    }

    public function with(): array
    {
        return [
            'sekolahOptions' => app(SekolahService::class)->getAll(),
        ];
    }

    #[On('create-jurusan')]
    public function create()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->dispatch('open-modal', 'form-jurusan');
    }

    #[On('edit-jurusan')]
    public function edit($id)
    {
        $this->resetForm();
        $record = app(JurusanService::class)->findById($id);

        if (!$record) return;

        $this->jurusanId = $record->id;
        $this->sekolah_id = $record->sekolah_id;
        $this->kode_jurusan = $record->kode_jurusan;
        $this->nama_jurusan = $record->nama_jurusan;

        $this->editMode = true;
        $this->dispatch('open-modal', 'form-jurusan');
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->editMode) {
            app(JurusanService::class)->update($this->jurusanId, $validated);
            session()->flash('success', 'Data jurusan berhasil diperbarui.');
        } else {
            app(JurusanService::class)->create($validated);
            session()->flash('success', 'Data jurusan berhasil ditambahkan.');
        }

        $this->close();
    }

    public function close()
    {
        $this->resetForm();
        $this->dispatch('close-modal', 'form-jurusan');
    }

    private function resetForm()
    {
        $this->reset(['jurusanId', 'sekolah_id', 'kode_jurusan', 'nama_jurusan']);
        $this->resetValidation();
        $this->editMode = false;
    }
};

?>

<x-shared.modal name="form-jurusan" maxWidth="lg">
    <div class="flex flex-col h-full max-h-[80vh]">

        {{-- HEADER --}}
        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">
                {{ $editMode ? 'Edit Jurusan' : 'Tambah Jurusan' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $editMode ? 'Perbarui data jurusan' : 'Catat data jurusan baru' }}
            </p>
        </div>

        {{-- BODY --}}
        <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">
            <div class="space-y-5">
                {{-- Sekolah --}}
                <div>
                    <x-atoms.input-label for="sekolah_id" size="sm">
                        Sekolah <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <select id="sekolah_id" wire:model="sekolah_id" class="w-full border border-gray-200 rounded-md px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        <option value="">Pilih Sekolah</option>
                        @foreach($sekolahOptions as $sekolah)
                            <option value="{{ $sekolah->id }}">{{ $sekolah->nama_sekolah }}</option>
                        @endforeach
                    </select>
                    @error('sekolah_id')
                        <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kode Jurusan --}}
                <div>
                    <x-atoms.input-label for="kode_jurusan" size="sm">
                        Kode Jurusan <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <x-atoms.text-input id="kode_jurusan" wire:model="kode_jurusan" size="md" placeholder="e.g., RPL" />
                    @error('kode_jurusan')
                        <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Jurusan --}}
                <div>
                    <x-atoms.input-label for="nama_jurusan" size="sm">
                        Nama Jurusan <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <x-atoms.text-input id="nama_jurusan" wire:model="nama_jurusan" size="md" placeholder="e.g., Rekayasa Perangkat Lunak" />
                    @error('nama_jurusan')
                        <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-3">
            <x-atoms.button variant="secondary" wire:click="close">
                Batal
            </x-atoms.button>

            <x-atoms.button wire:click="save">
                {{ $editMode ? 'Simpan Perubahan' : 'Tambah Jurusan' }}
            </x-atoms.button>
        </div>
    </div>
</x-shared.modal>
