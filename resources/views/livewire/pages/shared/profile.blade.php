<?php

namespace App\Livewire\Shared;

use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Validation\Rule;

new #[Layout('layouts.app', ['title' => 'Profil Saya - Bimbingan Konseling'])] class extends Component {
    public string $nama = '';
    public string $username = '';
    public string $email = '';
    public string $no_hp = '';
    public string $jenis_kelamin = 'L';
    public string $alamat = '';
    
    // Pegawai fields
    public string $nip = '';
    public string $jabatan = '';

    // Password fields
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public array $jenisKelaminOptions = [
        ['value' => 'L', 'label' => 'Laki-laki'],
        ['value' => 'P', 'label' => 'Perempuan'],
    ];

    public function mount(): void
    {
        $user = Auth::user();
        $this->nama = $user->nama ?? '';
        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';
        $this->no_hp = $user->no_hp ?? '';
        $this->jenis_kelamin = $user->jenis_kelamin ?? 'L';
        $this->alamat = $user->alamat ?? '';

        // Load pegawai fields if exists
        $pegawai = Pegawai::where('user_id', $user->id)->first();
        if ($pegawai) {
            $this->nip = $pegawai->nip ?? '';
            $this->jabatan = $pegawai->jabatan ?? '';
        }
    }

    public function updateProfile(): void
    {
        $user = Auth::user();
        
        $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp' => ['required', 'string', 'max:20'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'alamat' => ['nullable', 'string'],
        ]);

        $user->update([
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email,
            'no_hp' => $this->no_hp,
            'jenis_kelamin' => $this->jenis_kelamin,
            'alamat' => $this->alamat,
        ]);

        session()->flash('status', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(): void
    {
        $user = Auth::user();

        $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        session()->flash('status_password', 'Password berhasil diperbarui!');
    }
}; ?>

<div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 min-h-screen p-6 lg:p-10">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-brand-teal border border-teal-100">
                Pengaturan Akun
            </span>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Profil Saya</h1>
        </div>
    </div>

    {{-- Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Form: Informasi Pribadi --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                    <div class="p-2 rounded-xl bg-teal-50 text-brand-teal">
                        <x-atoms.icon variant="user" size="md" />
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Informasi Pribadi</h3>
                </div>

                @if (session()->has('status'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <form wire:submit="updateProfile" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-molecules.input-field label="Nama Lengkap" id="nama" type="text" name="nama" wire:model="nama" :error="$errors->first('nama')" />
                        <x-molecules.input-field label="Username" id="username" type="text" name="username" wire:model="username" :error="$errors->first('username')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-molecules.input-field label="Email" id="email" type="email" name="email" wire:model="email" :error="$errors->first('email')" />
                        <x-molecules.input-field label="No. HP" id="no_hp" type="text" name="no_hp" wire:model="no_hp" :error="$errors->first('no_hp')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <x-atoms.input-label for="jenis_kelamin" size="md">Jenis Kelamin</x-atoms.input-label>
                            <x-molecules.input-dropdown id="jenis_kelamin" wire:model="jenis_kelamin" size="md" :options="$jenisKelaminOptions" />
                            @error('jenis_kelamin')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @if($nip)
                            <div class="mb-4">
                                <x-atoms.input-label for="nip" size="md">NIP / NUPTK</x-atoms.input-label>
                                <x-atoms.text-input id="nip" type="text" value="{{ $nip }}" disabled class="bg-gray-50 border-gray-200 cursor-not-allowed text-gray-500" />
                            </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <x-atoms.input-label for="alamat" size="md">Alamat Lengkap</x-atoms.input-label>
                        <textarea id="alamat" wire:model="alamat" rows="3" class="w-full border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-brand-dark focus:border-brand-dark transition duration-150 px-4 py-2 text-sm" placeholder="Masukkan alamat lengkap"></textarea>
                        @error('alamat')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-atoms.button type="submit" variant="primary">
                            Simpan Perubahan
                        </x-atoms.button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Form: Ganti Password & Ringkasan --}}
        <div class="space-y-6">
            {{-- Ganti Password --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                    <div class="p-2 rounded-xl bg-red-50 text-red-600">
                        <x-atoms.icon variant="lock" size="md" />
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Ubah Password</h3>
                </div>

                @if (session()->has('status_password'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-medium">
                        {{ session('status_password') }}
                    </div>
                @endif

                <form wire:submit="updatePassword" class="space-y-4">
                    <x-molecules.input-field label="Password Saat Ini" id="current_password" type="password" name="current_password" wire:model="current_password" :error="$errors->first('current_password')" />
                    <x-molecules.input-field label="Password Baru" id="new_password" type="password" name="new_password" wire:model="new_password" :error="$errors->first('new_password')" />
                    <x-molecules.input-field label="Konfirmasi Password Baru" id="new_password_confirmation" type="password" name="new_password_confirmation" wire:model="new_password_confirmation" :error="$errors->first('new_password_confirmation')" />

                    <x-atoms.button type="submit" variant="primary" class="w-full">
                        Perbarui Password
                    </x-atoms.button>
                </form>
            </div>

            {{-- Ringkasan Peran --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 text-[15px] border-b border-gray-100 pb-4 mb-4">Informasi Peran</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Hak Akses / Role</span>
                        <span class="font-semibold text-gray-800 uppercase bg-teal-50 border border-teal-100 text-brand-teal px-2 py-0.5 rounded text-xs">
                            {{ Auth::user()->role === 'guru_bk' ? 'Konselor / Guru BK' : 'Administrator' }}
                        </span>
                    </div>
                    @if($jabatan)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Jabatan</span>
                            <span class="font-semibold text-gray-800">{{ $jabatan }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Status Akun</span>
                        <span class="font-semibold text-green-700 bg-green-50 border border-green-100 px-2 py-0.5 rounded text-xs capitalize">
                            {{ Auth::user()->status ?? 'aktif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
