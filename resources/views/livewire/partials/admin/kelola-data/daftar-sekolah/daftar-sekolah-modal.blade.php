<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Services\MasterData\SekolahService;

new class extends Component
{
    use WithFileUploads;

    public bool $editMode = false;
    public ?int $sekolahId = null;

    public string $nama_sekolah = '';
    public string $alamat = '';
    public string $telepon = '';
    public string $email = '';
    public $logo = null;

    public function rules()
    {
        return [
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'alamat'       => ['required', 'string'],
            'telepon'      => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email'],
            'logo'         => ['nullable', 'image', 'max:2048'],
        ];
    }

    #[On('create-sekolah')]
    public function create()
    {
        $this->resetForm();

        $this->editMode = false;
        $this->dispatch('open-modal', 'form-sekolah');
    }

    #[On('edit-sekolah')]
    public function edit($id)
    {
        $record = app(SekolahService::class)->findById($id);

        if (!$record) {
            return;
        }

        $this->sekolahId = $record->id;
        $this->nama_sekolah = $record->nama_sekolah;
        $this->alamat = $record->alamat;
        $this->telepon = $record->telepon;
        $this->email = $record->email;

        $this->editMode = true;
        $this->dispatch('open-modal', 'form-sekolah');
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->logo) {
            $validated['logo'] = $this->logo->store('sekolah', 'public');
        }

        if ($this->editMode) {

            app(SekolahService::class)
                ->update($this->sekolahId, $validated);

            session()->flash(
                'success',
                'Data sekolah berhasil diperbarui.'
            );

        } else {

            app(SekolahService::class)
                ->create($validated);

            session()->flash(
                'success',
                'Data sekolah berhasil ditambahkan.'
            );
        }

        $this->close();
    }

    public function close()
    {
        $this->resetForm();

        $this->dispatch('close-modal', 'form-sekolah');
    }

    private function resetForm()
    {
        $this->reset([
            'sekolahId',
            'nama_sekolah',
            'alamat',
            'telepon',
            'email',
            'logo',
        ]);

        $this->resetValidation();

        $this->editMode = false;
    }
};

?>

<x-shared.modal name="form-sekolah" maxWidth="xl">
    <div class="flex flex-col h-full max-h-[80vh]">

        {{-- Header --}}
        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">
                {{ $editMode ? 'Edit Sekolah' : 'Tambah Sekolah' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $editMode ? 'Perbarui data sekolah' : 'Catat data sekolah baru' }}
            </p>
        </div>

        {{-- Body --}}
        <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">
            <div class="space-y-5">

                {{-- Nama Sekolah --}}
                <div>
                    <x-atoms.input-label for="nama_sekolah" size="sm">Nama Sekolah <span class="text-red-500">*</span></x-atoms.input-label>
                    <x-atoms.text-input id="nama_sekolah" wire:model="nama_sekolah" size="md" placeholder="e.g., SMK Negeri 1 Jakarta" />
                    @error('nama_sekolah')
                        <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alamat --}}
                <div>
                    <x-atoms.input-label for="alamat" size="sm">Alamat <span class="text-red-500">*</span></x-atoms.input-label>
                    <textarea id="alamat" wire:model="alamat" rows="3" class="w-full border border-gray-200 rounded-md px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" placeholder="e.g., Jl. Budi Utomo No.7"></textarea>
                    @error('alamat')
                        <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Telepon --}}
                    <div>
                        <x-atoms.input-label for="telepon" size="sm">Telepon</x-atoms.input-label>
                        <x-atoms.text-input id="telepon" wire:model="telepon" size="md" placeholder="e.g., 021-3844444" />
                        @error('telepon')
                            <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-atoms.input-label for="email" size="sm">Email</x-atoms.input-label>
                        <x-atoms.text-input id="email" type="email" wire:model="email" size="md" placeholder="e.g., info@smkn1jakarta.sch.id" />
                        @error('email')
                            <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Logo --}}
                <div>
                    <x-atoms.input-label for="logo" size="sm">Logo Sekolah</x-atoms.input-label>
                    <input id="logo" type="file" wire:model="logo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                    @error('logo')
                        <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-3">
            <x-atoms.button variant="secondary" wire:click="close">
                Batal
            </x-atoms.button>

            <x-atoms.button wire:click="save">
                {{ $editMode ? 'Simpan Perubahan' : 'Tambah Sekolah' }}
            </x-atoms.button>
        </div>

    </div>
</x-shared.modal>