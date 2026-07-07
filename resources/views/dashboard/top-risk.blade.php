<div class="card dashboard-card mb-4">

    <div class="card-header bg-white">

        <h5 class="mb-0">
            🔥 Top 5 Highest Risk Countries
        </h5>

    </div>

    <div class="card-body">

        @foreach($topRiskCountries as $index => $country)

        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">

            <div>

                <strong>{{ $index + 1 }}.</strong>

                {{ $country->country_name }}

            </div>

            <div>

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

                   {{ number_format($country->risk_score, 0) }}
                </span>

            </div>

        </div>

        @endforeach

        <div class="text-center mt-3">

            <a href="{{ route('countries.index') }}"
               class="btn btn-outline-primary btn-sm">

                View All Countries

            </a>

        </div>

    </div>

</div>