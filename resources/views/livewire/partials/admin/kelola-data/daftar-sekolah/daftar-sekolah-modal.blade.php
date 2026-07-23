<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Services\SekolahService;

new class extends Component
{
    use WithFileUploads;

    public bool $show = false;
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
        $this->show = true;
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
        $this->show = true;
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

        $this->show = false;
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

<div>

@if($show)

<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl">

        {{-- Header --}}
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">
                {{ $editMode ? 'Edit Sekolah' : 'Tambah Sekolah' }}
            </h2>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-5">

            {{-- Nama Sekolah --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Nama Sekolah
                </label>

                <input
                    type="text"
                    wire:model="nama_sekolah"
                    class="w-full rounded-lg border-gray-300">

                @error('nama_sekolah')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

            </div>

            {{-- Alamat --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Alamat
                </label>

                <textarea
                    wire:model="alamat"
                    rows="3"
                    class="w-full rounded-lg border-gray-300"></textarea>

                @error('alamat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

            </div>

            {{-- Telepon --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Telepon
                </label>

                <input
                    type="text"
                    wire:model="telepon"
                    class="w-full rounded-lg border-gray-300">

                @error('telepon')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

            </div>

            {{-- Email --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Email
                </label>

                <input
                    type="email"
                    wire:model="email"
                    class="w-full rounded-lg border-gray-300">

                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

            </div>

            {{-- Logo --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Logo Sekolah
                </label>

                <input
                    type="file"
                    wire:model="logo"
                    class="w-full rounded-lg border-gray-300">

                @error('logo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

            </div>

        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t flex justify-end gap-3">

            <button
                wire:click="close"
                class="px-4 py-2 rounded-lg border">

                Batal

            </button>

            <button
                wire:click="save"
                class="px-4 py-2 rounded-lg bg-brand-teal text-white">

                {{ $editMode ? 'Simpan Perubahan' : 'Tambah Sekolah' }}

            </button>

        </div>

    </div>

</div>

@endif

</div>