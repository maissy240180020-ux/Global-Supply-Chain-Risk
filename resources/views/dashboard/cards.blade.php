<div class="row g-3 mb-4">

    <!-- Global Risk Score -->
    <div class="col-lg col-md-6">
        <div class="card dashboard-card h-100">
            <div class="card-body">

                <small class="text-muted">
                    Global Risk Score
                </small>

                <div class="d-flex justify-content-between align-items-center mt-3">

                    <div>

                        <h2 class="fw-bold mb-0">
                           {{ $averageRisk ?? 0 }}
                            <small>/100</small>
                        </h2>

                        <small class="text-warning">
                            Average Risk
                        </small>

                    </div>

                    <div>

                        <span class="badge bg-warning">
                            LIVE
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

    <!-- High Risk -->
    <div class="col-lg col-md-6">
        <div class="card dashboard-card h-100">
            <div class="card-body">

                <small class="text-muted">
                    High Risk Countries
                </small>

                <h2 class="mt-3">
                    {{ $highRisk }}
                </h2>

                <span class="badge bg-danger">
                    High
                </span>

            </div>
        </div>
    </div>

    <!-- Medium Risk -->
    <div class="col-lg col-md-6">
        <div class="card dashboard-card h-100">
            <div class="card-body">

                <small class="text-muted">
                    Medium Risk Countries
                </small>

                <h2 class="mt-3">
                    {{ $mediumRisk }}
                </h2>

                <span class="badge bg-warning text-dark">
                    Medium
                </span>

            </div>
        </div>
    </div>

    <!-- Low Risk -->
    <div class="col-lg col-md-6">
        <div class="card dashboard-card h-100">
            <div class="card-body">

                <small class="text-muted">
                    Low Risk Countries
                </small>

                <h2 class="mt-3">
                    {{ $lowRisk }}
                </h2>

                <span class="badge bg-success">
                    Low
                </span>

            </div>
        </div>
    </div>

    <!-- Total Countries -->
    <div class="col-lg col-md-6">
        <div class="card dashboard-card h-100">
            <div class="card-body">

                <small class="text-muted">
                    Total Countries
                </small>

                <h2 class="mt-3">
                    {{ $totalCountries }}
                </h2>

                <span class="badge bg-primary">
                    Database
                </span>

            </div>
        </div>
    </div>

</div>