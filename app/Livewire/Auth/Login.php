<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;

class Login extends Component
{
    public LoginForm $form;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Jika user sudah login, redirect ke dashboard sesuai role.
     */
    public function mount(): void
    {
        if (Auth::check()) {
            $role = Auth::user()->role;

            if ($role === 'admin') {
                $this->redirect(route('admin.dashboard', absolute: false), navigate: true);
            } elseif ($role === 'guru_bk') {
                $this->redirect(route('konselor.dashboard', absolute: false), navigate: true);
            }
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();

        $role = Auth::user()->role;
        $route = 'dashboard';

        if ($role === 'admin') {
            $route = route('admin.dashboard', absolute: false);
        } elseif ($role === 'guru_bk') {
            $route = route('konselor.dashboard', absolute: false);
        } else {
            $route = '/';
        }

        $this->redirectIntended(default: $route, navigate: true);
    }
}
