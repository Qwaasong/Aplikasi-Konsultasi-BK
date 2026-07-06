<?php

use Illuminate\Support\Facades\Auth;
use function Livewire\Volt\{state};

state([
    'menus' => function () {
        $role = Auth::user()->role ?? '';
        $prefix = match ($role) {
            'Guru_BK' => 'konselor',
            'Admin' => 'admin',
            default  => $role,
        };

        $menus = [];

        if ($role === 'Admin' || $role === 'Guru_BK') {
            $menus[] = [
                'label' => 'Dashboard',
                'url' => route($prefix . '.dashboard'),
                'active' => request()->routeIs($prefix . '.dashboard'),
                'variants' => 'dashboard'
            ];

            $menus[] = [
                'label' => 'Konsultasi',
                'url' => route($prefix . '.konsultasi.index'),
                'active' => request()->routeIs($prefix . '.konsultasi.*'),
                'variants' => 'consultation'
            ];
        }

        if ($role === 'Admin') {
            $menus[] = [
                'label' => 'Siswa',
                'url' => route('admin.siswa.index'),
                'active' => request()->routeIs('admin.siswa.*'),
                'variants' => 'student'
            ];

            $menus[] = [
                'label' => 'User',
                'url' => route('admin.user.index'),
                'active' => request()->routeIs('admin.user.*'),
                'variants' => 'user'
            ];
        }

        return $menus;
    }
]);

?>

<x-organisms.sidebar :menus="$menus">

    <x-slot:footer>

        <a href="{{ route('logout') }}"
            class="flex items-center h-16 w-full bg-brand-teal text-white hover:bg-brand-dark transition-colors overflow-hidden">
            <div class="w-20 flex-shrink-0 flex justify-center items-center">

                <x-atoms.icon variant="logout" />

            </div>

            <span class="hide-text font-medium whitespace-nowrap ml-2">
                Logout
            </span>

        </a>

    </x-slot:footer>

</x-organisms.sidebar>