<div class="row">

    <!-- Ringkasan Cuaca -->
    <div class="col-lg-3 mb-4">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <h6 class="fw-bold">
                        🌤 Ringkasan Cuaca
                    </h6>

                </div>

                @if($cuaca)

                    <h1 class="mt-3">

                        {{ $cuaca['temperature_2m'] }}°C

                    </h1>

                    <small class="text-muted">

                        Jakarta, Indonesia

                    </small>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">

                        <small>Kecepatan Angin</small>

                        <strong>{{ $cuaca['wind_speed_10m'] }} km/jam</strong>

                    </div>

                    <div class="d-flex justify-content-between">

                        <small>Update</small>

                        <strong>Realtime</strong>

                    </div>

                @else

                    <div class="alert alert-danger">

                        API Cuaca gagal diakses.

                    </div>

                @endif

            </div>

        </div>

    </div>

    <!-- Nilai Tukar -->
    <div class="col-lg-3 mb-4">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <h6 class="fw-bold">

                        💱 Nilai Tukar

                    </h6>

                </div>

                <h3 class="mt-3">

                    Coming Soon

                </h3>

                <small class="text-success">

                    Akan menggunakan API

                </small>

                <hr>

                <small class="text-muted">

                    Realtime Exchange Rate

                </small>

            </div>

        </div>

    </div>

    <!-- Berita -->
    <div class="col-lg-3 mb-4">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <h6 class="fw-bold">

                    📰 Berita

                </h6>

                <hr>

                <p>

                    Akan menggunakan News API.

                </p>

                <small class="text-muted">

                    Realtime

                </small>

            </div>

        </div>

    </div>

    <!-- Informasi Negara -->
    <div class="col-lg-3 mb-4">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <h6 class="fw-bold">

                    🌍 Informasi Negara

                </h6>

                <hr>

                <h4>

                    {{ $totalCountries }}

                </h4>

                <small>

                    Total Negara

                </small>

                <h5 class="mt-3">

                    High Risk : {{ $highRisk }}

                </h5>

            </div>

        </div>

    </div>

</div>