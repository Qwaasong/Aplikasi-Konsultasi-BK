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

        // Guru BK
        if ($role === 'Guru_BK') {

            $menus[] = [
                'label' => 'Dashboard',
                'url' => route('konselor.dashboard'),
                'active' => request()->routeIs('konselor.dashboard'),
                'variants' => 'dashboard',
                'children' => [],
            ];

            $menus[] = [
                'label' => 'Pembinaan Siswa',
                'url' => '#',
                'active' =>
                request()->routeIs('konselor.kehadiran-siswa.*') ||
                    request()->routeIs('konselor.layanan-konseling.*') ||
                    request()->routeIs('konselor.kunjungan-rumah.*') ||
                    request()->routeIs('konselor.alih-tangan-kasus.*') ||
                    request()->routeIs('konselor.konferensi-kasus.*') ||
                    request()->routeIs('konselor.peminatan.*'),

                'variants' => 'consultation',

                'children' => [

                    [
                        'label' => 'Kehadiran Siswa',
                        'url' => route('konselor.kehadiran-siswa.index'),
                        'active' => request()->routeIs('konselor.kehadiran-siswa.*'),
                        'variants' => 'attendance',
                    ],

                    [
                        'label' => 'Konseling Siswa',
                        'url' => '#',

                        'active' =>
                        request()->routeIs('konselor.layanan-konseling.individu') ||
                            request()->routeIs('konselor.layanan-konseling.kelompok'),

                        'variants' => 'consultation',

                        'children' => [

                            [
                                'label' => 'Individu',
                                'url' => route('konselor.layanan-konseling.individu'),
                                'active' => request()->routeIs('konselor.layanan-konseling.individu'),
                                'variants' => 'consultation',
                            ],

                            [
                                'label' => 'Kelompok',
                                'url' => route('konselor.layanan-konseling.kelompok'),
                                'active' => request()->routeIs('konselor.layanan-konseling.kelompok'),
                                'variants' => 'consultation',
                            ],

                        ],
                    ],

                    [
                        'label' => 'Kunjungan Rumah',
                        'url' => route('konselor.kunjungan-rumah.index'),
                        'active' => request()->routeIs('konselor.kunjungan-rumah.*'),
                        'variants' => 'home',
                    ],

                    [
                        'label' => 'Alih Tangan Kasus',
                        'url' => route('konselor.alih-tangan-kasus.index'),
                        'active' => request()->routeIs('konselor.alih-tangan-kasus.*'),
                        'variants' => 'swap',
                    ],

                    [
                        'label' => 'Konferensi Kasus',
                        'url' => route('konselor.konferensi-kasus.index'),
                        'active' => request()->routeIs('konselor.konferensi-kasus.*'),
                        'variants' => 'group',
                    ],

                    [
                        'label' => 'Peminatan',
                        'url' => route('konselor.peminatan.index'),
                        'active' => request()->routeIs('konselor.peminatan.*'),
                        'variants' => 'target',
                    ],

                ],
            ];
        }

        // Admin
        if ($role === 'Admin') {

            $menus[] = [
                'label' => 'Dashboard',
                'url' => route('admin.dashboard'),
                'active' => request()->routeIs('admin.dashboard'),
                'variants' => 'dashboard'
            ];

            $menus[] = [
                'label' => 'Konsultasi',
                'url' => route('admin.konsultasi.index'),
                'active' => request()->routeIs('admin.konsultasi.*'),
                'variants' => 'consultation'
            ];

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