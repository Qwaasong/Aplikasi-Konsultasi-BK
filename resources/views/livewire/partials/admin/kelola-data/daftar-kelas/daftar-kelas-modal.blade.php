<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Services\e\KelasService;
use App\Services\u\JurusanService;
use App\Services\i\SekolahService;
use App\Services\e\PegawaiService;

new class extends Component
{
    public bool $show = false;
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
        $this->show = true;
    }

    #[On('edit-kelas')]
    public function edit($id)
    {
        $record = app(KelasService::class)->findById($id);

        if (!$record) return;

        $this->kelasId = $record->id;
        $this->jurusan_id = $record->jurusan_id;
        $this->nama_kelas = $record->nama_kelas;
        $this->tingkat = $record->tingkat;
        $this->wali_kelas_id = $record->wali_kelas_id;

        $this->sekolah_id = $record->jurusan?->sekolah_id;

        $this->editMode = true;
        $this->show = true;
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->editMode) {

            app(KelasService::class)
                ->update($this->kelasId, $validated);

            session()->flash('success', 'Data kelas berhasil diperbarui.');

        } else {

            app(KelasService::class)
                ->create($validated);

            session()->flash('success', 'Data kelas berhasil ditambahkan.');
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
            'kelasId',
            'sekolah_id',
            'jurusan_id',
            'nama_kelas',
            'tingkat',
            'wali_kelas_id',
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
                {{ $editMode ? 'Edit Kelas' : 'Tambah Kelas' }}
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
                    wire:model.live="sekolah_id"
                    class="w-full rounded-lg border-gray-300">

                    <option value="">Pilih Sekolah</option>

                    @foreach($sekolahOptions as $sekolah)
                        <option value="{{ $sekolah->id }}">
                            {{ $sekolah->nama_sekolah }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- Jurusan --}}
            <div>
                <label class="block text-sm font-medium mb-2">
                    Jurusan
                </label>

                <select
                    wire:model="jurusan_id"
                    class="w-full rounded-lg border-gray-300">

                    <option value="">Pilih Jurusan</option>

                    @foreach($jurusanOptions->where('sekolah_id', $sekolah_id) as $jurusan)

                        <option value="{{ $jurusan->id }}">
                            {{ $jurusan->nama_jurusan }}
                        </option>

                    @endforeach

                </select>

                @error('jurusan_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

            </div>

            {{-- Nama Kelas --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Nama Kelas
                </label>

                <input
                    type="text"
                    wire:model="nama_kelas"
                    class="w-full rounded-lg border-gray-300">

                @error('nama_kelas')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

            </div>

            {{-- Tingkat --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Tingkat
                </label>

                <select
                    wire:model="tingkat"
                    class="w-full rounded-lg border-gray-300">

                    <option value="">Pilih Tingkat</option>
                    <option value="X">X</option>
                    <option value="XI">XI</option>
                    <option value="XII">XII</option>

                </select>

                @error('tingkat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

            </div>

            {{-- Wali Kelas --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Wali Kelas
                </label>

                <select
                    wire:model="wali_kelas_id"
                    class="w-full rounded-lg border-gray-300">

                    <option value="">Pilih Wali Kelas</option>

                    @foreach($pegawaiOptions as $pegawai)

                        <option value="{{ $pegawai->id }}">
                            {{ $pegawai->user?->nama }}
                        </option>

                    @endforeach

                </select>

                @error('wali_kelas_id')
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

                {{ $editMode ? 'Simpan Perubahan' : 'Tambah Kelas' }}

            </button>

        </div>

    </div>

</div>

@endif

</div>