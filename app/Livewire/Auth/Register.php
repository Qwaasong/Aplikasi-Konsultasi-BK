<?php

namespace App\Livewire\Auth;

use App\Services\s\UserService;
use Illuminate\Support\Facades\Auth;
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
    public function register(UserService $service): void
    {
        $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'role' => ['required', 'string', 'in:guru_bk,admin'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $service->register([
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email,
            'no_hp' => $this->no_hp,
            'jenis_kelamin' => $this->jenis_kelamin,
            'role' => $this->role,
            'password' => $this->password,
        ]);

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
}
