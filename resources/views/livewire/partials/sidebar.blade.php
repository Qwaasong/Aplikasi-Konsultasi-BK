<?php

use function Livewire\Volt\{state};

state([
    'menus' => fn() => [
        [
            'label' => 'Dashboard',
            'active' => request()->routeIs('dashboard'),
            'variants' => 'dashboard'
        ],

        [
            'label' => 'Konsultasi',
            'active' => request()->routeIs('konsultasi.*'),
            'variants'  => 'consultation'
        ],

        [
            'label' => 'Siswa',
            'active' => request()->routeIs('siswa.*'),
            'variants' => 'student'
        ],

        [
            'label' => 'User',
            'active' => request()->routeIs('user.*'),
            'variants' => 'user'
        ]
    ]
]);

?>

<x-organisms.sidebar :menus="$menus">

    <x-slot:footer>

        <a href=""
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