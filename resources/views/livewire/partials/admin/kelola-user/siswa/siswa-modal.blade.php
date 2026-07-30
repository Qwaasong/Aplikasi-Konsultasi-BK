<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Services\i\SiswaService;
use App\Models\Kelas;

new class extends Component {

    public ?int $editingId = null;

    #[Validate('required|string|max:255')]
    public $nama = '';

    #[Validate('required|string|max:100')]
    public $username = '';

    #[Validate('nullable|email')]
    public $email = '';

    #[Validate('nullable|string|max:20')]
    public $no_hp = '';

    #[Validate('required|numeric')]
    public $nis = '';

    #[Validate('required|integer')]
    public $kelas_id = '';

    #[Validate('required|in:L,P')]
    public $jenis_kelamin = 'L';

    #[Validate('nullable|string')]
    public $alamat = '';

    public function kelasOptions()
    {
        return Kelas::orderBy('nama_kelas')->get();
    }

    #[On('create-siswa')]
    public function create()
    {
        $this->reset();

        $this->jenis_kelamin = 'L';

        $this->dispatch('open-modal','form-siswa');
    }

    #[On('edit-siswa')]
    public function edit($id)
    {
        $this->editingId = $id;

        $siswa = app(SiswaService::class)->findById($id);

        $this->nama = $siswa->user->nama;
        $this->username = $siswa->user->username;
        $this->email = $siswa->user->email;
        $this->no_hp = $siswa->user->no_hp;
        $this->jenis_kelamin = $siswa->user->jenis_kelamin;

        $this->nis = $siswa->nis;
        $this->kelas_id = $siswa->kelas_id;
        $this->alamat = $siswa->alamat;

        $this->dispatch('open-modal','form-siswa');
    }

    public function save(SiswaService $service)
    {
        $this->validate();

        $data = [
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email,
            'no_hp' => $this->no_hp,
            'jenis_kelamin' => $this->jenis_kelamin,
            'nis' => $this->nis,
            'kelas_id' => $this->kelas_id,
            'alamat' => $this->alamat,
        ];

        if ($this->editingId) {
            $service->update($this->editingId,$data);
            session()->flash('success','Data siswa berhasil diperbarui.');
        } else {
            $service->create($data);
            session()->flash('success','Data siswa berhasil ditambahkan.');
        }

        $this->dispatch('close-modal','form-siswa');
        $this->dispatch('refreshTable');
    }

}; 
?>

<x-shared.modal name="form-siswa" maxWidth="2xl">

<div class="flex flex-col">

<div class="px-6 py-4 border-b">
    <h2 class="text-lg font-bold">
        {{ $editingId ? 'Edit Siswa' : 'Tambah Siswa' }}
    </h2>
</div>

<div class="p-6 space-y-5">

    <div class="grid grid-cols-2 gap-4">

        <div>
            <x-atoms.input-label>Nama</x-atoms.input-label>
            <x-atoms.text-input wire:model="nama"/>
        </div>

        <div>
            <x-atoms.input-label>Username</x-atoms.input-label>
            <x-atoms.text-input wire:model="username"/>
        </div>

        <div>
            <x-atoms.input-label>Email</x-atoms.input-label>
            <x-atoms.text-input type="email" wire:model="email"/>
        </div>

        <div>
            <x-atoms.input-label>No HP</x-atoms.input-label>
            <x-atoms.text-input wire:model="no_hp"/>
        </div>

        <div>
            <x-atoms.input-label>NIS</x-atoms.input-label>
            <x-atoms.text-input wire:model="nis"/>
        </div>

        <div>
            <x-atoms.input-label>Kelas</x-atoms.input-label>

            <select
                wire:model="kelas_id"
                class="w-full rounded-lg border-gray-300">

                <option value="">Pilih Kelas</option>

                @foreach($this->kelasOptions() as $kelas)
                    <option value="{{ $kelas->id }}">
                        {{ $kelas->nama_kelas }}
                    </option>
                @endforeach

            </select>

        </div>

        <div>
            <x-atoms.input-label>Jenis Kelamin</x-atoms.input-label>

            <select
                wire:model="jenis_kelamin"
                class="w-full rounded-lg border-gray-300">

                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>

            </select>

        </div>

    </div>

    <div>

        <x-atoms.input-label>Alamat</x-atoms.input-label>

        <textarea
            wire:model="alamat"
            rows="3"
            class="w-full rounded-lg border-gray-300"></textarea>

    </div>

</div>

<div class="px-6 py-4 border-t flex justify-end gap-2">

    <x-atoms.button
        variant="secondary"
        x-on:click="show=false">
        Batal
    </x-atoms.button>

    <x-atoms.button
        wire:click="save">

        {{ $editingId ? 'Perbarui' : 'Simpan' }}

    </x-atoms.button>

</div>

</div>

</x-shared.modal>