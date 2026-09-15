<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? (isset($title) ? $title : (config('app.name') ? config('app.name') . ' - Bimbingan Konseling' : 'Aplikasi Bimbingan Konseling')) }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="bg-gray-50 font-sans text-gray-800 flex h-screen overflow-hidden">

        {{-- MOBILE NAVIGATION --}}
        <livewire:layout.navigation />

        {{-- DESKTOP SIDEBAR --}}
        <div class="hidden md:block h-full flex-shrink-0">
            <livewire:partials.sidebar />
        </div>

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto bg-white pt-14 md:pt-0">
            {{ $slot }}
        </main>

    </div>
</body>

</html>