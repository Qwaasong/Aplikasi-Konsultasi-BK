<header class="navbar-header">

    <nav class="container navbar-container">

        <a href="{{ route('landing') }}" class="navbar-brand">

            <img
                src="{{ asset('asset/image/SMKLogo.png') }}"
                alt="Logo SMKN 9 Malang"
                class="navbar-logo">

            <div class="navbar-brand-text">

                <span class="navbar-brand-title">
                    Aplikasi Konsultasi BK
                </span>

                <span class="navbar-brand-subtitle">
                    SMK Negeri 9 Malang
                </span>

            </div>
        </a>

        <ul class="navbar-menu">

            <li>
                <a href="{{ route('landing') }}"
                    class="navbar-link {{ request()->routeIs('landing') ? 'active' : '' }}">
                    Beranda
                </a>
            </li>

            <li>
                <a href="{{ route('layanan') }}" class="navbar-link">
                    Layanan BK
                </a>
            </li>

            <li class="navbar-dropdown">

                <a href="#" class="navbar-link">
                    Asesmen
                    <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                </a>

                <ul class="nav-dropdown-menu">

                    <li>
                        <a href="{{ route('asesmen.akpd') }}" class="nav-dropdown-item">
                            AKPD
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('asesmen.gaya-belajar') }}" class="nav-dropdown-item">
                            Gaya Belajar
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('asesmen.dcm') }}" class="nav-dropdown-item">
                            DCM
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('asesmen.sosiometri') }}" class="nav-dropdown-item">
                            Sosiometri
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('asesmen.tes-bakat-minat') }}" class="nav-dropdown-item">
                            Tes Bakat Minat
                        </a>
                    </li>

                </ul>

            </li>

        </ul>

        <div class="navbar-actions">

            @guest
                <a href="{{ route('login') }}" class="login-button btn-outline">
                    Login
                </a>

                <a href="{{ route('register') }}" class="login-button btn-primary">
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
                <a href="{{ route($route) }}" class="login-button btn-primary">
                    Dashboard
                </a>
            @endguest

        </div>

    </nav>

</header>