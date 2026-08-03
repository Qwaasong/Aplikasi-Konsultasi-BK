<?php

use App\Livewire\Konselor\Asesmen\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {};

?>

<div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 min-h-screen">

    {{-- Header --}}
    <x-organisms.header>
        <x-slot:search>
            @if($selectedKelas)
                <x-molecules.search-input model="search" placeholder="Cari siswa di {{ $selectedKelas }}..." maxWidth="max-w-md" />
            @endif
        </x-slot:search>
        Asesmen
    </x-organisms.header>

    <div class="p-6 sm:p-8 space-y-6">

        {{-- ========================================================= --}}
        {{-- HERO METRICS & INFORMASI INSTRUMEN                        --}}
        {{-- ========================================================= --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-teal-700 via-teal-800 to-cyan-900 text-white p-6 sm:p-8 shadow-xl shadow-teal-900/10">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute right-1/3 -top-10 w-48 h-48 bg-cyan-400/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-teal-100 text-xs font-semibold uppercase tracking-wider mb-3">
                        <svg class="w-3.5 h-3.5 text-teal-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Portal Asesmen Bimbingan Konseling
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                        Pemetaan Potensi & Masalah Siswa
                    </h1>
                    <p class="text-teal-100/90 text-sm mt-2 leading-relaxed">
                        Kelola data asesmen diagnostik peserta didik secara terpadu meliputi instrumen AKPD, DCM, Gaya Belajar, Sosiometri, dan Tes Bakat Minat.
                    </p>
                </div>

                {{-- Visual Quick Pills Instrumen --}}
                <div class="flex flex-wrap gap-2 sm:gap-2.5 shrink-0">
                    <a href="{{ route('konselor.asesmen.akpd.index') }}" wire:navigate class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/15 text-xs font-semibold text-white transition flex items-center gap-1.5 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span> AKPD
                    </a>
                    <a href="{{ route('konselor.asesmen.dcm.index') }}" wire:navigate class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/15 text-xs font-semibold text-white transition flex items-center gap-1.5 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span> DCM
                    </a>
                    <a href="{{ route('konselor.asesmen.gaya-belajar.index') }}" wire:navigate class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/15 text-xs font-semibold text-white transition flex items-center gap-1.5 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span> Gaya Belajar
                    </a>
                    <a href="{{ route('konselor.asesmen.sosiometri.index') }}" wire:navigate class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/15 text-xs font-semibold text-white transition flex items-center gap-1.5 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-purple-400"></span> Sosiometri
                    </a>
                    <a href="{{ route('konselor.asesmen.tes-bakat-minat.index') }}" wire:navigate class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/15 text-xs font-semibold text-white transition flex items-center gap-1.5 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span> Bakat Minat
                    </a>
                </div>
            </div>
        </div>


        {{-- ========================================================= --}}
        {{-- DAFTAR KELAS (TAMPILAN UTAMA)                             --}}
        {{-- ========================================================= --}}
        @if(!$selectedKelas)

            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200/80 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Pilih Kelompok Kelas
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Pilih kelas di bawah untuk melihat rincian asesmen dan instrumen peserta didik.
                        </p>
                    </div>
                    <span class="text-xs font-medium text-gray-500 bg-white border border-gray-200 px-3 py-1.5 rounded-lg shadow-2xs self-start">
                        Total {{ count($kelasOptions) }} Kelompok Kelas
                    </span>
                </div>

                {{-- Cards Grid Kelas --}}
                @if(count($kelasOptions) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                        @foreach($kelasOptions as $index => $kelas)
                            @php
                                $gradients = [
                                    'from-teal-500/10 via-teal-500/5 to-transparent border-teal-200/80 hover:border-teal-500 text-teal-600',
                                    'from-cyan-500/10 via-cyan-500/5 to-transparent border-cyan-200/80 hover:border-cyan-500 text-cyan-600',
                                    'from-indigo-500/10 via-indigo-500/5 to-transparent border-indigo-200/80 hover:border-indigo-500 text-indigo-600',
                                    'from-purple-500/10 via-purple-500/5 to-transparent border-purple-200/80 hover:border-purple-500 text-purple-600',
                                    'from-rose-500/10 via-rose-500/5 to-transparent border-rose-200/80 hover:border-rose-500 text-rose-600',
                                    'from-amber-500/10 via-amber-500/5 to-transparent border-amber-200/80 hover:border-amber-500 text-amber-600',
                                ];
                                $cardStyle = $gradients[$index % count($gradients)];
                            @endphp

                            <button
                                type="button"
                                wire:click="pilihKelas(@js($kelas['nama']))"
                                class="group relative text-left bg-white border rounded-2xl p-5 shadow-xs transition-all duration-300 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2 overflow-hidden flex flex-col justify-between"
                            >
                                <div class="absolute inset-0 bg-gradient-to-br {{ $cardStyle }} opacity-50 group-hover:opacity-100 transition-opacity"></div>

                                <div class="relative z-10">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="px-2.5 py-1 rounded-md bg-white border border-gray-200/80 text-[10px] font-bold uppercase tracking-wider text-gray-500 shadow-2xs">
                                            Rombel
                                        </span>
                                        <div class="w-10 h-10 rounded-xl bg-white shadow-xs border border-gray-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <svg class="w-5 h-5 text-brand-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332-.477 4.5-1.253m0-10.494C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332-.477-4.5-1.253" />
                                            </svg>
                                        </div>
                                    </div>

                                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-brand-teal transition-colors">
                                        {{ $kelas['nama'] }}
                                    </h3>

                                    @if(!empty($kelas['jurusan']))
                                        <p class="text-xs text-gray-500 mt-1 font-medium line-clamp-1">
                                            {{ $kelas['jurusan'] }}
                                        </p>
                                    @endif
                                </div>

                                <div class="relative z-10 mt-6 pt-4 border-t border-gray-100 flex items-center justify-between text-xs font-semibold text-brand-teal">
                                    <span>Lihat Data Siswa</span>
                                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center shadow-2xs">
                        <div class="w-12 h-12 rounded-full bg-teal-50 text-brand-teal flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h4 class="text-base font-semibold text-gray-800">Belum Ada Data Kelas</h4>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                            Silakan tambahkan data kelas melalui menu Kelola Data master terlebih dahulu.
                        </p>
                    </div>
                @endif

            </div>

        {{-- ========================================================= --}}
        {{-- TABEL SISWA BERDASARKAN KELAS                             --}}
        {{-- ========================================================= --}}
        @else

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">

                {{-- Header Navigasi Kelas --}}
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <button
                            type="button"
                            wire:click="kembaliKeKelas"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-brand-teal bg-white border border-gray-200 px-3 py-1.5 rounded-lg shadow-2xs transition mb-2"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Daftar Kelas
                        </button>
                        <h2 class="text-xl font-extrabold text-gray-900 flex items-center gap-2">
                            <span>Asesmen Kelas {{ $selectedKelas }}</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-teal-100 text-teal-800 text-xs font-bold">
                                {{ count($records) }} Siswa
                            </span>
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Daftar peserta didik dan tautan langsung pengisian/pemeriksaan instrumen asesmen.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-organisms.table-toolbar onRefresh="refreshData">
                            <x-slot:pagination>
                                {{ count($records) }} data
                            </x-slot:pagination>
                        </x-organisms.table-toolbar>
                    </div>
                </div>

                {{-- Flash Message --}}
                <div class="px-6 pt-3">
                    <x-shared.flash-message />
                </div>

                {{-- Tabel Data Siswa --}}
                <x-organisms.data-table
                    :headers="[
                        'No',
                        'Siswa',
                        'NIS',
                        'Kelas',
                        'AKPD',
                        'DCM',
                        'Gaya Belajar',
                        'Sosiometri',
                        'Tes Bakat Minat',
                    ]"
                    empty="Belum ada data siswa untuk kelas ini."
                >
                    @forelse($records as $index => $record)
                        @php
                            $initials = collect(explode(' ', $record['nama']))
                                ->take(2)
                                ->map(fn($w) => mb_substr($w, 0, 1))
                                ->implode('');
                            $colors = ['bg-teal-500', 'bg-indigo-500', 'bg-cyan-500', 'bg-purple-500', 'bg-rose-500', 'bg-emerald-500'];
                            $avatarBg = $colors[$index % count($colors)];
                        @endphp

                        <tr wire:key="asesmen-{{ $record['id'] }}" class="group border-b border-gray-100 hover:bg-teal-50/30 transition-colors">
                            {{-- No --}}
                            <td class="w-12 text-center py-3.5 text-xs font-medium text-gray-400">
                                {{ $index + 1 }}
                            </td>

                            {{-- Nama Siswa & Avatar --}}
                            <td class="px-4 py-3.5 font-semibold text-gray-900">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $avatarBg }} text-white text-xs font-bold flex items-center justify-center shrink-0 shadow-2xs">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-800 group-hover:text-brand-teal transition-colors">
                                        {{ $record['nama'] }}
                                    </span>
                                </div>
                            </td>

                            {{-- NIS --}}
                            <td class="px-4 py-3.5 text-xs font-medium text-gray-500">
                                {{ $record['nis'] }}
                            </td>

                            {{-- Kelas --}}
                            <td class="px-4 py-3.5 text-xs font-semibold text-gray-700">
                                <span class="px-2 py-1 rounded-md bg-gray-100 text-gray-700">
                                    {{ $record['kelas'] }}
                                </span>
                            </td>

                            {{-- AKPD --}}
                            <td class="px-3 py-3.5">
                                <a href="{{ route('konselor.asesmen.akpd.index') }}" wire:navigate class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    AKPD
                                </a>
                            </td>

                            {{-- DCM --}}
                            <td class="px-3 py-3.5">
                                <a href="{{ route('konselor.asesmen.dcm.index') }}" wire:navigate class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    DCM
                                </a>
                            </td>

                            {{-- Gaya Belajar --}}
                            <td class="px-3 py-3.5">
                                <a href="{{ route('konselor.asesmen.gaya-belajar.index') }}" wire:navigate class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Gaya Belajar
                                </a>
                            </td>

                            {{-- Sosiometri --}}
                            <td class="px-3 py-3.5">
                                <a href="{{ route('konselor.asesmen.sosiometri.index') }}" wire:navigate class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Sosiometri
                                </a>
                            </td>

                            {{-- Tes Bakat Minat --}}
                            <td class="px-3 py-3.5">
                                <a href="{{ route('konselor.asesmen.tes-bakat-minat.index') }}" wire:navigate class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Bakat Minat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-sm text-gray-500">
                                Belum ada data siswa untuk kelas {{ $selectedKelas }}.
                            </td>
                        </tr>
                    @endforelse
                </x-organisms.data-table>

            </div>

        @endif

    </div>

</div>