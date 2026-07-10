<div class="row g-3 mb-4">

    <!-- Skor Risiko Global -->
    <div class="col-lg col-md-6">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <small class="text-muted">
                    Skor Risiko Global
                </small>

                <div class="d-flex justify-content-between align-items-center mt-3">

                    <div>

                        <h2 class="fw-bold mb-0">

                            {{ $averageRisk ?? 0 }}

                            <small>/100</small>

                        </h2>

                        <small class="text-primary">

                            Rata-rata Tingkat Risiko

                        </small>

                    </div>

                    <div>

                        <span class="badge bg-success">

                            AKTIF

                        </span>

                    </div>

                </div>

                <div class="mini-chart mt-3">

                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>

                </div>

            </div>

        </div>

    </div>

    <!-- Negara Risiko Tinggi -->
    <div class="col-lg col-md-6">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <small class="text-muted">

                    Negara Risiko Tinggi

                </small>

                <h2 class="mt-3">

                    {{ $highRisk }}

                </h2>

                <span class="badge bg-danger">

                    Tinggi

                </span>

            </div>

        </div>

    </div>

    <!-- Negara Risiko Sedang -->
    <div class="col-lg col-md-6">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <small class="text-muted">

                    Negara Risiko Sedang

                </small>

                <h2 class="mt-3">

                    {{ $mediumRisk }}

                </h2>

                <span class="badge bg-warning text-dark">

                    Sedang

                </span>

            </div>

        </div>

    </div>

    <!-- Negara Risiko Rendah -->
    <div class="col-lg col-md-6">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <small class="text-muted">

                    Negara Risiko Rendah

                </small>

                <h2 class="mt-3">

                    {{ $lowRisk }}

                </h2>

                <span class="badge bg-success">

                    Rendah

                </span>

            </div>

        </div>

    </div>

    <!-- Total Negara -->
    <div class="col-lg col-md-6">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <small class="text-muted">

                    Total Negara

                </small>

                <h2 class="mt-3">

                    {{ $totalCountries }}

                </h2>

                <span class="badge bg-primary">

                    Data Tersimpan

                </span>

            </div>

        </div>

    </div>

</div>