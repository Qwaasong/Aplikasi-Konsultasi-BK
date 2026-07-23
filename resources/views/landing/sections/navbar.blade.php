<header class="navbar-header">

    <nav class="container navbar-container">

        <a href="{{ route('landing') }}" class="navbar-logo d-flex align-items-center gap-3 text-decoration-none">
            <img src="{{ asset('asset/image/SMKLogo.png') }}" alt="Logo SMKN 9 Malang">
            <span class="fw-bold fs-5 text-black mb-0" style="margin-top: 2px;">Aplikasi Konsultasi BK</span>
        </a>

        <ul class="navbar-menu">

            <li>
                <a href="{{ route('landing') }}"
                    class="navbar-link {{ request()->routeIs('landing') ? 'active' : '' }}">
                    Beranda
                </a>
            </li>

            <li>
                <a href="#" class="navbar-link">
                    Kehadiran Siswa
                </a>
            </li>

            <li>
                <a href="#" class="navbar-link">
                    Peminatan Siswa
                </a>
            </li>

        </ul>

        <a href="{{ route('login') }}" class="login-button btn-primary">
            Login
        </a>

    </nav>

</header>