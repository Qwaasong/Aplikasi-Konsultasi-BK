<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $nama = '';
    public string $username = '';
    public string $role = 'konselor';
    public string $password = '';
    public string $password_confirmation = '';
    public $roles = [
        ['value' => 'admin', 'label' => 'Admin'],
        ['value' => 'konselor', 'label' => 'Konselor'],
    ];

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'max:255', 'unique:' . User::class],
            'role' => ['required', 'string', 'in:konselor,admin'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $role = Auth::user()->role;
        $route = 'dashboard';

        if ($role === 'admin') {
            $route = route('admin.dashboard', absolute: false);
        } elseif ($role === 'konselor') {
            $route = route('konselor.dashboard', absolute: false);
        } else {
            $route = '/';
        }

        $this->redirect($route, navigate: true);
    }
}; ?>

<div class="flex min-h-screen w-full">
    {{-- Kolom Kiri: Form --}}
    <div class="w-full md:w-1/2 flex flex-col justify-center px-8 md:px-16 lg:px-24 py-12 bg-white">
        <form wire:submit="register" class="space-y-6">
            <x-molecules.auth-header title="Buat Akun" subtitle="Sudah punya akun?" linkText="Login di sini"
                :linkHref="route('login')" />

            {{-- Nama --}}
            <x-molecules.input-field label="Nama Lengkap" id="nama" type="text" name="nama" size="md"
                placeholder="Masukkan nama lengkap" wire:model="nama" :error="$errors->first('nama')" />

            {{-- Username --}}
            <x-molecules.input-field label="Username" id="username" type="text" name="username" size="md"
                placeholder="Masukkan username" wire:model="username" :error="$errors->first('username')" />

            {{-- Role --}}
            <div class="mb-4">
                <x-atoms.input-label for="role" size="md">Role</x-atoms.input-label>
                <x-molecules.input-dropdown id="role" wire:model="role" size="md" :options="$roles" />
                @error('role')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <x-molecules.input-field label="Password" id="password" type="password" name="password" size="md"
                placeholder="Masukkan password" wire:model="password" :error="$errors->first('password')" />

            {{-- Konfirmasi Password --}}
            <x-molecules.input-field label="Konfirmasi Password" id="password_confirmation" type="password"
                name="password_confirmation" size="md" placeholder="Ulangi password" wire:model="password_confirmation"
                :error="$errors->first('password_confirmation')" />

            <x-atoms.button variant="primary" size="md" type="submit" class="w-full">
                {{ __('Daftar') }}
            </x-atoms.button>
        </form>
    </div>

    {{-- Kolom Kanan: Gambar --}}
    <div class="hidden md:block md:w-1/2 relative">
        <img src="{{ asset('asset/image/BackgroundForest.webp') }}" alt="Background Forest"
            class="absolute inset-0 w-full h-full object-cover" fetchpriority="high">
        <div class="absolute inset-0 bg-blue-900 bg-opacity-10 mix-blend-overlay"></div>
    </div>
</div>