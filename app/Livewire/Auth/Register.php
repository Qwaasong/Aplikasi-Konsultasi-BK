<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Volt\Component;

class Register extends Component
{
    public function __construct()
    {
        parent::__construct();
    }
    public string $nama = '';
    public string $username = '';
    public string $email = '';
    public string $no_hp = '';
    public string $jenis_kelamin = 'L';
    public string $role = 'guru_bk';
    public string $password = '';
    public string $password_confirmation = '';

    public array $roles = [
        ['value' => 'admin', 'label' => 'Admin'],
        ['value' => 'guru_bk', 'label' => 'Konselor'],
    ];

    public array $jenisKelaminOptions = [
        ['value' => 'L', 'label' => 'Laki-laki'],
        ['value' => 'P', 'label' => 'Perempuan'],
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
            'jenis_kelamin' => ['required', 'in:L,P'],
            'role' => ['required', 'string', 'in:guru_bk,admin'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['foto'] = '';

        $user = User::create($validated);

        // Buat entri pegawai otomatis untuk role Guru_BK dan Admin
        if (in_array($user->role, ['guru_bk', 'admin'])) {
            Pegawai::create([
                'user_id' => $user->id,
                'nip' => $this->generateNip($user),
                'jabatan' => $user->role === 'admin' ? 'Admin' : 'Guru BK',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        $role = Auth::user()->role;
        $route = 'dashboard';

        if ($role === 'admin') {
            $route = route('admin.dashboard', absolute: false);
        } elseif ($role === 'guru_bk') {
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
        $prefix = $user->role === 'admin' ? 'ADM' : 'GBK';
        $count = Pegawai::count() + 1;
        $date = date('Ymd');

        return "{$prefix}{$date}{$count}";
    }
}
