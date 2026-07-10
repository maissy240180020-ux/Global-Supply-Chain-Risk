@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="mb-4">

        <h2 class="fw-bold">
            Dashboard
        </h2>

        <p class="text-muted">
            Selamat datang di Sistem Monitoring Risiko Rantai Pasok Global
        </p>

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