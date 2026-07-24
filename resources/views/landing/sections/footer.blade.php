<footer class="footer">

    <div class="container footer-container" data-aos="fade-up">

        {{-- Logo & Identitas --}}
        <div class="footer-brand">

            <img
                src="{{ asset('asset/image/SMKLogo.png') }}"
                alt="Logo SMKN 9 Malang"
                class="footer-logo">

            <h2 class="footer-title">
                Bimbingan & Konseling
            </h2>

            <p class="footer-subtitle">
                Layanan BK SMK Negeri 9 Malang
            </p>

        </div>

        {{-- Menu --}}
        <div class="footer-column">

            <h3 class="footer-heading">
                Menu
            </h3>

            <ul class="footer-list">

                <li>
                    <a href="{{ route('landing') }}" class="footer-link">
                        Beranda
                    </a>
                </li>

                <li>
                    <a href="#" class="footer-link">
                        Kehadiran Siswa
                    </a>
                </li>

                <li>
                    <a href="#" class="footer-link">
                        Peminatan Siswa
                    </a>
                </li>

                <li>
                    <a href="{{ route('login') }}" class="footer-link">
                        Login
                    </a>
                </li>

                <li>
                    <a href="{{ route('register') }}" class="footer-link">
                        Daftar
                    </a>
                </li>
                
            </ul>

        </div>

        {{-- Layanan --}}
        <div class="footer-column">

            <h3 class="footer-heading">
                Layanan
            </h3>

            <ul class="footer-list">

                <li>Konseling Individu</li>

                <li>Konseling Kelompok</li>

                <li>Kunjungan Rumah</li>

                <li>Konferensi Kasus</li>

                <li>Alih Tangan Kasus</li>

            </ul>

        </div>

        {{-- Kontak --}}
        <div class="footer-column">

            <h3 class="footer-heading">
                Kontak
            </h3>

            <ul class="footer-list">

                <li>
                    <a href="#" class="footer-link">
                        <i class="fa-solid fa-location-dot"></i>
                        Alamat
                    </a>
                </li>

                <li>
                    <a href="#" class="footer-link">
                        <i class="fa-solid fa-phone"></i>
                        No. Telp
                    </a>
                </li>

                <li>
                    <a href="#" class="footer-link">
                        <i class="fa-solid fa-envelope"></i>
                        Email
                    </a>
                </li>

            </ul>

        </div>

    </div>

    {{-- Copyright --}}
    <div class="footer-bottom">

        <div class="container">

            <p>
                © {{ date('Y') }} Bimbingan & Konseling SMK Negeri 9 Malang.
                All Rights Reserved.
            </p>

        </div>

    </div>

</footer>