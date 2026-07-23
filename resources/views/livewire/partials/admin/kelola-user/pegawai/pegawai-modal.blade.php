<?php

use App\Models\User;
use App\Services\PegawaiService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    public ?int $editingId = null;

    public int $step = 1;

    // ==========================
    // Data User
    // ==========================

    public $nama = '';
    public $email = '';
    public $password = '';
    public $role = '';

    // ==========================
    // Data Pegawai
    // ==========================

    public $nip = '';
    public $jabatan = '';

    #[Computed]
    public function roleOptions()
    {
        return [
            [
                'value' => 'admin',
                'label' => 'Administrator'
            ],
            [
                'value' => 'konselor',
                'label' => 'Konselor'
            ],
            [
                'value' => 'pegawai',
                'label' => 'Pegawai'
            ],
        ];
    }

    #[Computed]
    public function jabatanOptions()
    {
        return [
            'Guru BK',
            'Wali Kelas',
            'Guru',
            'Kepala Sekolah',
            'Staff TU',
        ];
    }

    public function getInitials($name)
    {
        if (!$name) {
            return '--';
        }

        $words = explode(' ', trim($name));

        if (count($words) >= 2) {
            return strtoupper(
                substr($words[0], 0, 1) .
                substr($words[1], 0, 1)
            );
        }

        return strtoupper(substr($name, 0, 2));
    }

    // ==========================
    // Navigation
    // ==========================

    public function nextStep()
    {
        if ($this->step == 1) {

            $rules = [

                'nama' => 'required|string|max:255',

                'email' => 'required|email',

                'role' => 'required',

            ];

            if (!$this->editingId) {

                $rules['password'] = 'required|min:8';

            }

            $this->validate($rules);
        }

        if ($this->step == 2) {

            $this->validate([

                'nip' => 'required',

                'jabatan' => 'required',

            ]);

        }

        if ($this->step < 3) {

            $this->step++;

        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {

            $this->step--;

        }
    }

    // ==========================
    // CREATE
    // ==========================

    #[On('create-pegawai')]
    public function createPegawai()
    {
        $this->resetValidation();

        $this->reset([
            'editingId',
            'nama',
            'email',
            'password',
            'role',
            'nip',
            'jabatan',
        ]);

        $this->step = 1;

        $this->dispatch('open-modal', 'form-pegawai');
    }

    // ==========================
    // EDIT
    // ==========================

    #[On('edit-pegawai')]
    public function loadPegawai($id)
    {
        $service = app(PegawaiService::class);

        $pegawai = $service->findById($id);

        $this->editingId = $pegawai->id;

        $this->nama = $pegawai->user->name;

        $this->email = $pegawai->user->email;

        $this->role = $pegawai->user->role;

        $this->nip = $pegawai->nip;

        $this->jabatan = $pegawai->jabatan;

        $this->password = '';

        $this->step = 1;

        $this->dispatch('open-modal', 'form-pegawai');
    }

    // ==========================
    // SAVE
    // ==========================

    public function save(PegawaiService $service)
    {
        $rules = [

            'nama' => 'required',

            'email' => 'required|email',

            'role' => 'required',

            'nip' => 'required',

            'jabatan' => 'required',

        ];

        if (!$this->editingId) {

            $rules['password'] = 'required|min:8';

        }

        $this->validate($rules);

        $data = [

            'name' => $this->nama,

            'email' => $this->email,

            'password' => $this->password,

            'role' => $this->role,

            'nip' => $this->nip,

            'jabatan' => $this->jabatan,

        ];

        if ($this->editingId) {

            $service->update($this->editingId, $data);

            session()->flash('success', 'Data pegawai berhasil diperbarui!');

        } else {

            $service->create($data);

            session()->flash('success', 'Data pegawai berhasil ditambahkan!');

        }

        $this->dispatch('close-modal', 'form-pegawai');

        $this->dispatch('refreshTable');
    }

};

?>

<div>
    <x-shared.modal name="form-pegawai" maxWidth="lg">
        <div class="flex flex-col h-full max-h-[80vh]">

            {{-- ================= HEADER ================= --}}
            <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">

                <h2 class="text-base font-bold text-gray-900 leading-tight">
                    {{ $editingId ? 'Edit Pegawai' : 'Tambah Pegawai' }}
                </h2>

                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $editingId
                        ? 'Perbarui data pegawai'
                        : 'Tambahkan data pegawai baru' }}
                </p>

                {{-- Progress --}}
                <div class="mt-4">

                    <div class="flex items-center justify-between mb-2">

                        <span class="text-xs font-semibold text-gray-600">
                            Langkah {{ $step }} Dari 3
                        </span>

                        <span class="text-xs text-gray-400">
                            @if($step==1)
                                Data Akun
                            @elseif($step==2)
                                Data Pegawai
                            @else
                                Review
                            @endif
                        </span>

                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-2">

                        <div
                            class="bg-primary h-2 rounded-full transition-all duration-500 ease-in-out"
                            style="width: {{ ($step/3)*100 }}%">
                        </div>

                    </div>

                    <div class="flex justify-between mt-2">

                        @foreach([1,2,3] as $s)

                            <div class="flex items-center gap-1">

                                <div
                                    class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold
                                    {{ $step >= $s
                                        ? 'bg-primary text-white'
                                        : 'bg-gray-200 text-gray-500' }}">

                                    {{ $s }}

                                </div>

                                <span
                                    class="text-[10px]
                                    {{ $step >= $s
                                        ? 'text-primary font-semibold'
                                        : 'text-gray-400' }}">

                                    @if($s==1)
                                        Akun
                                    @elseif($s==2)
                                        Pegawai
                                    @else
                                        Review
                                    @endif

                                </span>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>

            {{-- ================= CONTENT ================= --}}
            <div
                class="px-6 py-5 overflow-y-auto modal-scroll grow"
                style="scrollbar-width:thin;">
                @if($step==1)

            <div class="space-y-6">

                {{-- Nama --}}
                <div>

                    <x-atoms.input-label for="nama" size="sm">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </x-atoms.input-label>

                    <x-atoms.text-input
                        id="nama"
                        wire:model="nama"
                        size="md"
                        placeholder="Masukkan nama pegawai"/>

                    @error('nama')
                        <span class="text-red-500 text-[13px] mt-1 block">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                {{-- Email --}}
                <div>

                    <x-atoms.input-label for="email" size="sm">
                        Email <span class="text-red-500">*</span>
                    </x-atoms.input-label>

                    <x-atoms.text-input
                        id="email"
                        type="email"
                        wire:model="email"
                        size="md"
                        placeholder="pegawai@email.com"/>

                    @error('email')
                        <span class="text-red-500 text-[13px] mt-1 block">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                {{-- Password --}}
                @if(!$editingId)

                <div>

                    <x-atoms.input-label for="password" size="sm">
                        Password <span class="text-red-500">*</span>
                    </x-atoms.input-label>

                    <x-atoms.text-input
                        id="password"
                        type="password"
                        wire:model="password"
                        size="md"
                        placeholder="Minimal 8 karakter"/>

                    @error('password')
                        <span class="text-red-500 text-[13px] mt-1 block">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                @endif

                {{-- Role --}}
                <div>

                    <x-atoms.input-label size="sm">
                        Role <span class="text-red-500">*</span>
                    </x-atoms.input-label>

                    <select
                        wire:model="role"
                        class="w-full border border-gray-200 rounded-md px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">

                        <option value="">Pilih Role</option>

                        @foreach($this->roleOptions as $role)

                            <option value="{{ $role['value'] }}">
                                {{ $role['label'] }}
                            </option>

                        @endforeach

                    </select>

                    @error('role')
                        <span class="text-red-500 text-[13px] mt-1 block">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

            </div>

        @endif

        @if($step==2)

<div class="space-y-6">

    {{-- NIP --}}
    <div>

        <x-atoms.input-label for="nip" size="sm">
            NIP <span class="text-red-500">*</span>
        </x-atoms.input-label>

        <x-atoms.text-input
            id="nip"
            wire:model="nip"
            size="md"
            placeholder="Masukkan NIP Pegawai" />

        @error('nip')
            <span class="text-red-500 text-[13px] mt-1 block">
                {{ $message }}
            </span>
        @enderror

    </div>

    {{-- Jabatan --}}
    <div>

        <x-atoms.input-label for="jabatan" size="sm">
            Jabatan <span class="text-red-500">*</span>
        </x-atoms.input-label>

        <select
            id="jabatan"
            wire:model="jabatan"
            class="w-full border border-gray-200 rounded-md px-4 py-3 text-[14px]
                focus:outline-none focus:border-primary
                focus:ring-1 focus:ring-primary shadow-sm">

            <option value="">Pilih Jabatan</option>

            @foreach($this->jabatanOptions as $item)

                <option value="{{ $item }}">
                    {{ $item }}
                </option>

            @endforeach

        </select>

        @error('jabatan')
            <span class="text-red-500 text-[13px] mt-1 block">
                {{ $message }}
            </span>
        @enderror

    </div>

    {{-- Preview --}}
    <div class="bg-bg-light border border-gray-200 rounded-lg p-5">

        <h4 class="text-sm font-bold text-gray-800 mb-4">
            Preview Data Pegawai
        </h4>

        <div class="grid grid-cols-2 gap-y-3 text-sm">

            <div class="text-gray-500">
                NIP
            </div>

            <div class="font-semibold">
                {{ $nip ?: '-' }}
            </div>

            <div class="text-gray-500">
                Jabatan
            </div>

            <div class="font-semibold">
                {{ $jabatan ?: '-' }}
            </div>

        </div>

    </div>

</div>

@endif

@if($step==3)

<div class="space-y-5">

    {{-- Alert --}}
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-start gap-3">

        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5"
            fill="currentColor"
            viewBox="0 0 20 20">

            <path fill-rule="evenodd"
                d="M8.257 3.099c.765-1.36
                2.722-1.36
                3.486 0l5.58
                9.92c.75
                1.334-.213
                2.98-1.742
                2.98H4.42c-1.53
                0-2.493-1.646-1.743-2.98l5.58-9.92z"
                clip-rule="evenodd"/>

        </svg>

        <p class="text-[13px] text-amber-800 font-medium">

            Pastikan seluruh data pegawai sudah benar
            sebelum disimpan.

        </p>

    </div>

    {{-- Data User --}}
    <div class="border border-gray-200 rounded-lg overflow-hidden">

        <div class="bg-gray-50 px-4 py-2.5 border-b">

            <h4 class="text-[12px] font-bold text-gray-500 uppercase">
                Data User
            </h4>

        </div>

        <div class="px-4 py-4">

            <div class="flex items-center gap-3">

                <div class="w-11 h-11 rounded-full bg-teal-100
                    flex items-center justify-center
                    font-bold text-teal-700">

                    {{ $this->getInitials($nama) }}

                </div>

                <div>

                    <div class="font-bold text-gray-900">

                        {{ $nama ?: '-' }}

                    </div>

                    <div class="text-xs text-gray-500">

                        {{ $email ?: '-' }}

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- Detail Pegawai --}}
    <div class="border border-gray-200 rounded-lg overflow-hidden">

        <div class="bg-gray-50 px-4 py-2.5 border-b">

            <h4 class="text-[12px] font-bold text-gray-500 uppercase">
                Detail Pegawai
            </h4>

        </div>

        <div class="divide-y">

            <div class="flex justify-between px-4 py-3">

                <span class="text-sm text-gray-500">
                    Role
                </span>

                <span class="font-semibold">
                    {{ ucfirst($role) }}
                </span>

            </div>

            <div class="flex justify-between px-4 py-3">

                <span class="text-sm text-gray-500">
                    NIP
                </span>

                <span class="font-semibold">
                    {{ $nip }}
                </span>

            </div>

            <div class="flex justify-between px-4 py-3">

                <span class="text-sm text-gray-500">
                    Jabatan
                </span>

                <span class="font-semibold">
                    {{ $jabatan }}
                </span>

            </div>

        </div>

    </div>

</div>

@endif

{{-- ================= FOOTER ================= --}}
<div
    class="bg-bg-light px-7 py-5 border-t border-gray-100
    flex justify-end shrink-0 rounded-b-xl gap-3">

    {{-- Tombol Batal --}}
    <x-atoms.button
        variant="secondary"
        size="md"
        x-on:click="show = false">

        Batal

    </x-atoms.button>

    {{-- STEP 1 --}}
    @if($step==1)

        <x-atoms.button
            variant="primary"
            size="md"
            wire:click="nextStep">

            Selanjutnya

            <svg
                class="w-4 h-4 ml-1.5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5l7 7-7 7"/>

            </svg>

        </x-atoms.button>

    {{-- STEP 2 --}}
    @elseif($step==2)

        <x-atoms.button
            variant="secondary"
            size="md"
            wire:click="prevStep">

            <svg
                class="w-4 h-4 mr-1.5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19l-7-7 7-7"/>

            </svg>

            Kembali

        </x-atoms.button>

        <x-atoms.button
            variant="primary"
            size="md"
            wire:click="nextStep">

            Selanjutnya

            <svg
                class="w-4 h-4 ml-1.5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5l7 7-7 7"/>

            </svg>

        </x-atoms.button>

    {{-- STEP 3 --}}
    @else

        <x-atoms.button
            variant="secondary"
            size="md"
            wire:click="prevStep">

            <svg
                class="w-4 h-4 mr-1.5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19l-7-7 7-7"/>

            </svg>

            Kembali

        </x-atoms.button>

        <x-atoms.button
            variant="primary"
            size="md"
            wire:click="save"
            wire:loading.attr="disabled">

            <span wire:loading.remove wire:target="save">

                {{ $editingId ? 'Perbarui' : 'Simpan' }}

            </span>

            <span wire:loading wire:target="save">

                Menyimpan...

            </span>

        </x-atoms.button>

    @endif

</div>

</div>
</x-shared.modal>
</div>