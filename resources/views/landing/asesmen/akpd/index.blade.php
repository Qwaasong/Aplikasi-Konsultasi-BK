@extends('layouts.landing')

@section('title', 'AKPD')

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

                        <path
                            d="M320-240h320v-80H320v80Zm0-160h320v-80H320v80Zm0-160h160v-80H320v80Zm-80 400q-33 0-56.5-23.5T160-240v-480q0-33 23.5-56.5T240-800h160l80-80h240q33 0 56.5 23.5T800-800v560q0 33-23.5 56.5T720-160H240Z" />

                    </svg>

                </div>

                {{-- Content --}}
                <div class="assessment-content">

                    <h1 class="assessment-title">
                        AKPD
                    </h1>

                    <h3 class="assessment-subtitle">
                        Angket Kebutuhan Peserta Didik
                    </h3>

                    <p class="assessment-description">
                        Halaman ini digunakan untuk mengisi Angket Kebutuhan Peserta Didik (AKPD).
                    </p>

                    <h4 class="assessment-detail-title">
                        Detail Penting
                    </h4>

                    <ul class="assessment-detail-list">

                        <li>
                            Mengenali kebutuhan peserta didik.
                        </li>

                        <li>
                            Jawablah sesuai kondisi sebenarnya.
                        </li>

                        <li>
                            Tidak ada jawaban benar maupun salah.
                        </li>

                    </ul>

                    <a href="#" class="assessment-button">
                        Mulai Mengisi AKPD
                    </a>

                </div>

            </div>

        </div>

    </section>

    {{-- Footer --}}
    @include('landing.sections.footer')

@endsection