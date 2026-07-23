<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Services\JurusanService;
use App\Services\SekolahService;

new class extends Component
{
    public bool $show = false;
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
        $this->show = true;
    }

    #[On('edit-jurusan')]
    public function edit($id)
    {
        $record = app(JurusanService::class)->findById($id);

        if (!$record) return;

        $this->jurusanId = $record->id;
        $this->sekolah_id = $record->sekolah_id;
        $this->kode_jurusan = $record->kode_jurusan;
        $this->nama_jurusan = $record->nama_jurusan;

        $this->editMode = true;
        $this->show = true;
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->editMode) {

            app(JurusanService::class)
                ->update($this->jurusanId, $validated);

            session()->flash('success', 'Data jurusan berhasil diperbarui.');

        } else {

            app(JurusanService::class)
                ->create($validated);

            session()->flash('success', 'Data jurusan berhasil ditambahkan.');
        }

        $this->close();
    }

    public function close()
    {
        $this->resetForm();

        $this->show = false;

        $this->dispatch('close-modal');
    }

    private function resetForm()
    {
        $this->reset([
            'jurusanId',
            'sekolah_id',
            'kode_jurusan',
            'nama_jurusan',
        ]);

        $this->resetValidation();

        $this->editMode = false;
    }
};

?>

<div>

@if($show)

<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">

        {{-- Header --}}
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">
                {{ $editMode ? 'Edit Jurusan' : 'Tambah Jurusan' }}
            </h2>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-5">

            {{-- Sekolah --}}
            <div>
                <label class="block text-sm font-medium mb-2">
                    Sekolah
                </label>

                <select
                    wire:model="sekolah_id"
                    class="w-full rounded-lg border-gray-300">

                    <option value="">
                        Pilih Sekolah
                    </option>

                    @foreach($sekolahOptions as $sekolah)

                        <option value="{{ $sekolah->id }}">
                            {{ $sekolah->nama_sekolah }}
                        </option>

                    @endforeach

                </select>

                @error('sekolah_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kode Jurusan --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Kode Jurusan
                </label>

                <input
                    type="text"
                    wire:model="kode_jurusan"
                    class="w-full rounded-lg border-gray-300">

                @error('kode_jurusan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

            </div>

            {{-- Nama Jurusan --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Nama Jurusan
                </label>

                <input
                    type="text"
                    wire:model="nama_jurusan"
                    class="w-full rounded-lg border-gray-300">

                @error('nama_jurusan')
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

                {{ $editMode ? 'Simpan Perubahan' : 'Tambah Jurusan' }}

            </button>

        </div>

    </div>

</div>

@endif