<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $role = Auth::user()->role;
        $route = 'dashboard'; // fallback

        if ($role === 'admin') {
            $route = route('admin.dashboard', absolute: false);
        } elseif ($role === 'konselor') {
            $route = route('konselor.dashboard', absolute: false);
        } else {
            // Jika role siswa atu role lain
            $route = '/';
        }

        $this->redirectIntended(default: $route, navigate: true);
    }
}; ?>

<div class="flex min-h-screen w-full">
    {{-- Kolom Kiri: Form --}}
    <div class="w-full md:w-1/2 flex flex-col justify-center px-8 md:px-16 lg:px-24 py-12 bg-white">
        <!-- Session Status -->
        <x-atoms.auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="login" class="space-y-6">
            <x-molecules.auth-header title="Selamat Datang" subtitle="Belum Punya Akun ?" linkText="Buat Disini"
                linkHref="register" />
            <x-molecules.input-field label="Username" id="username" type="text" name="username" size="md"
                placeholder="Masukkan username" wire:model="form.username" />
            <x-molecules.input-field label="Password" id="password" type="password" name="password" size="md"
                placeholder="Masukkan password" wire:model="form.password" />
            <x-molecules.auth-remember wire:model="form.remember" />
            <x-atoms.button variant="primary" size="md" type="submit" class="w-full">{{ __('Log in') }}</x-atoms.button>
        </form>
    </div>

    {{-- Kolom Kanan: Gambar --}}
    <div class="hidden md:block md:w-1/2 relative">
        <img src="{{ asset("asset\image\BackgroundForest.webp") }}" alt="Background Forest"
            class="absolute inset-0 w-full h-full object-cover" fetchpriority="high">
        <div class="absolute inset-0 bg-blue-900 bg-opacity-10 mix-blend-overlay"></div>
    </div>
</div>