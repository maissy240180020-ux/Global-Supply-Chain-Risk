<div class="card dashboard-card mb-4">

    <div class="card-header bg-white">

        <h5 class="mb-0">

            🔥 5 Negara dengan Risiko Tertinggi

        </h5>

    </div>

    <div class="card-body">

        @forelse($topRiskCountries as $index => $country)

        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">

            <div>

                <div class="fw-semibold">

                    {{ $index + 1 }}. {{ $country->country_name }}

                </div>

                <small class="text-muted">

                    Tingkat Risiko :
                    {{ $country->risk_level }}

                </small>

            </div>

            <div class="text-end">

                @php

                    $badge = 'bg-success';

                    if($country->risk_level == 'Medium'){
                        $badge = 'bg-warning text-dark';
                    }

                    if($country->risk_level == 'High'){
                        $badge = 'bg-danger';
                    }

                @endphp

                <span class="badge {{ $badge }}">

                    {{ number_format($country->risk_score,0) }}

                </span>

            </div>

        </div>

        @empty

        <div class="text-center py-4">

            <i class="bi bi-database text-secondary fs-1"></i>

            <p class="mt-3 text-muted">

                Belum ada data negara.

            </p>

        </div>

        @endforelse

        <div class="text-center mt-4">
            <a
                href="{{ route('countries.index') }}"
                class="btn btn-outline-primary">
                <i class="bi bi-globe2"></i>
                Lihat Semua Negara
            </a>
        </div>

    </div>

</div>