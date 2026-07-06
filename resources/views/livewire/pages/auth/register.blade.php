<?php

use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $nama = '';
    public string $username = '';
    public string $email = '';
    public string $no_hp = '';
    public string $jenis_kelamin = 'Laki-laki';
    public string $role = 'Guru_BK';
    public string $password = '';
    public string $password_confirmation = '';
    public $roles = [
        ['value' => 'Admin', 'label' => 'Admin'],
        ['value' => 'Guru_BK', 'label' => 'Konselor'],
    ];
    public $jenisKelaminOptions = [
        ['value' => 'Laki-laki', 'label' => 'Laki-laki'],
        ['value' => 'Perempuan', 'label' => 'Perempuan'],
    ];

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'max:255', 'unique:' . User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'no_hp' => ['required', 'string', 'max:20'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'role' => ['required', 'string', 'in:Guru_BK,Admin'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['foto'] = '';

        $user = User::create($validated);

        // Buat entri pegawai otomatis untuk role Guru_BK dan Admin
        if (in_array($user->role, ['Guru_BK', 'Admin'])) {
            Pegawai::create([
                'user_id' => $user->id,
                'nip' => $this->generateNip($user),
                'jabatan' => $user->role === 'Admin' ? 'Admin' : 'Guru BK',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        $role = Auth::user()->role;
        $route = 'dashboard';

        if ($role === 'Admin') {
            $route = route('admin.dashboard', absolute: false);
        } elseif ($role === 'Guru_BK') {
            $route = route('konselor.dashboard', absolute: false);
        } else {
            $route = '/';
        }

        $this->redirect($route, navigate: true);
    }

    /**
     * Generate NIP otomatis berdasarkan tahun + urutan.
     */
    private function generateNip(User $user): string
    {
        $prefix = $user->role === 'Admin' ? 'ADM' : 'GBK';
        $count = Pegawai::count() + 1;
        $date = date('Ymd');

        return "{$prefix}{$date}{$count}";
    }
}; ?>

<div class="flex h-full w-full">
    {{-- Kolom Kiri: Form --}}
    <div class="w-full md:w-1/2 flex flex-col justify-start overflow-y-auto px-8 md:px-16 lg:px-24 py-8 md:py-12 bg-white" style="scrollbar-width: thin;">
        <form wire:submit="register" class="space-y-6">
                <x-molecules.auth-header title="Buat Akun" subtitle="Sudah punya akun?" linkText="Login di sini"
                    :linkHref="route('login')" />

                {{-- Nama --}}
                <x-molecules.input-field label="Nama Lengkap" id="nama" type="text" name="nama" size="md"
                    placeholder="Masukkan nama lengkap" wire:model="nama" :error="$errors->first('nama')" />

                {{-- Username --}}
                <x-molecules.input-field label="Username" id="username" type="text" name="username" size="md"
                    placeholder="Masukkan username" wire:model="username" :error="$errors->first('username')" />

                {{-- Email --}}
                <x-molecules.input-field label="Email" id="email" type="email" name="email" size="md"
                    placeholder="Masukkan email" wire:model="email" :error="$errors->first('email')" />

                {{-- Grid: No HP + Jenis Kelamin --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-molecules.input-field label="No. HP" id="no_hp" type="text" name="no_hp" size="md"
                        placeholder="Masukkan no. HP" wire:model="no_hp" :error="$errors->first('no_hp')" />

                    <div class="mb-4">
                        <x-atoms.input-label for="jenis_kelamin" size="md">Jenis Kelamin</x-atoms.input-label>
                        <x-molecules.input-dropdown id="jenis_kelamin" wire:model="jenis_kelamin" size="md" :options="$jenisKelaminOptions" />
                        @error('jenis_kelamin')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

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