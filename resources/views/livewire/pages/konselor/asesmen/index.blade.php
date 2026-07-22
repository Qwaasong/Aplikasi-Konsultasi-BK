<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {

    public function with(): array
    {
        return [
            'asesmens' => [
                [
                    'title' => 'AKPD',
                    'subtitle' => 'Angket Kebutuhan Peserta Didik',
                    'description' => 'Membantu Guru BK memahami kebutuhan peserta didik dalam aspek pribadi, sosial, belajar, dan karier sehingga layanan yang diberikan lebih tepat sasaran.',
                    'details' => [
                        'Membantu BK memahami kebutuhan siswa.',
                        'Tidak ada jawaban benar atau salah.',
                        'Estimasi waktu 10-15 menit.',
                    ],
                    'route' => route('konselor.asesmen.akpd.index'),
                    'button' => 'Mulai Mengisi AKPD',
                    'variants' => 'assignment',
                    'color' => 'teal'
                ],

                [
                    'title' => 'DCM',
                    'subtitle' => 'Daftar Cek Masalah',
                    'description' => 'Digunakan untuk mengetahui berbagai permasalahan yang dialami peserta didik sehingga Guru BK dapat memberikan layanan yang sesuai.',
                    'details' => [
                        'Jawablah sesuai kondisi sebenarnya.',
                        'Jawaban bersifat rahasia.',
                        'Estimasi waktu 10-15 menit.',
                    ],
                    'route' => route('konselor.asesmen.dcm.index'),
                    'button' => 'Mulai Mengisi DCM',
                    'variants' => 'fact_check',
                    'color' => 'teal'
                ],



                [
                    'title' => 'Sosiometri',
                    'subtitle' => 'Hubungan Sosial Siswa',
                    'description' => 'Membantu Guru BK memahami hubungan sosial antar peserta didik dalam satu kelas.',
                    'details' => [
                        'Jawablah dengan jujur.',
                        'Tidak ada jawaban benar atau salah.',
                        'Data hanya digunakan untuk layanan BK.',
                    ],
                    'route' => route('konselor.asesmen.sosiometri.index'),
                    'button' => 'Mulai Sosiometri',
                    'variants' => 'group',
                    'color' => 'teal'
                ],

                [
                    'title' => 'Gaya Belajar',
                    'subtitle' => 'Identifikasi Gaya Belajar',
                    'description' => 'Mengidentifikasi kecenderungan gaya belajar peserta didik agar proses pembelajaran menjadi lebih efektif.',
                    'details' => [
                        'Jawablah sesuai kebiasaan belajar.',
                        'Tidak ada jawaban benar atau salah.',
                        'Estimasi waktu 5-10 menit.',
                    ],
                    'route' => route('konselor.asesmen.gaya-belajar.index'),
                    'button' => 'Mulai Tes Gaya Belajar',
                    'variants' => 'book',
                    'color' => 'teal'
                ],

                [
                    'title' => 'Tes Bakat Minat',
                    'subtitle' => 'Identifikasi Potensi Siswa',
                    'description' => 'Membantu mengetahui bakat dan minat peserta didik sebagai dasar pemberian layanan karier.',
                    'details' => [
                        'Jawablah sesuai diri sendiri.',
                        'Tidak ada jawaban benar atau salah.',
                        'Hasil menjadi rekomendasi BK.',
                    ],
                    'route' => route('konselor.asesmen.tes-bakat-minat.index'),
                    'button' => 'Mulai Tes Bakat Minat',
                    'variants' => 'analytics',
                    'color' => 'teal'
                ],
            ],
        ];
    }

};

?>

<div class="flex-1 bg-gray-50">

    <div class="px-8 py-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Halo, Konselor!
            </h1>

            <p class="text-gray-500 mt-2">
                Silakan pilih instrumen asesmen yang akan digunakan.
            </p>
        </div>

    <x-organisms.header>
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>

    </x-organisms.header>

        <div class="space-y-6">

            @foreach($asesmens as $item)

                <x-organisms.assessment-card
                    :title="$item['title']"
                    :subtitle="$item['subtitle']"
                    :description="$item['description']"
                    :details="$item['details']"
                    :route="$item['route']"
                    :button="$item['button']"
                    :variants="$item['variants']"
                    :color="$item['color']"
                />

            @endforeach

        </div>

    </div>

</div>