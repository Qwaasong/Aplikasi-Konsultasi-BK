<footer class="footer">

    <div class="container footer-container" data-aos="fade-up">

        <div class="footer-brand">

            <img
                src="{{ asset('asset/image/SMKLogo.png') }}"
                alt="Logo SMKN 9 Malang"
                class="footer-logo">

            <h2 class="footer-title">
                Bimbingan & Konseling
            </h2>

            <p class="footer-subtitle">
                Layanan Bimbingan dan Konseling
                SMK Negeri 9 Malang.
            </p>

        </div>

        {{-- MENU --}}
        <div class="footer-column">

            <h3 class="footer-heading">
                Menu
            </h3>

            <ul class="footer-menu">

                <li>
                    <a href="{{ route('landing') }}" class="footer-link">
                        Beranda
                    </a>
                </li>

                <li>
                    <a href="{{ route('layanan') }}" class="footer-link">
                        Layanan BK
                    </a>
                </li>

                <li class="footer-dropdown">

                    <button type="button" class="footer-link footer-dropdown-toggle">
                        <span>Asesmen</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>

                    <ul class="footer-dropdown-menu">

                        <li>
                            <a href="{{ route('asesmen.akpd') }}">
                                AKPD
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('asesmen.gaya-belajar') }}">
                                Gaya Belajar
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('asesmen.dcm') }}">
                                DCM
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('asesmen.sosiometri') }}">
                                Sosiometri
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('asesmen.tes-bakat-minat') }}">
                                Tes Bakat Minat
                            </a>
                        </li>

                    </ul>

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

        {{-- LAYANAN --}}
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

        {{-- KONTAK --}}
        <div class="footer-column">

            <h3 class="footer-heading">
                Kontak
            </h3>

            <ul class="footer-list">

                <li>
                    <a href="https://maps.app.goo.gl/P3u2q5SVZfp46LZB9" class="footer-link">
                        <i class="fa-solid fa-location-dot"></i>
                        Alamat
                    </a>
                </li>

                <li>
                    <a href="tel:(0341) 727998" class="footer-link">
                        <i class="fa-solid fa-phone"></i>
                        No. Telp
                    </a>
                </li>

                <li>
                    <a href="mailto:humas@smkn9malang.sch.id" class="footer-link">
                        <i class="fa-solid fa-envelope"></i>
                        Email
                    </a>
                </li>

            </ul>

        </div>

    </div>

    {{-- COPYRIGHT --}}
    <div class="footer-bottom">

        <div class="container">

            <p>
                © {{ date('Y') }} Bimbingan & Konseling SMK Negeri 9 Malang.
                All Rights Reserved.
            </p>

        </div>

    </div>

</footer>