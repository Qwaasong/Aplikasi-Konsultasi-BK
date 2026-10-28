<header class="navbar-header">
    <nav class="container navbar-container">

        {{-- Logo & Brand --}}
        <a href="{{ route('landing') }}" class="navbar-brand">
            <img
                src="{{ asset('asset/image/SMKLogo.png') }}"
                alt="Logo SMKN 9 Malang"
                class="navbar-logo"
            >

            <div class="navbar-brand-text">
                <span class="navbar-brand-title">BK Skanawa</span>
                <span class="navbar-brand-subtitle">SMK Negeri 9 Malang</span>
            </div>
        </a>

        {{-- Mobile Toggle --}}
        <button
            type="button"
            class="navbar-toggle"
            id="navbarToggle"
            aria-label="Buka menu"
            aria-expanded="false"
        >
            <i class="fa-solid fa-bars"></i>
        </button>

        {{-- Navigation Content --}}
        <div class="navbar-content" id="navbarContent">

            <ul class="navbar-menu">

                {{-- Beranda --}}
                <li>
                    <a
                        href="{{ route('landing') }}"
                        class="navbar-link {{ request()->routeIs('landing') ? 'active' : '' }}"
                    >
                        Beranda
                    </a>
                </li>

                {{-- Layanan BK --}}
                <li>
                    <a
                        href="{{ route('layanan') }}"
                        class="navbar-link {{ request()->routeIs('layanan') ? 'active' : '' }}"
                    >
                        Layanan BK
                    </a>
                </li>

                {{-- Asesmen --}}
                <li class="navbar-dropdown">

                    <a
                        href="#"
                        class="navbar-link navbar-dropdown-toggle"
                        id="asesmenToggle"
                    >
                        <span>Asesmen</span>

                        <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                    </a>

                    <ul class="nav-dropdown-menu">

                        <li>
                            <a
                                href="{{ route('asesmen.akpd') }}"
                                class="nav-dropdown-item"
                            >
                                AKPD
                            </a>
                        </li>

                        <li>
                            <a
                                href="{{ route('asesmen.gaya-belajar') }}"
                                class="nav-dropdown-item"
                            >
                                Gaya Belajar
                            </a>
                        </li>

                        <li>
                            <a
                                href="{{ route('asesmen.dcm') }}"
                                class="nav-dropdown-item"
                            >
                                DCM
                            </a>
                        </li>

                        <li>
                            <a
                                href="{{ route('asesmen.sosiometri') }}"
                                class="nav-dropdown-item"
                            >
                                Sosiometri
                            </a>
                        </li>

                        <li>
                            <a
                                href="{{ route('asesmen.tes-bakat-minat') }}"
                                class="nav-dropdown-item"
                            >
                                Tes Bakat Minat
                            </a>
                        </li>

                    </ul>
                </li>

            </ul>

            {{-- Login / Daftar / Dashboard --}}
            <div class="navbar-actions">

                @guest

                    <a
                        href="{{ route('login') }}"
                        class="login-button btn-outline"
                    >
                        Login
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="login-button btn-primary"
                    >
                        Daftar
                    </a>

                @else

                    @php
                        $route = match(auth()->user()->role) {
                            'admin' => 'admin.dashboard',
                            'guru_bk' => 'konselor.dashboard',
                            default => 'landing',
                        };
                    @endphp

                    <a
                        href="{{ route($route) }}"
                        class="login-button btn-primary"
                    >
                        Dashboard
                    </a>

                @endguest

            </div>

        </div>
    </nav>
</header>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const navbarToggle = document.getElementById('navbarToggle');
    const navbarContent = document.getElementById('navbarContent');
    const asesmenToggle = document.getElementById('asesmenToggle');
    const navbarDropdown = document.querySelector('.navbar-dropdown');

    if (!navbarToggle || !navbarContent || !asesmenToggle || !navbarDropdown) {
        return;
    }

    navbarToggle.addEventListener('click', function () {

        const isOpen = navbarContent.classList.toggle('show');

        navbarToggle.classList.toggle('active', isOpen);

        navbarToggle.setAttribute(
            'aria-expanded',
            isOpen ? 'true' : 'false'
        );

        const icon = navbarToggle.querySelector('i');

        if (isOpen) {

            icon.classList.remove('fa-bars');
            icon.classList.add('fa-xmark');

        } else {

            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bars');

            navbarDropdown.classList.remove('open');
        }
    });

    asesmenToggle.addEventListener('click', function (event) {

        if (window.innerWidth <= 768) {

            event.preventDefault();

            navbarDropdown.classList.toggle('open');
        }
    });

    const navLinks = navbarContent.querySelectorAll(
        '.navbar-link:not(.navbar-dropdown-toggle), .nav-dropdown-item'
    );

    navLinks.forEach(function (link) {

        link.addEventListener('click', function () {

            if (window.innerWidth <= 768) {

                navbarContent.classList.remove('show');
                navbarDropdown.classList.remove('open');

                navbarToggle.classList.remove('active');

                navbarToggle.setAttribute(
                    'aria-expanded',
                    'false'
                );

                const icon = navbarToggle.querySelector('i');

                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        });
    });

    window.addEventListener('resize', function () {

        if (window.innerWidth > 768) {

            navbarContent.classList.remove('show');
            navbarDropdown.classList.remove('open');

            navbarToggle.classList.remove('active');

            navbarToggle.setAttribute(
                'aria-expanded',
                'false'
            );

            const icon = navbarToggle.querySelector('i');

            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bars');
        }
    });

});
</script>
@endpush