<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Services\Siswa\SiswaService;
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
        $this->resetValidation();

        $this->jenis_kelamin = 'L';

        $this->dispatch('open-modal','form-siswa');
    }

    #[On('edit-siswa')]
    public function edit($id)
    {
        $this->resetValidation();
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
    <div class="flex flex-col h-full max-h-[80vh]">

        {{-- HEADER ─────────────────────────────────────── --}}
        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">
                {{ $editingId ? 'Edit Siswa' : 'Tambah Siswa' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $editingId ? 'Perbarui data siswa' : 'Catat data siswa baru' }}
            </p>
        </div>

        {{-- SCROLLABLE CONTENT ────────────────────────────────── --}}
        <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div>
                    <x-atoms.input-label for="nama" size="sm">Nama <span class="text-red-500">*</span></x-atoms.input-label>
                    <x-atoms.text-input id="nama" wire:model="nama" size="md" placeholder="Masukkan nama siswa"/>
                    @error('nama') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-atoms.input-label for="username" size="sm">Username <span class="text-red-500">*</span></x-atoms.input-label>
                    <x-atoms.text-input id="username" wire:model="username" size="md" placeholder="Masukkan username"/>
                    @error('username') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-atoms.input-label for="email" size="sm">Email</x-atoms.input-label>
                    <x-atoms.text-input id="email" type="email" wire:model="email" size="md" placeholder="contoh@email.com"/>
                    @error('email') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-atoms.input-label for="no_hp" size="sm">No HP</x-atoms.input-label>
                    <x-atoms.text-input id="no_hp" wire:model="no_hp" size="md" placeholder="08..."/>
                    @error('no_hp') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-atoms.input-label for="nis" size="sm">NIS <span class="text-red-500">*</span></x-atoms.input-label>
                    <x-atoms.text-input id="nis" wire:model="nis" size="md" placeholder="Masukkan NIS"/>
                    @error('nis') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-atoms.input-label for="kelas_id" size="sm">Kelas <span class="text-red-500">*</span></x-atoms.input-label>
                    <select id="kelas_id" wire:model="kelas_id"
                        class="w-full border border-gray-200 rounded-md px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        <option value="">Pilih Kelas</option>
                        @foreach($this->kelasOptions() as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                     @error('kelas_id') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-atoms.input-label for="jenis_kelamin" size="sm">Jenis Kelamin <span class="text-red-500">*</span></x-atoms.input-label>
                    <select id="jenis_kelamin" wire:model="jenis_kelamin"
                        class="w-full border border-gray-200 rounded-md px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-5">
                <x-atoms.input-label for="alamat" size="sm">Alamat</x-atoms.input-label>
                <textarea id="alamat" wire:model="alamat" rows="3" class="w-full border border-gray-200 rounded-md p-4 text-[14.5px] text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm leading-relaxed" placeholder="Masukkan alamat lengkap siswa..."></textarea>
                @error('alamat') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
            </div>

        </div>

        {{-- FOOTER ─────────────────────────────────────── --}}
        <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-3">
            <x-atoms.button variant="secondary" x-on:click="show=false">
                Batal
            </x-atoms.button>
            <x-atoms.button wire:click="save">
                {{ $editingId ? 'Perbarui' : 'Simpan' }}
            </x-atoms.button>
        </div>

    </div>
</x-shared.modal>
