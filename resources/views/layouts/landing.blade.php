<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Bimbingan & Konseling')</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    {{-- AOS --}}
    <link rel="stylesheet"
        href="https://unpkg.com/aos@2.3.4/dist/aos.css">

    {{-- Universal CSS --}}
    <link rel="stylesheet"
        href="{{ asset('asset/css/landing.css') }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('asset/image/SMKLogo.png') }}">

    {{-- CSS khusus halaman --}}
    @stack('styles')

    {{-- Livewire Styles --}}
    @livewireStyles

</head>

<body>

    @yield('content')

    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    {{-- AOS --}}
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
        });
    </script>

    {{-- Script khusus halaman --}}
    @stack('scripts')

    {{-- Livewire Scripts --}}
    @livewireScripts

</body>

</html>