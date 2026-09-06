<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public function getRoleProperty(): string
    {
        return Auth::user()->role ?? '';
    }
};

?>

<div
    class="md:hidden"
    x-data="{
        mobileMenuOpen: false,

        {{-- Konselor --}}
        pembinaanOpen: false,
        asesmenOpen: false,

        {{-- Admin --}}
        kelolaUserOpen: false,
        kelolaDataOpen: false
    }"
    @keydown.escape.window="mobileMenuOpen = false">

    {{-- TOMBOL HAMBURGER --}}
    <button
        type="button"
        @click="mobileMenuOpen = true"
        class="md:hidden fixed top-3 left-3 z-[70]
               w-9 h-9
               flex items-center justify-center
               rounded-md
               bg-white
               border border-gray-200
               shadow-sm
               text-brand-teal"
        aria-label="Buka menu">

        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M4 6h16M4 12h16M4 18h16" />

        </svg>

    </button>


    {{-- MOBILE SIDEBAR --}}
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="-translate-y-full"
        class="md:hidden fixed inset-x-0 top-0 z-[60]
               bg-white shadow-lg"
        style="display: none;">


        {{-- HEADER --}}
        <div
            class="h-16 px-4
                   flex items-center justify-between
                   border-b border-gray-200">


            {{-- LOGO + JUDUL --}}
            <div class="flex items-center gap-3">

                <x-atoms.application-logo
                    class="w-8 h-8 object-contain" />

                <span class="font-semibold text-brand-teal">

                    @if ($this->role === 'admin')
                        Halaman Admin

                    @elseif ($this->role === 'guru_bk')
                        Halaman Konselor

                    @endif

                </span>

            </div>


            {{-- TOMBOL CLOSE --}}
            <button
                type="button"
                @click="mobileMenuOpen = false"
                class="w-9 h-9
                       flex items-center justify-center
                       rounded-md
                       text-gray-600
                       hover:bg-gray-100"
                aria-label="Tutup menu">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-6 h-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12" />

                </svg>

            </button>

        </div>


        {{-- NAVIGATION --}}
        <nav
            class="flex flex-col
                   max-h-[calc(100vh-4rem)]
                   overflow-y-auto">


            {{-- ============================================ --}}
            {{-- MENU ADMIN --}}
            {{-- ============================================ --}}
            @if ($this->role === 'admin')


                {{-- DASHBOARD --}}
                <a
                    href="{{ route('admin.dashboard') }}"
                    wire:navigate
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3
                           px-5 py-4
                           text-sm text-gray-700
                           border-b border-gray-200
                           hover:bg-gray-50">

                    <span class="w-5 flex justify-center">
                        <x-atoms.icon variant="dashboard" />
                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                {{-- KELOLA USER --}}
                <div class="border-b border-gray-200">

                    <button
                        type="button"
                        @click="kelolaUserOpen = !kelolaUserOpen"
                        class="w-full
                               flex items-center justify-between
                               px-5 py-4
                               text-sm text-gray-700
                               hover:bg-gray-50">

                        <div class="flex items-center gap-3">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="user" />
                            </span>

                            <span>
                                Kelola User
                            </span>

                        </div>


                        {{-- ARROW --}}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-180': kelolaUserOpen }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m19 9-7 7-7-7" />

                        </svg>

                    </button>


                    {{-- SUBMENU KELOLA USER --}}
                    <div
                        x-show="kelolaUserOpen"
                        x-transition
                        x-cloak
                        class="bg-gray-50">


                        {{-- SISWA --}}
                        <a
                            href="{{ route('admin.kelola-user.siswa.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="user" />
                            </span>

                            <span>
                                Siswa
                            </span>

                        </a>


                        {{-- PEGAWAI --}}
                        <a
                            href="{{ route('admin.kelola-user.pegawai.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="user" />
                            </span>

                            <span>
                                Pegawai
                            </span>

                        </a>

                    </div>

                </div>


                {{-- KELOLA DATA --}}
                <div class="border-b border-gray-200">

                    <button
                        type="button"
                        @click="kelolaDataOpen = !kelolaDataOpen"
                        class="w-full
                               flex items-center justify-between
                               px-5 py-4
                               text-sm text-gray-700
                               hover:bg-gray-50">

                        <div class="flex items-center gap-3">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="book" />
                            </span>

                            <span>
                                Kelola Data
                            </span>

                        </div>


                        {{-- ARROW --}}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-180': kelolaDataOpen }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m19 9-7 7-7-7" />

                        </svg>

                    </button>


                    {{-- SUBMENU KELOLA DATA --}}
                    <div
                        x-show="kelolaDataOpen"
                        x-transition
                        x-cloak
                        class="bg-gray-50">


                        {{-- DAFTAR SEKOLAH --}}
                        <a
                            href="{{ route('admin.kelola-data.daftar-sekolah.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="home" />
                            </span>

                            <span>
                                Daftar Sekolah
                            </span>

                        </a>


                        {{-- DAFTAR JURUSAN --}}
                        <a
                            href="{{ route('admin.kelola-data.daftar-jurusan.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="book" />
                            </span>

                            <span>
                                Daftar Jurusan
                            </span>

                        </a>


                        {{-- DAFTAR KELAS --}}
                        <a
                            href="{{ route('admin.kelola-data.daftar-kelas.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="book" />
                            </span>

                            <span>
                                Daftar Kelas
                            </span>

                        </a>


                        {{-- DAFTAR TAHUN AJARAN --}}
                        <a
                            href="{{ route('admin.kelola-data.daftar-tahun-ajaran.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="book" />
                            </span>

                            <span>
                                Daftar Tahun Ajaran
                            </span>

                        </a>

                    </div>

                </div>


                {{-- REKAP ABSENSI --}}
                <a
                    href="{{ route('admin.rekap-absensi.index') }}"
                    wire:navigate
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3
                           px-5 py-4
                           text-sm text-gray-700
                           border-b border-gray-200
                           hover:bg-gray-50">

                    <span class="w-5 flex justify-center">
                        <x-atoms.icon variant="attendance" />
                    </span>

                    <span>
                        Rekap Absensi
                    </span>

                </a>


                {{-- LOG KASUS --}}
                <a
                    href="{{ route('admin.log-kasus.index') }}"
                    wire:navigate
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3
                           px-5 py-4
                           text-sm text-gray-700
                           border-b border-gray-200
                           hover:bg-gray-50">

                    <span class="w-5 flex justify-center">
                        <x-atoms.icon variant="fact_check" />
                    </span>

                    <span>
                        Log Kasus
                    </span>

                </a>


                {{-- PROFIL SAYA --}}
                <a
                    href="{{ route('admin.profile') }}"
                    wire:navigate
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3
                           px-5 py-4
                           text-sm text-gray-700
                           border-b border-gray-200
                           hover:bg-gray-50">

                    <span class="w-5 flex justify-center">
                        <x-atoms.icon variant="user" />
                    </span>

                    <span>
                        Profil Saya
                    </span>

                </a>



            {{-- ============================================ --}}
            {{-- MENU KONSELOR --}}
            {{-- ============================================ --}}
            @elseif ($this->role === 'guru_bk')


                {{-- DASHBOARD --}}
                <a
                    href="{{ route('konselor.dashboard') }}"
                    wire:navigate
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3
                           px-5 py-4
                           text-sm text-gray-700
                           border-b border-gray-200
                           hover:bg-gray-50">

                    <span class="w-5 flex justify-center">
                        <x-atoms.icon variant="dashboard" />
                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                {{-- PEMBINAAN SISWA --}}
                <div class="border-b border-gray-200">

                    <button
                        type="button"
                        @click="pembinaanOpen = !pembinaanOpen"
                        class="w-full
                               flex items-center justify-between
                               px-5 py-4
                               text-sm text-gray-700
                               hover:bg-gray-50">

                        <div class="flex items-center gap-3">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="consultation" />
                            </span>

                            <span>
                                Pembinaan Siswa
                            </span>

                        </div>


                        {{-- ARROW --}}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-180': pembinaanOpen }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m19 9-7 7-7-7" />

                        </svg>

                    </button>


                    <div
                        x-show="pembinaanOpen"
                        x-transition
                        class="bg-gray-50">


                        {{-- KEHADIRAN SISWA --}}
                        <a
                            href="{{ route('konselor.kehadiran-siswa.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="attendance" />
                            </span>

                            <span>
                                Kehadiran Siswa
                            </span>

                        </a>


                        {{-- LAYANAN KONSELING --}}
                        <div
                            x-data="{ konselingOpen: false }"
                            class="border-t border-gray-200">

                            <button
                                type="button"
                                @click="konselingOpen = !konselingOpen"
                                class="w-full flex items-center justify-between
                                       pl-12 pr-5 py-3
                                       text-sm font-medium text-gray-700
                                       hover:bg-gray-50
                                       transition">

                                <div class="flex items-center gap-3">

                                    <x-atoms.icon
                                        variant="consultation"
                                        size="md" />

                                    <span>
                                        Layanan Konseling
                                    </span>

                                </div>

                                <x-atoms.icon
                                    variant="chevron"
                                    size="sm"
                                    class="transition-transform duration-200"
                                    ::class="{ 'rotate-180': konselingOpen }" />

                            </button>


                            {{-- SUBMENU LAYANAN KONSELING --}}
                            <div
                                x-show="konselingOpen"
                                x-transition
                                x-cloak
                                class="bg-white">


                                {{-- INDIVIDU --}}
                                <a
                                    href="{{ route('konselor.layanan-konseling.individu') }}"
                                    wire:navigate
                                    @click="mobileMenuOpen = false"
                                    class="flex items-center gap-3
                                           pl-16 pr-5 py-3
                                           text-sm text-gray-600
                                           hover:bg-gray-50
                                           hover:text-brand-teal
                                           transition">

                                    <x-atoms.icon
                                        variant="triangle"
                                        size="sm" />

                                    <span>
                                        Individu
                                    </span>

                                </a>


                                {{-- KELOMPOK --}}
                                <a
                                    href="{{ route('konselor.layanan-konseling.kelompok') }}"
                                    wire:navigate
                                    @click="mobileMenuOpen = false"
                                    class="flex items-center gap-3
                                           pl-16 pr-5 py-3
                                           text-sm text-gray-600
                                           hover:bg-gray-50
                                           hover:text-brand-teal
                                           transition">

                                    <x-atoms.icon
                                        variant="triangle"
                                        size="sm" />

                                    <span>
                                        Kelompok
                                    </span>

                                </a>

                            </div>

                        </div>


                        {{-- KUNJUNGAN RUMAH --}}
                        <a
                            href="{{ route('konselor.kunjungan-rumah.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="home" />
                            </span>

                            <span>
                                Kunjungan Rumah
                            </span>

                        </a>


                        {{-- ALIH TANGAN KASUS --}}
                        <a
                            href="{{ route('konselor.alih-tangan-kasus.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="swap" />
                            </span>

                            <span>
                                Alih Tangan Kasus
                            </span>

                        </a>


                        {{-- KONFERENSI KASUS --}}
                        <a
                            href="{{ route('konselor.konferensi-kasus.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="group" />
                            </span>

                            <span>
                                Konferensi Kasus
                            </span>

                        </a>


                        {{-- PENGUNDURAN DIRI --}}
                        <a
                            href="{{ route('konselor.pengunduran-diri.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="logout" />
                            </span>

                            <span>
                                Pengunduran Diri
                            </span>

                        </a>

                    </div>

                </div>


                {{-- ASESMEN --}}
                <div class="border-b border-gray-200">

                    <button
                        type="button"
                        @click="asesmenOpen = !asesmenOpen"
                        class="w-full
                               flex items-center justify-between
                               px-5 py-4
                               text-sm text-gray-700
                               hover:bg-gray-50">

                        <div class="flex items-center gap-3">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="assessment" />
                            </span>

                            <span>
                                Asesmen
                            </span>

                        </div>


                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-180': asesmenOpen }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m19 9-7 7-7-7" />

                        </svg>

                    </button>


                    {{-- ASESMEN CHILDREN --}}
                    <div
                        x-show="asesmenOpen"
                        x-transition
                        class="bg-gray-50">


                        {{-- AKPD --}}
                        <a
                            href="{{ route('konselor.asesmen.akpd.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="assignment" />
                            </span>

                            <span>
                                AKPD
                            </span>

                        </a>


                        {{-- GAYA BELAJAR --}}
                        <a
                            href="{{ route('konselor.asesmen.gaya-belajar.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="book" />
                            </span>

                            <span>
                                Gaya Belajar
                            </span>

                        </a>


                        {{-- DCM --}}
                        <a
                            href="{{ route('konselor.asesmen.dcm.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="fact_check" />
                            </span>

                            <span>
                                DCM
                            </span>

                        </a>


                        {{-- SOSIOMETRI --}}
                        <a
                            href="{{ route('konselor.asesmen.sosiometri.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="group" />
                            </span>

                            <span>
                                Sosiometri
                            </span>

                        </a>


                        {{-- TES BAKAT MINAT --}}
                        <a
                            href="{{ route('konselor.asesmen.tes-bakat-minat.index') }}"
                            wire:navigate
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3
                                   px-5 pl-12 py-3
                                   text-sm text-gray-600
                                   border-t border-gray-200
                                   hover:bg-gray-100">

                            <span class="w-5 flex justify-center">
                                <x-atoms.icon variant="analytics" />
                            </span>

                            <span>
                                Tes Bakat Minat
                            </span>

                        </a>

                    </div>

                </div>


                {{-- PROFIL SAYA KONSELOR --}}
                <a
                    href="{{ route('konselor.profile') }}"
                    wire:navigate
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3
                           px-5 py-4
                           text-sm text-gray-700
                           border-b border-gray-200
                           hover:bg-gray-50">

                    <span class="w-5 flex justify-center">
                        <x-atoms.icon variant="user" />
                    </span>

                    <span>
                        Profil Saya
                    </span>

                </a>


            @endif


            {{-- ============================================ --}}
            {{-- LOGOUT --}}
            {{-- UNTUK ADMIN DAN KONSELOR --}}
            {{-- ============================================ --}}
            <a
                href="{{ route('logout') }}"
                class="flex items-center gap-3
                       px-5 py-4
                       mt-8
                       bg-brand-teal
                       text-white
                       hover:bg-brand-dark
                       transition-colors">

                <span class="w-5 flex justify-center">
                    <x-atoms.icon variant="logout" />
                </span>

                <span class="font-medium">
                    Logout
                </span>

            </a>

        </nav>

    </div>

</div>