<?php

use Illuminate\Support\Facades\Auth;
use function Livewire\Volt\{state};

state([
    'menus' => function () {
        $role = Auth::user()->role ?? '';
        $prefix = match ($role) {
            'guru_bk' => 'konselor',
            'admin' => 'admin',
            default  => $role,
        };

        $menus = [];

        // Guru BK
        if ($role === 'guru_bk') {

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
                    request()->routeIs('konselor.pengunduran-diri.*') ||
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
                                'variants' => 'triangle',
                            ],

                            [
                                'label' => 'Kelompok',
                                'url' => route('konselor.layanan-konseling.kelompok'),
                                'active' => request()->routeIs('konselor.layanan-konseling.kelompok'),
                                'variants' => 'triangle',
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
                        'label' => 'Pengunduran Diri',
                        'url' => route('konselor.pengunduran-diri.index'),
                        'active' => request()->routeIs('konselor.pengunduran-diri.*'),
                        'variants' => 'logout',
                    ],

                    [
                        'label' => 'Peminatan',
                        'url' => route('konselor.peminatan.index'),
                        'active' => request()->routeIs('konselor.peminatan.*'),
                        'variants' => 'target',
                    ],

                ],

            ];

            $menus[] = [
                'label' => 'Asesmen',
                'url' => route('konselor.asesmen.index'),
                'active' => request()->routeIs('konselor.asesmen.*'),
                'variants' => 'assessment',

                'children' => [

                    [
                        'label' => 'AKPD',
                        'url' => route('konselor.asesmen.akpd.index'),
                        'active' => request()->routeIs('konselor.asesmen.akpd.*'),
                        'variants' => 'assignment',
                    ],

                    [
                        'label' => 'Gaya Belajar',
                        'url' => route('konselor.asesmen.gaya-belajar.index'),
                        'active' => request()->routeIs('konselor.asesmen.gaya-belajar.*'),
                        'variants' => 'book',
                    ],

                    [
                        'label' => 'DCM',
                        'url' => route('konselor.asesmen.dcm.index'),
                        'active' => request()->routeIs('konselor.asesmen.dcm.*'),
                        'variants' => 'fact_check',
                    ],

                    [
                        'label' => 'Sosiometri',
                        'url' => route('konselor.asesmen.sosiometri.index'),
                        'active' => request()->routeIs('konselor.asesmen.sosiometri.*'),
                        'variants' => 'group',
                    ],

                    [
                        'label' => 'Tes Bakat Minat',
                        'url' => route('konselor.asesmen.tes-bakat-minat.index'),
                        'active' => request()->routeIs('konselor.asesmen.tes-bakat-minat.*'),
                        'variants' => 'analytics',
                    ],
                ],
            ];
        }

        // Admin
        if ($role === 'admin') {

            $menus[] = [
                'label' => 'Dashboard',
                'url' => route('admin.dashboard'),
                'active' => request()->routeIs('admin.dashboard'),
                'variants' => 'dashboard'
            ];

            $menus[] = [
                'label' => 'Kelola User',
                'url' => '#',
                'active' =>
                request()->routeIs('admin.kelola-user.siswa.*') ||
                    request()->routeIs('admin.kelola-user.pegawai.*'),
                'variants' => 'user',

                'children' => [

                    [
                        'label' => 'Siswa',
                        'url' => route('admin.kelola-user.siswa.index'),
                        'active' => request()->routeIs('admin.kelola-user.siswa.*'),
                        'variants' => 'student',
                    ],

                    [
                        'label' => 'Pegawai',
                        'url' => route('admin.kelola-user.pegawai.index'),
                        'active' => request()->routeIs('admin.kelola-user.pegawai.*'),
                        'variants' => 'employee',
                    ],

                ],
            ];

            $menus[] = [
                'label' => 'Kelola Data',
                'url' => '#',
                'active' =>
                request()->routeIs('admin.kelola-data.daftar-sekolah.*') ||
                    request()->routeIs('admin.kelola-data.daftar-jurusan.*') ||
                    request()->routeIs('admin.kelola-data.daftar-kelas.*') ||
                    request()->routeIs('admin.kelola-data.daftar-tahun-ajaran.*'),
                'variants' => 'database',

                'children' => [

                    [
                        'label' => 'Daftar Sekolah',
                        'variants' => 'school',
                        'url' => route('admin.kelola-data.daftar-sekolah.index'),
                        'active' => request()->routeIs('admin.kelola-data.daftar-sekolah.*'),
                    ],

                    [
                        'label' => 'Daftar Jurusan',
                        'variants' => 'category',
                        'url' => route('admin.kelola-data.daftar-jurusan.index'),
                        'active' => request()->routeIs('admin.kelola-data.daftar-jurusan.*'),
                    ],

                    [
                        'label' => 'Daftar Kelas',
                        'variants' => 'book',
                        'url' => route('admin.kelola-data.daftar-kelas.index'),
                        'active' => request()->routeIs('admin.kelola-data.daftar-kelas.*'),
                    ],

                    [
                        'label' => 'Daftar Tahun Ajaran',
                        'variants' => 'calendar',
                        'url' => route('admin.kelola-data.daftar-tahun-ajaran.index'),
                        'active' => request()->routeIs('admin.kelola-data.daftar-tahun-ajaran.*'),
                    ],
                ],
            ];

            $menus[] = [
                'label' => 'Rekap Absensi',
                'url' => route('admin.rekap-absensi.index'),
                'active' => request()->routeIs('admin.rekap-absensi.*'),
                'variants' => 'attendance',
            ];

            $menus[] = [
                'label' => 'Log Kasus',
                'url' => route('admin.log-kasus.index'),
                'active' => request()->routeIs('admin.log-kasus.*'),
                'variants' => 'file',
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