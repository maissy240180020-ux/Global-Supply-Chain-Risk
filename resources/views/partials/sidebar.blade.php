<div class="sidebar">

    <!-- Logo -->
    <div class="logo text-center py-4 border-bottom">

        <h4 class="fw-bold text-white mb-1">
            🌍 SIMRPG
        </h4>

        <small class="text-light">
            Sistem Monitoring Risiko Global
        </small>

    </div>

    <!-- Admin -->
    <div class="sidebar-footer p-3 border-bottom">

        <div class="fw-bold text-white">

            Administrator

        </div>

        <small class="text-light">

            admin@simrpg.com

        </small>

    </div>

    <!-- Menu -->
    <ul class="menu">

        <li>

            <a href="{{ route('dashboard') }}">

                <i class="bi bi-speedometer2"></i>

                Dashboard

            </a>

        </li>

        <li>

            <a href="{{ route('countries.index') }}">

                <i class="bi bi-globe2"></i>

                Dashboard Negara Global

            </a>

        </li>

        <li>

            <a href="{{ route('risk.index') }}">

                <i class="bi bi-exclamation-triangle"></i>

                Mesin Penilaian Risiko

            </a>

        </li>

        <li>

            <a href="{{ route('cuaca.index') }}">

                <i class="bi bi-cloud-sun"></i>

                Pemantauan Cuaca Global

            </a>

        </li>

        <li>

           <a href="{{ route('nilai-tukar.index') }}">

                <i class="bi bi-currency-exchange"></i>

                Dashboard Nilai Tukar

            </a>

        </li>

        <li>

            <a href="{{ route('berita.index') }}">

                <i class="bi bi-newspaper"></i>

                Analisis Berita

            </a>

        </li>

        <li>

            <a href="{{ route('pelabuhan.index') }}">

                <i class="bi bi-geo-alt"></i>

                Dashboard Lokasi Pelabuhan

            </a>

        </li>

        <li>

            <a href="/visualisasi">

                <i class="bi bi-bar-chart"></i>

                Dashboard Visualisasi Data

            </a>

        </li>

        <li>

            <a href="{{ route('compare.index') }}">

                <i class="bi bi-columns-gap"></i>

                Perbandingan Negara

            </a>

        </li>

        <li>

            <a href="{{ route('watchlist.index') }}">

                <i class="bi bi-star"></i>

                Daftar Pantauan Favorit

            </a>

        </li>

        <li>

            <a href="{{ route('admin.index') }}">

                <i class="bi bi-person-gear"></i>

                Dashboard Admin

            </a>

        </li>

    </ul>

</div>