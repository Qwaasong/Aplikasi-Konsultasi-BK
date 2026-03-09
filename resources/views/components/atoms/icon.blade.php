@props([
    'variant' => 'dashboard',
    'size' => 'md',
    'color' => 'currentColor'
])

@php
    // Class dasar 
    $baseClasses = 'inline-flex justify-center items-center transition duration-200 shrink-0 [&>svg]:w-full [&>svg]:h-full';

    // Ukuran berbeda berdasarkan size
    $sizes = [
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6',
        'xl' => 'w-8 h-8',
        '2xl'=> 'w-10 h-10',
    ];
@endphp

<span {{ $attributes->merge(['class' => "$baseClasses {$sizes[$size]}"]) }}>

@switch($variant)

    {{-- Dashboard --}}
    @case('dashboard')
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="{{ $color }}"><path d="M240-200h120v-200q0-17 11.5-28.5T400-440h160q17 0 28.5 11.5T600-400v200h120v-360L480-740 240-560v360Zm-80 0v-360q0-19 8.5-36t23.5-28l240-180q21-16 48-16t48 16l240 180q15 11 23.5 28t8.5 36v360q0 33-23.5 56.5T720-120H560q-17 0-28.5-11.5T520-160v-200h-80v200q0 17-11.5 28.5T400-120H240q-33 0-56.5-23.5T160-200Zm320-270Z"/></svg>
    @break


    {{-- Consultation --}}
    @case('consultation')
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="{{ $color }}"><path d="M240-400h320v-80H240v80Zm0-120h480v-80H240v80Zm0-120h480v-80H240v80Zm-80 400q-33 0-56.5-23.5T80-320v-480q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H587l-74 110q-6 9-14.5 13.5T480-112q-10 0-18.5-4.5T447-130l-74-110H160Zm320 16 64-96h256v-480H160v480h256l64 96Zm0-336Z"/></svg>
    @break

    {{-- Student --}}
    @case('student')
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="{{ $color }}"><path d="M242-249q-20-11-31-29.5T200-320v-192l-96-53q-11-6-16-15t-5-20q0-11 5-20t16-15l338-184q9-5 18.5-7.5T480-829q10 0 19.5 2.5T518-819l381 208q10 5 15.5 14.5T920-576v256q0 17-11.5 28.5T880-280q-17 0-28.5-11.5T840-320v-236l-80 44v192q0 23-11 41.5T718-249L518-141q-9 5-18.5 7.5T480-131q-10 0-19.5-2.5T442-141L242-249Zm238-203 274-148-274-148-274 148 274 148Zm0 241 200-108v-151l-161 89q-9 5-19 7.5t-20 2.5q-10 0-20-2.5t-19-7.5l-161-89v151l200 108Zm0-241Zm0 121Zm0 0Z"/></svg>
    @break

    {{-- User --}}
    @case('user')
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="{{ $color }}"><path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-240v-32q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v32q0 33-23.5 56.5T720-160H240q-33 0-56.5-23.5T160-240Zm80 0h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/></svg>
    @break

    {{-- Chevron --}}
    @case('chevron')
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="{{ $color }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    @break

    {{-- Edit --}}
    @case('edit')
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="{{ $color }}"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
    @break

    {{-- Delete --}}
    @case('delete')
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="{{ $color }}"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
    @break

    {{-- Filter --}}
    @case('filter')
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="{{ $color }}"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
    @break

    {{-- Refresh --}}
    @case('refresh')
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="{{ $color }}"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    @break

    {{-- Logout --}}
    @case('logout')
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="{{ $color }}"><path d="M200-120q-33 0-56.5-23.5T120-200v-120q0-17 11.5-28.5T160-360q17 0 28.5 11.5T200-320v120h560v-560H200v120q0 17-11.5 28.5T160-600q-17 0-28.5-11.5T120-640v-120q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm266-320H160q-17 0-28.5-11.5T120-480q0-17 11.5-28.5T160-520h306l-74-74q-12-12-11.5-28t11.5-28q12-12 28.5-12.5T449-651l143 143q6 6 8.5 13t2.5 15q0 8-2.5 15t-8.5 13L449-309q-12 12-28.5 11.5T392-310q-11-12-11.5-28t11.5-28l74-74Z"/></svg>
    @break

    {{-- Search --}}
    @case('search')
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="{{ $color }}"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
    @break

    {{-- Plus --}}
    @case('plus')
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="{{ $color }}"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
    @break

@endswitch

</span>