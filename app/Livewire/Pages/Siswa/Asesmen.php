<?php

namespace App\Livewire\Pages\Siswa;

use App\Models\DataSiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Asesmen Siswa - Bimbingan Konseling'])]
class Asesmen extends Component
{
    public ?DataSiswa $siswa = null;

    public array $items = [];

    public function mount(): void
    {
        $user = Auth::user();

        $this->siswa = DataSiswa::with(['user', 'kelas'])->where('user_id', $user->id)->first();

        if (! $this->siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $this->items = [
            [
                'title' => 'AKPD',
                'description' => 'Angket Kebutuhan Peserta Didik untuk mengetahui kebutuhan dan perkembangan diri.',
                'route' => 'https://forms.gle/EiEaJS2VYU6k6AeV8',
                'badge' => 'Kebutuhan',
                'label' => 'Pilih Kelas AKPD',
                'options' => [
                    ['label' => 'Kelas X', 'route' => 'https://forms.gle/EiEaJS2VYU6k6AeV8'],
                    ['label' => 'Kelas XI', 'route' => 'https://forms.gle/xNyicyELono4yn9Z7'],
                    ['label' => 'Kelas XII', 'route' => 'https://forms.gle/s5K1thgso3C673DS6'],
                ],
            ],
            [
                'title' => 'Gaya Belajar',
                'description' => 'Tes untuk mengenali kecenderungan cara belajar yang paling sesuai.',
                'route' => 'https://forms.gle/kTMVHMNSFGe9bgMF9',
                'badge' => 'Belajar',
                'label' => 'Mulai Tes',
            ],
            [
                'title' => 'DCM',
                'description' => 'Daftar Cek Masalah untuk menilai potensi persoalan yang sedang dihadapi.',
                'route' => 'https://forms.gle/UDbDykodyMKY6Ejb8',
                'badge' => 'Masalah',
                'label' => 'Isi DCM',
            ],
            [
                'title' => 'Sosiometri',
                'description' => 'Menggambarkan hubungan sosial dan preferensi teman dalam kelompok.',
                'route' => '#',
                'badge' => 'Sosial',
                'label' => 'Segera hadir',
            ],
            [
                'title' => 'Tes Bakat Minat',
                'description' => 'Membantu mengetahui minat, bakat, dan kecenderungan potensi diri.',
                'route' => 'https://forms.gle/Mw6QT8pg61tmVTRL9',
                'badge' => 'Potensi',
                'label' => 'Mulai Tes',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.pages.siswa.asesmen');
    }
}
