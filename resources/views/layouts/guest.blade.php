<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex">

        <!-- KIRI : FORM -->
        <div class="w-full md:w-1/2 flex flex-col items-center justify-center bg-gray-100 space-y-6">

            <div class="w-full sm:max-w-md space-y-6">

                <div class="mb-12">
                    <x-judul class="text-3xl font-bold text-left">
                        {{ __('Welcome Back') }}
                    </x-judul>

                    <x-deskripsi class="text-gray-600 mt-2 mb-8">
                        Bukan Member?
                        <a href="#" class="text-[var(--primary-color)] hover:underline">Daftar Disini</a>
                    </x-deskripsi>
                </div>


                <div class="px-6 py-4 shadow-md sm:rounded-lg bg-white">
                    {{ $slot }}
                </div>

            </div>

        </div>

        <!-- KANAN : GAMBAR -->
        <div class="hidden md:block md:w-1/2">
            <img src="{{ asset('images/wallpaper.jpg') }}" class="h-screen w-full object-cover">
        </div>

    </div>
</body>

</html>