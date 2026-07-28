@extends('layouts.landing')

@section('title', 'Layanan BK')

@push('styles')
<link rel="stylesheet" href="{{ asset('asset/css/layanan.css') }}">
@endpush

@section('content')

@include('landing.sections.navbar')

@include('landing.layanan.sections.hero')

@include('landing.layanan.sections.about')

@include('landing.layanan.sections.importance')

@include('landing.layanan.sections.services')

@include('landing.layanan.sections.role')

@include('landing.sections.cta')

@include('landing.sections.footer')

@endsection