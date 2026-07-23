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