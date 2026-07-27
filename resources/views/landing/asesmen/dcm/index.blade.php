@extends('layouts.landing')

@section('title', 'DCM')

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
                    
                    <path d="m438-498 142-142-56-56-86 86-46-46-56 56 102 102ZM240-160q-33 0-56.5-23.5T160-240v-560q0-33 23.5-56.5T240-880h320l240 240v400q0 33-23.5 56.5T720-160H240Zm280-520v-120H240v560h480v-440H520Z" />
                </svg>

            </div>

            {{-- Content --}}
            <div class="assessment-content">

                <h1 class="assessment-title">
                    DCM
                </h1>

                <h3 class="assessment-subtitle">
                    Daftar Cek Masalah
                </h3>

                <p class="assessment-description">
                    Halaman ini digunakan untuk mengidentifikasi masalah yang dialami peserta didk.
                </p>

                <h4 class="assessment-detail-title">
                    Detail Penting
                </h4>

                <ul class="assessment-detail-list">

                    <li>
                        Jawablah dengan jujur.
                    </li>

                    <li>
                        Data bersifat rahasia.
                    </li>

                    <li>
                        Digunakan sebagai dasar layanan BK.
                    </li>

                </ul>

                <a href="{{ route('login') }}" class="assessment-button">
                    Mulai Mengisi DCM
                </a>

            </div>

        </div>

    </div>

</section>

{{-- Footer --}}
@include('landing.sections.footer')

@endsection