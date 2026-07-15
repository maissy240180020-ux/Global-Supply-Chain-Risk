@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0">Dashboard Analytics</h2>
            <p class="text-muted mb-0">Sistem Monitoring Risiko Rantai Pasok Global Berbasis Multi-API</p>
        </div>
        
        <!-- Pemilih Negara (Fitur 1) -->
        <div class="bg-white p-2 rounded shadow-sm border border-light d-flex align-items-center gap-2">
            <label for="country_id" class="fw-semibold text-secondary mb-0">Negara:</label>
            <form action="{{ route('dashboard') }}" method="GET" id="countrySelectForm" class="m-0">
                <select name="country_id" id="country_id" class="form-select form-select-sm border-0 bg-light" style="font-weight: 500;" onchange="this.form.submit()">
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ $selectedCountry && $selectedCountry->id == $c->id ? 'selected' : '' }}>
                            {{ $c->country_name }} ({{ $c->country_code }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- Kartu Statistik --}}
    @include('dashboard.cards')

    <div class="row">

        <div class="col-lg-8">

            @include('dashboard.risk-map')

            @include('dashboard.indicators')

        </div>

        <div class="col-lg-4">

            @include('dashboard.top-risk')

        </div>

    </div>

    <!-- Distribusi Risiko -->
    <div class="card dashboard-card mb-4">

        <div class="card-header bg-white">

            <h5 class="mb-0">
                📊 Distribusi Risiko
            </h5>

        </div>

        <div class="card-body">

            <div style="height:300px; position:relative;">

                <canvas id="riskChart"></canvas>

            </div>

        </div>

    </div>

    {{-- Cuaca --}}
    @include('dashboard.weather')

    {{-- Dashboard Pelabuhan --}}
    @include('dashboard.port-map')

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function () {

    const ctx = document.getElementById('riskChart');

    if (!ctx || typeof window.Chart === 'undefined') {
        return;
    }

    new window.Chart(ctx, {

        type: 'doughnut',

        data: {

            labels: [

                'Risiko Tinggi',

                'Risiko Sedang',

                'Risiko Rendah'

            ],

            datasets: [{

                data: [

                    {{ $highRisk }},

                    {{ $mediumRisk }},

                    {{ $lowRisk }}

                ],

                backgroundColor: [

                    '#dc3545',

                    '#ffc107',

                    '#198754'

                ],

                borderWidth: 1

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {

                    position: 'bottom'

                }

            }

        }

    });

});

</script>

@endpush