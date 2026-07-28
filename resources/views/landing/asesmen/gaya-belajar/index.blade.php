@extends('layouts.landing')

@section('title', 'Gaya Belajar')

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
                viewBox="0 0 640 640"
                class="assessment-svg">
                    <path d="M480 576L192 576C139 576 96 533 96 480L96 160C96 107 139 64 192 64L496 64C522.5 64 544 85.5 544 112L544 400C544 420.9 530.6 438.7 512 445.3L512 512C529.7 512 544 526.3 544 544C544 561.7 529.7 576 512 576L480 576zM192 448C174.3 448 160 462.3 160 480C160 497.7 174.3 512 192 512L448 512L448 448L192 448zM224 216C224 229.3 234.7 240 248 240L424 240C437.3 240 448 229.3 448 216C448 202.7 437.3 192 424 192L248 192C234.7 192 224 202.7 224 216zM248 288C234.7 288 224 298.7 224 312C224 325.3 234.7 336 248 336L424 336C437.3 336 448 325.3 448 312C448 298.7 437.3 288 424 288L248 288z" />
                </svg>

            </div>

            {{-- Content --}}
            <div class="assessment-content">

                <h1 class="assessment-title">
                    Gaya Belajar
                </h1>

                <h3 class="assessment-subtitle">
                    Tes Gaya Belajar
                </h3>

                <p class="assessment-description">
                    Halaman ini digunakan untuk mengetahui kecenderungan gaya belajar peserta didik.
                </p>

                <h4 class="assessment-detail-title">
                    Detail Penting
                </h4>

                <ul class="assessment-detail-list">

                    <li>
                        Visual.
                    </li>

                    <li>
                        Auditori.
                    </li>

                    <li>
                        Kinestetik.
                    </li>

                </ul>

                <a href="#" class="assessment-button">
                    Mulai Tes Gaya Belajar
                </a>

            </div>

        </div>

    </div>

</section>

{{-- Footer --}}
@include('landing.sections.footer')

@endsection