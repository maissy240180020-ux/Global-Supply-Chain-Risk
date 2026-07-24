@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
    .welcome-card {
        background: linear-gradient(135deg, #0a192f 0%, #1e3a8a 100%);
        border-radius: 16px;
        color: white;
        box-shadow: 0 10px 25px -5px rgba(30, 58, 138, 0.4);
        position: relative;
        overflow: hidden;
    }
    
    .welcome-card::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: url('data:image/svg+xml;utf8,<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" stroke="rgba(255,255,255,0.05)" stroke-width="2" fill="none"/></svg>') repeat;
        opacity: 0.3;
        pointer-events: none;
    }
    
    .badge-realtime {
        background-color: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .badge-realtime i {
        animation: blink 2s infinite;
    }

    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }
</style>

<div class="container-fluid">

    <!-- Welcome Card -->
    <div class="card border-0 welcome-card mb-4 p-4 p-md-5">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <h2 class="fw-bold mb-0">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                </div>
                <p class="text-white-50 mb-3" style="font-size: 1.05rem;">
                    Berikut adalah ringkasan data risiko rantai pasok global per tanggal <strong id="realtime-clock" class="text-white">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i:s') }} WIB</strong>.
                </p>
                <div class="badge-realtime">
                    <i class="bi bi-record-circle-fill"></i> Realtime Monitoring Active
                </div>
            </div>
            
            <div class="col-md-4 mt-3 mt-md-0 d-flex justify-content-md-end">
                <!-- Pemilih Negara -->
                <div class="bg-white p-2 rounded shadow-sm border border-light d-flex align-items-center gap-2" style="max-width: 300px;">
                    <label for="country_id" class="fw-semibold text-secondary mb-0 ps-1"><i class="bi bi-geo-alt-fill text-primary"></i></label>
                    <form action="{{ route('dashboard') }}" method="GET" id="countrySelectForm" class="m-0 flex-grow-1">
                        <select name="country_id" id="country_id" class="form-select form-select-sm border-0" style="font-weight: 500; background-color: transparent; box-shadow: none;" onchange="this.form.submit()">
                            <option value="">Semua Negara</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" {{ $selectedCountry && $selectedCountry->id == $c->id ? 'selected' : '' }}>
                                    {{ $c->country_name }} ({{ $c->country_code }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu Statistik Utama --}}
    @include('dashboard.cards')

    <div class="row">
        <!-- Kolom Kiri: Peta Risiko Utama & Indikator Makroekonomi -->
        <div class="col-lg-8 col-12">
            @include('dashboard.risk-map')
            @include('dashboard.indicators')
        </div>

        <!-- Kolom Kanan: Top 5 Negara Berisiko & Grafik Distribusi Risiko -->
        <div class="col-lg-4 col-12">
            @include('dashboard.top-risk')

            <!-- Distribusi Risiko Doughnut Chart -->
            <div class="card dashboard-card mb-4" style="border-radius: 16px; border: none; box-shadow: 0 10px 20px rgba(0,0,0,0.04); transition: transform 0.2s, box-shadow 0.2s;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">
                        <i class="bi bi-pie-chart-fill text-primary me-2"></i> Distribusi Risiko
                    </h5>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div style="height:250px; position:relative;">
                        <canvas id="riskChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Integrasi API Realtime (Cuaca, Kurs & Sentimen Berita) --}}
    @include('dashboard.weather')

    {{-- Pemantauan Pelabuhan Internasional --}}
    @include('dashboard.port-map')

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function () {

    // Realtime Clock
    function updateClock() {
        const clockElement = document.getElementById('realtime-clock');
        if (!clockElement) return;

        const now = new Date();
        const day = now.getDate();
        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const month = monthNames[now.getMonth()];
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        clockElement.innerText = `${day} ${month} ${year}, ${hours}:${minutes}:${seconds} WIB`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Count Up Animation
    const counters = document.querySelectorAll('.count-up');
    const speed = 200; 

    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const inc = target / speed;

            if (count < target) {
                counter.innerText = Math.ceil(count + inc);
                setTimeout(updateCount, 10);
            } else {
                counter.innerText = target;
            }
        };
        updateCount();
    });

    // Chart JS
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

                    '#ef4444', // pastel-ish red
                    '#f59e0b', // pastel-ish yellow
                    '#10b981'  // pastel-ish green

                ],

                borderWidth: 0,
                hoverOffset: 5

            }]

        },

        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            family: "'Poppins', sans-serif",
                            size: 12
                        }
                    }
                }
            }
        }
    });

});

</script>
@endpush