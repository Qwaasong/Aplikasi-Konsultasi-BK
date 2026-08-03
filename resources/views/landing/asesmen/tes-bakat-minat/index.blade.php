@extends('layouts.landing')

@section('title', 'Tes Bakat Minat')

@push('styles')
<link rel="stylesheet" href="{{ asset('asset/css/asesmen.css') }}">
@endpush

@section('content')

{{-- Navbar --}}
@include('landing.sections.navbar')

{{-- Assessment --}}
<section class="assessment-section">

    <div class="container">

        <div class="assessment-card" data-aos="fade-up">

            {{-- Icon --}}
            <div class="assessment-icon">

                <svg xmlns="http://www.w3.org/2000/svg" 
                viewBox="0 -960 960 960" 
                class="assessment-svg">
                    <path d="M320-320h80v-200h-80v200Zm120 0h80v-320h-80v320Zm120 0h80v-120h-80v120ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Z" />
                </svg>

            </div>

            {{-- Content --}}
            <div class="assessment-content">

                <h1 class="assessment-title">
                    Tes Bakat Minat
                </h1>

                <h3 class="assessment-subtitle">
                    Tes Potensi Bakat dan Minat
                </h3>

                <p class="assessment-description">
                    Halaman ini digunakan untuk mengetahui kecenderungan bakat dan minat peserta didik.
                </p>

                <h4 class="assessment-detail-title">
                    Detail Penting
                </h4>

                <ul class="assessment-detail-list">

                    <li>
                        Jawablah sesuai kondisi diri.
                    </li>

                    <li>
                        Tidak ada jawaban benar atau salah.
                    </li>

                    <li>
                        Hasil digunakan sebagai bahan layanan BK.
                    </li>

                </ul>

                <div style="display: flex; justify-content: flex-start; margin-top: 20px;">
                    <a href="https://forms.gle/Mw6QT8pg61tmVTRL9" target="_blank" class="assessment-button">
                        Mulai Tes Bakat Minat
                    </a>
                </div>

            </div>

        </div>

    </div>

</section>

{{-- Footer --}}
@include('landing.sections.footer')

@endsection