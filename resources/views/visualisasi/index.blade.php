@extends('layouts.app')

@section('title', 'Visualisasi Data Global | SIMRPG')

@section('content')
<style>
    /* ── Live Pulse Badge ── */
    @keyframes pulse-live {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.6; transform: scale(0.9); }
    }
    .live-dot {
        display: inline-block;
        width: 10px; height: 10px;
        border-radius: 50%;
        background: #ef4444; /* Red dot for live */
        animation: pulse-live 1.2s ease-in-out infinite;
        margin-right: 6px;
    }

    /* ── KPI value flash on update ── */
    @keyframes kpi-flash {
        0%   { color: #0284c7; transform: scale(1.05); }
        100% { color: inherit; transform: scale(1); }
    }
    .kpi-flash { animation: kpi-flash 0.6s ease-out forwards; }

    /* ── Spinner overlay ── */
    @keyframes spin { to { transform: rotate(360deg); } }
    .update-spinner {
        width: 14px; height: 14px;
        border: 2px solid #cbd5e1;
        border-top-color: #ef4444;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        display: inline-block;
        vertical-align: middle;
        margin-left: 8px;
    }

    /* ── Refresh bar progress ── */
    #refreshBar {
        position: fixed;
        top: 0; left: 0;
        height: 3px;
        width: 0%;
        background: linear-gradient(90deg, #3b82f6, #ef4444);
        transition: width 0.4s linear;
        z-index: 9999;
    }

    /* ── Card hover transition ── */
    .hover-card { transition: transform 0.25s ease, box-shadow 0.25s ease; border-radius: 14px !important; }
    .hover-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.06) !important; z-index: 10; }
    
    .chart-container-inner {
        position: relative;
        width: 100%;
        height: 280px;
    }
</style>

{{-- Refresh progress bar --}}
<div id="refreshBar"></div>

<div class="container-fluid py-2">

    {{-- ── Header ── --}}
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="font-size: 1.85rem;">
                📈 Pusat Visualisasi Data Global
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.95rem;">
                Analisis Statistik Multidimensi & Pemantauan Risiko Rantai Pasok Terpusat
            </p>
        </div>
        <div class="d-flex align-items-center gap-3">
            {{-- Permanent Live Badge --}}
            <div id="liveBadge" class="d-flex align-items-center px-4 py-2 rounded-pill shadow-sm"
                 style="background: #fff; border: 1px solid #e2e8f0; font-size: 0.85rem; font-weight: 700; color: #1e293b;">
                <span class="live-dot" id="liveDot"></span>
                <span id="liveStatusText">Data Real-Time Aktif</span>
                <span class="update-spinner d-none" id="refreshSpinner"></span>
            </div>
            
            <span class="text-muted fw-medium" style="font-size:0.75rem;" id="lastUpdatedText">
                Terakhir: <strong id="lastUpdatedVal">—</strong>
            </span>
        </div>
    </div>

    {{-- ── KPI Cards ── --}}
    <div class="row g-4 mb-4">
        {{-- Negara (Blue) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card hover-card border-0 h-100" style="background-color: #f0f9ff; border: 1px solid #bae6fd !important;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0 bg-white shadow-sm"
                         style="width: 54px; height: 54px; color: #0284c7;">
                        <i class="bi bi-globe-americas fs-3"></i>
                    </div>
                    <div>
                        <small class="fw-bold d-block text-uppercase" style="color: #0369a1; font-size: 0.65rem; letter-spacing: .05em;">Total Negara</small>
                        <h3 class="fw-bold mb-0 mt-1 stat-value" id="kpiTotalCountries" style="font-size: 1.7rem; color: #0c4a6e;">{{ $totalCountries }}</h3>
                        <small style="color: #0284c7; font-size: 0.72rem; font-weight: 500;">Dipantau sistem</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rata-rata risiko (Green) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card hover-card border-0 h-100" style="background-color: #f0fdf4; border: 1px solid #bbf7d0 !important;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0 bg-white shadow-sm"
                         style="width: 54px; height: 54px; color: #10b981;">
                        <i class="bi bi-shield-check fs-3"></i>
                    </div>
                    <div>
                        <small class="fw-bold d-block text-uppercase" style="color: #047857; font-size: 0.65rem; letter-spacing: .05em;">Rata-rata Risiko</small>
                        <h3 class="fw-bold mb-0 mt-1 stat-value" id="kpiAvgRisk" style="font-size: 1.7rem; color: #064e3b;">
                            {{ $avgGlobalRisk }} <span style="font-size:0.8rem;font-weight:600;opacity:0.6;">/100</span>
                        </h3>
                        <small style="color: #10b981; font-size: 0.72rem; font-weight: 500;">Indeks kerentanan</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Risiko Tinggi (Red) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card hover-card border-0 h-100" style="background-color: #fef2f2; border: 1px solid #fecaca !important;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0 bg-white shadow-sm"
                         style="width: 54px; height: 54px; color: #ef4444;">
                        <i class="bi bi-exclamation-triangle fs-3"></i>
                    </div>
                    <div>
                        <small class="fw-bold d-block text-uppercase" style="color: #b91c1c; font-size: 0.65rem; letter-spacing: .05em;">Risiko Tinggi</small>
                        <h3 class="fw-bold mb-0 mt-1 stat-value" id="kpiHighRisk" style="font-size: 1.7rem; color: #7f1d1d;">
                            {{ $highRiskCount }} <span style="font-size:0.8rem;font-weight:600;opacity:0.6;">Negara</span>
                        </h3>
                        <small style="color: #ef4444; font-size: 0.72rem; font-weight: 500;">Butuh atensi khusus</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pelabuhan (Orange) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card hover-card border-0 h-100" style="background-color: #fffbeb; border: 1px solid #fde68a !important;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0 bg-white shadow-sm"
                         style="width: 54px; height: 54px; color: #f59e0b;">
                        <i class="bi bi-anchor fs-3"></i>
                    </div>
                    <div>
                        <small class="fw-bold d-block text-uppercase" style="color: #b45309; font-size: 0.65rem; letter-spacing: .05em;">Infrastruktur Pelabuhan</small>
                        <h3 class="fw-bold mb-0 mt-1 stat-value" id="kpiTotalPorts" style="font-size: 1.7rem; color: #78350f;">{{ $totalPorts }}</h3>
                        <small style="color: #f59e0b; font-size: 0.72rem; font-weight: 500;">Titik logistik global</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Charts Grid ── --}}
    <div class="row g-4 mb-4">
        {{-- Chart 1: Distribusi Risiko --}}
        <div class="col-xl-4 col-lg-5 col-12">
            <div class="card hover-card border-0 shadow-sm h-100 bg-white">
                <div class="card-header bg-transparent py-3 border-light">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size:0.95rem;">🍩 Distribusi Tingkat Risiko Negara</h6>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div style="width:100%;max-width:250px;height:250px;position:relative;">
                        <canvas id="riskDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart 2: Top 10 Risiko --}}
        <div class="col-xl-8 col-lg-7 col-12">
            <div class="card hover-card border-0 shadow-sm h-100 bg-white">
                <div class="card-header bg-transparent py-3 border-light">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size:0.95rem;">🔥 Top 10 Negara Kerentanan Tertinggi</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container-inner">
                        <canvas id="topRiskChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart 3: Top Ports --}}
        <div class="col-xl-6 col-12">
            <div class="card hover-card border-0 shadow-sm h-100 bg-white">
                <div class="card-header bg-transparent py-3 border-light">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size:0.95rem;">🚢 10 Negara Infrastruktur Pelabuhan Terbanyak</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container-inner">
                        <canvas id="topPortsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart 4: Correlation Bubble with Trendline --}}
        <div class="col-xl-6 col-12">
            <div class="card hover-card border-0 shadow-sm h-100 bg-white">
                <div class="card-header bg-transparent py-3 border-light">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size:0.95rem;">📈 Analisis Korelasi (Inflasi vs Risiko)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container-inner">
                        <canvas id="correlationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Country Profile & Radar ── --}}
    <div class="card border-0 shadow-sm mb-4 hover-card" style="background:#fff; border-radius:14px; overflow:hidden;">
        <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-light">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2" style="font-size:1.1rem;">
                <i class="bi bi-radar text-primary"></i> Alat Profiling & Perbandingan Radar Negara
            </h5>
            <div class="d-flex align-items-center gap-2">
                <label for="radarCountrySelector" class="fw-semibold text-secondary mb-0" style="font-size:0.85rem;">Pilih Negara:</label>
                <select id="radarCountrySelector" class="form-select form-select-sm shadow-none"
                        style="width:220px;font-weight:600;border-radius:8px;border:1px solid #cbd5e1;height:38px;background-color:#f8fafc;">
                    @foreach($countries as $c)
                        <option value="{{ $c->country_code }}" {{ $c->country_code === 'ID' ? 'selected' : '' }}>
                            {{ $c->country_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card-body pt-0 px-4 pb-4 mt-3">
            <div class="row g-4 align-items-stretch">
                {{-- Profile Card --}}
                <div class="col-xl-4 col-lg-5 col-12">
                    <div class="card h-100 border-0 shadow-none" style="border-radius:12px;background:#f8fafc;border:1px solid #e2e8f0 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <img id="profileFlag" src="" alt="Flag"
                                     style="width:60px;height:40px;object-fit:cover;border-radius:6px;border:1px solid #cbd5e1;box-shadow:0 2px 4px rgba(0,0,0,.08);">
                                <div>
                                    <h4 class="fw-bold text-dark mb-0" style="font-size:1.3rem;" id="profileCountryName">Negara</h4>
                                    <span class="badge bg-dark px-2 mt-1" style="font-size:0.68rem;font-weight:600;letter-spacing:1px;" id="profileCountryCode">CODE</span>
                                </div>
                            </div>
                            <div class="vstack gap-3" style="font-size:0.85rem;">
                                <div class="d-flex align-items-center justify-content-between border-bottom border-light pb-2">
                                    <span class="text-secondary fw-medium text-nowrap me-3"><i class="bi bi-bank me-2 text-muted"></i>Ibu Kota</span>
                                    <strong class="text-dark text-end" id="profileCapital" style="word-break: break-word;">--</strong>
                                </div>
                                <div class="d-flex align-items-center justify-content-between border-bottom border-light pb-2">
                                    <span class="text-secondary fw-medium text-nowrap me-3"><i class="bi bi-cash-stack me-2 text-muted"></i>Mata Uang</span>
                                    <strong class="text-dark text-end" id="profileCurrency">--</strong>
                                </div>
                                <div class="d-flex align-items-center justify-content-between border-bottom border-light pb-2">
                                    <span class="text-secondary fw-medium text-nowrap me-3"><i class="bi bi-people me-2 text-muted"></i>Populasi</span>
                                    <strong class="text-dark text-end" id="profilePopulation">--</strong>
                                </div>
                                <div class="d-flex align-items-center justify-content-between border-bottom border-light pb-2">
                                    <span class="text-secondary fw-medium text-nowrap me-3"><i class="bi bi-thermometer-half me-2 text-muted"></i>Suhu & Cuaca</span>
                                    <div class="text-end">
                                        <span class="badge fw-bold" id="profileWeather" style="font-size:0.75rem; white-space: normal; text-align: right; line-height: 1.4; padding: 6px 10px; border-radius: 6px; display: inline-block;">--</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between border-bottom border-light pb-2">
                                    <span class="text-secondary fw-medium text-nowrap me-3"><i class="bi bi-anchor me-2 text-muted"></i>Total Pelabuhan</span>
                                    <strong class="text-dark fs-6 text-end" id="profilePorts">--</strong>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-1">
                                    <span class="text-secondary fw-medium text-nowrap me-3"><i class="bi bi-shield-exclamation me-2 text-muted"></i>Skor Risiko</span>
                                    <div class="text-end">
                                        <span class="badge" id="profileRiskBadge" style="font-size:0.85rem; font-weight:700; white-space: normal; text-align: right; line-height: 1.4; padding: 6px 12px; border-radius: 8px; display: inline-block;">--</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Radar Chart --}}
                <div class="col-xl-8 col-lg-7 col-12">
                    <div class="card h-100 border-0 shadow-none" style="border-radius:12px;background:#f8fafc;border:1px solid #e2e8f0 !important;">
                        <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                            <div style="width:100%;max-width:350px;height:350px;position:relative;">
                                <canvas id="comparisonRadarChart"></canvas>
                            </div>
                            <div class="mt-2 text-muted text-center" style="font-size:0.75rem;font-style:italic;">
                                *Nilai dinormalisasi (Skala 0-100) untuk komparasi metrik yang berimbang.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- Import ChartDataLabels Plugin --}}
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Register Plugin DataLabels Global
    Chart.register(ChartDataLabels);

    // =========================================================
    // 1. Initial Data
    // =========================================================
    let riskCounts      = @json($riskCounts);
    let topRiskData     = @json($topRisk);
    let topPortsData    = @json($topPorts);
    let correlationData = @json($correlationData);
    let globalAverages  = @json($globalAverages);
    let countriesList   = @json($countries->keyBy('country_code'));
    let totalCountriesCount = {{ $totalCountries }};

    Chart.defaults.font.family = 'Poppins, Inter, sans-serif';
    Chart.defaults.color       = '#64748b';

    const LIVE_DATA_URL = "{{ route('visualisasi.live-data') }}";
    const REFRESH_MS    = 30000; 

    // =========================================================
    // Linear Regression Algorithm for Trendline
    // =========================================================
    function calculateTrendLine(dataPoints) {
        let n = dataPoints.length;
        if (n === 0) return [];
        let sumX = 0, sumY = 0, sumXY = 0, sumXX = 0;
        let minX = Infinity, maxX = -Infinity;

        for (let p of dataPoints) {
            sumX += p.x;
            sumY += p.y;
            sumXY += (p.x * p.y);
            sumXX += (p.x * p.x);
            if (p.x < minX) minX = p.x;
            if (p.x > maxX) maxX = p.x;
        }

        let slope = (n * sumXY - sumX * sumY) / (n * sumXX - sumX * sumX);
        let intercept = (sumY - slope * sumX) / n;

        // Create two points for the line
        return [
            { x: minX, y: slope * minX + intercept },
            { x: maxX, y: slope * maxX + intercept }
        ];
    }

    // =========================================================
    // Custom Plugin for Center Text in Doughnut Chart
    // =========================================================
    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw: function(chart) {
            if (chart.config.type !== 'doughnut') return;
            const width = chart.width, height = chart.height, ctx = chart.ctx;
            ctx.restore();
            
            // Draw Total Number
            let fontSize = (height / 80).toFixed(2);
            ctx.font = 'bold ' + fontSize + "em sans-serif";
            ctx.textBaseline = "middle";
            ctx.fillStyle = "#1e293b";
            
            let text = totalCountriesCount.toString();
            let textX = Math.round((width - ctx.measureText(text).width) / 2);
            let textY = height / 2 - 10;
            ctx.fillText(text, textX, textY);

            // Draw Label
            let labelFontSize = (height / 250).toFixed(2);
            ctx.font = '600 ' + labelFontSize + "em sans-serif";
            ctx.fillStyle = "#64748b";
            let labelText = "Negara";
            let labelX = Math.round((width - ctx.measureText(labelText).width) / 2);
            ctx.fillText(labelText, labelX, height / 2 + 18);
            
            ctx.save();
        }
    };
    Chart.register(centerTextPlugin);


    // =========================================================
    // 2. Chart 1: Risk Distribution Doughnut
    // =========================================================
    const chartRiskDist = new Chart(document.getElementById('riskDistributionChart'), {
        type: 'doughnut',
        data: {
            labels: ['Tinggi', 'Sedang', 'Rendah'],
            datasets: [{
                data: [riskCounts.High, riskCounts.Medium, riskCounts.Low],
                backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '75%',
            plugins: { 
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11, weight: '600' } } },
                datalabels: { display: false } // Hide datalabels for donut
            }
        }
    });

    // =========================================================
    // 3. Chart 2: Top 10 Risk Horizontal Bar
    // =========================================================
    const chartTopRisk = new Chart(document.getElementById('topRiskChart'), {
        type: 'bar',
        data: {
            labels: topRiskData.map(c => c.country_name),
            datasets: [{
                label: 'Skor Risiko',
                data: topRiskData.map(c => Number(c.risk_score)),
                backgroundColor: 'rgba(239,68,68,.85)',
                borderRadius: 6,
                maxBarThickness: 20
            }]
        },
        options: {
            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
            layout: { padding: { right: 40 } },
            scales: {
                x: { max: 110, display: false },
                y: { grid: { display: false }, ticks: { font: { size: 10, weight: '600' }, color: '#334155' } }
            },
            plugins: { 
                legend: { display: false },
                datalabels: { display: false }
            }
        }
    });

    // =========================================================
    // 4. Chart 3: Top Ports Vertical Bar
    // =========================================================
    const chartTopPorts = new Chart(document.getElementById('topPortsChart'), {
        type: 'bar',
        data: {
            labels: topPortsData.map(c => c.country_name),
            datasets: [{
                label: 'Jumlah Pelabuhan',
                data: topPortsData.map(c => Number(c.total)),
                backgroundColor: 'rgba(245,158,11,.85)',
                borderRadius: {topLeft: 6, topRight: 6},
                maxBarThickness: 30
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            layout: { padding: { top: 30 } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10, weight: '600' }, color: '#334155' } },
                y: { display: false, beginAtZero: true }
            },
            plugins: { 
                legend: { display: false },
                datalabels: { display: false }
            }
        }
    });

    // =========================================================
    // 5. Chart 4: Correlation Bubble (Mixed with Line)
    // =========================================================
    const chartCorrelation = new Chart(document.getElementById('correlationChart'), {
        type: 'scatter', // Mixed chart base
        data: {
            datasets: [
                {
                    type: 'line',
                    label: 'Garis Tren',
                    data: calculateTrendLine(correlationData),
                    borderColor: 'rgba(220, 38, 38, 0.6)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    pointRadius: 0,
                    datalabels: { display: false }
                },
                {
                    type: 'bubble',
                    label: 'Negara',
                    data: correlationData,
                    backgroundColor: 'rgba(2,132,199,.65)',
                    borderColor: '#0284c7',
                    borderWidth: 1,
                    datalabels: { display: false }
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: {
                x: { title: { display: true, text: 'Tingkat Inflasi (%)', font: { weight: 'bold' } }, grid: { color: '#f1f5f9' } },
                y: { title: { display: true, text: 'Skor Risiko', font: { weight: 'bold' } }, max: 100, grid: { color: '#f1f5f9' } }
            },
            plugins: {
                legend: { position: 'top', labels: { boxWidth: 12, usePointStyle: true } },
                tooltip: { 
                    callbacks: { 
                        label: function(ctx) {
                            if (ctx.dataset.type === 'line') return 'Garis Tren';
                            return `${ctx.raw.country}: Inflasi ${ctx.raw.x}%, Risiko ${ctx.raw.y}`;
                        }
                    }
                }
            }
        }
    });

    // =========================================================
    // 6. Radar Chart (Country Profile)
    // =========================================================
    let radarChart = null;
    const profileEl = {
        flag: document.getElementById('profileFlag'), name: document.getElementById('profileCountryName'),
        code: document.getElementById('profileCountryCode'), capital: document.getElementById('profileCapital'),
        currency: document.getElementById('profileCurrency'), population: document.getElementById('profilePopulation'),
        weather: document.getElementById('profileWeather'), ports: document.getElementById('profilePorts'),
        riskBadge: document.getElementById('profileRiskBadge'),
    };

    function normalise(country, globalAvg) {
        return [
            parseFloat(country.risk_score || 0),
            Math.min(100, parseFloat(country.inflation || 0) * 5),
            Math.min(100, Math.max(0, (parseFloat(country.temperature || 25) + 5) * 2)),
            Math.min(100, parseInt(country.population || 0) / 10000000),
            Math.min(100, parseInt(country.port_count || 0) * 8)
        ];
    }

    function normaliseGlobal(g) {
        return [
            parseFloat(g.risk_score), Math.min(100, parseFloat(g.inflation) * 5),
            Math.min(100, Math.max(0, (parseFloat(g.temperature) + 5) * 2)),
            Math.min(100, parseInt(g.population) / 10000000), Math.min(100, parseFloat(g.port_count) * 8)
        ];
    }

    function updateCountryProfile(countryCode) {
        const country = countriesList[countryCode];
        if (!country) return;

        profileEl.flag.src = country.flag || 'https://flagcdn.com/w320/un.png';
        profileEl.name.textContent = country.country_name;
        profileEl.code.textContent = country.country_code;
        profileEl.capital.textContent = country.capital || '-';
        profileEl.currency.textContent = country.currency || 'USD';
        const pop = parseInt(country.population);
        profileEl.population.textContent = pop > 1000000 ? `${(pop/1000000).toFixed(1)} Jt jiwa` : `${pop.toLocaleString()} jiwa`;
        profileEl.weather.textContent = `${country.temperature}°C / ${country.weather}`;
        profileEl.weather.className = (country.temperature > 30 || country.temperature < 5) ? 'badge bg-warning text-dark fw-bold' : 'badge bg-info text-white fw-bold';
        profileEl.ports.textContent = `${country.port_count}`;
        
        const score = country.risk_score, level = country.risk_level;
        profileEl.riskBadge.textContent = `${score} (${level})`;
        profileEl.riskBadge.className = level === 'High' ? 'badge bg-danger text-white' : level === 'Medium' ? 'badge bg-warning text-dark' : 'badge bg-success text-white';

        renderRadar(country);
    }

    function renderRadar(country) {
        const radarData = {
            labels: ['Risiko', 'Inflasi', 'Suhu Lingkungan', 'Populasi', 'Pelabuhan'],
            datasets: [
                {
                    label: country.country_name,
                    data: normalise(country, globalAverages),
                    fill: true, backgroundColor: 'rgba(16,185,129,.2)', borderColor: '#10b981',
                    pointBackgroundColor: '#10b981', pointBorderColor: '#fff', borderWidth: 2
                },
                {
                    label: 'Rata-rata Global',
                    data: normaliseGlobal(globalAverages),
                    fill: true, backgroundColor: 'rgba(148,163,184,.15)', borderColor: '#94a3b8',
                    pointBackgroundColor: '#94a3b8', pointBorderColor: '#fff', borderWidth: 1.5, borderDash: [4, 4]
                }
            ]
        };

        if (radarChart) {
            radarChart.data = radarData; radarChart.update();
        } else {
            radarChart = new Chart(document.getElementById('comparisonRadarChart'), {
                type: 'radar',
                data: radarData,
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: { r: { suggestedMin: 0, suggestedMax: 100, ticks: { display: false }, pointLabels: { font: { size: 10, weight: '600' } } } },
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } }, datalabels: { display: false } }
                }
            });
        }
    }

    document.getElementById('radarCountrySelector').addEventListener('change', function () { updateCountryProfile(this.value); });
    updateCountryProfile('ID');

    // =========================================================
    // 7. Auto-Polling Logic (Permanently LIVE)
    // =========================================================
    const spinner = document.getElementById('refreshSpinner');
    const lastUpdated = document.getElementById('lastUpdatedVal');
    const refreshBar = document.getElementById('refreshBar');
    let barProgress = 0, barInterval = null;

    function startProgressBar() {
        barProgress = 0; refreshBar.style.width = '0%';
        clearInterval(barInterval);
        barInterval = setInterval(() => {
            barProgress = Math.min(barProgress + (100 / (REFRESH_MS / 400)), 95);
            refreshBar.style.width = barProgress + '%';
        }, 400);
    }

    function completeProgressBar() {
        clearInterval(barInterval);
        refreshBar.style.width = '100%';
        setTimeout(() => { refreshBar.style.width = '0%'; }, 300);
    }

    async function fetchLiveData() {
        spinner.classList.remove('d-none');
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
            const res = await fetch(LIVE_DATA_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken } });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();

            // Update KPI
            totalCountriesCount = data.kpi.totalCountries;
            document.getElementById('kpiTotalCountries').textContent = totalCountriesCount;
            document.getElementById('kpiAvgRisk').innerHTML = `${data.kpi.avgGlobalRisk} <span style="font-size:0.8rem;font-weight:600;opacity:0.6;">/100</span>`;
            document.getElementById('kpiHighRisk').innerHTML = `${data.kpi.highRiskCount} <span style="font-size:0.8rem;font-weight:600;opacity:0.6;">Negara</span>`;
            document.getElementById('kpiTotalPorts').textContent = data.kpi.totalPorts;

            // Update Donut
            chartRiskDist.data.datasets[0].data = [data.riskCounts.High, data.riskCounts.Medium, data.riskCounts.Low];
            chartRiskDist.update();

            // Update Bar Top Risk
            chartTopRisk.data.labels = data.topRisk.map(c => c.country_name);
            chartTopRisk.data.datasets[0].data = data.topRisk.map(c => c.risk_score);
            chartTopRisk.update();

            // Update Bar Top Ports
            chartTopPorts.data.labels = data.topPorts.map(c => c.country_name);
            chartTopPorts.data.datasets[0].data = data.topPorts.map(c => c.total);
            chartTopPorts.update();

            // Update Correlation (Bubble & Trendline)
            correlationData = data.correlationData;
            chartCorrelation.data.datasets[1].data = correlationData;
            chartCorrelation.data.datasets[0].data = calculateTrendLine(correlationData); // Recompute trendline
            chartCorrelation.update();

            // Update Radar
            globalAverages = data.globalAverages;
            countriesList = data.countries;
            updateCountryProfile(document.getElementById('radarCountrySelector').value);

            // Last Updated Time
            const dt = new Date(data.lastUpdated);
            lastUpdated.textContent = dt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            completeProgressBar();
        } catch (err) {
            console.error('[Visualisasi Live] Fetch error:', err);
            completeProgressBar();
        } finally {
            spinner.classList.add('d-none');
        }
    }

    // Start infinite polling
    fetchLiveData();
    startProgressBar();
    setInterval(() => {
        fetchLiveData();
        startProgressBar();
    }, REFRESH_MS);

});
</script>
@endpush