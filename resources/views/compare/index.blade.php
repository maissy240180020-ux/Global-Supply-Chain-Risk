@extends('layouts.app')

@section('title', 'Perbandingan Negara | SIMRPG')

@section('content')

<style>
    .hover-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important; }
    .risk-circle { transition: border-color 0.4s ease; }
    
    .loading-shimmer {
        animation: shimmer 1.5s infinite linear;
        background: linear-gradient(to right, #f6f7f8 4%, #edeef1 25%, #f6f7f8 36%);
        background-size: 1000px 100%;
    }
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
    
    /* Loading overlay for the whole comparison section */
    #compareOverlay {
        backdrop-filter: blur(4px);
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 50;
    }
</style>

<div class="container-fluid position-relative">

    <!-- Header Section -->
    <div class="mb-4">
        <h2 class="fw-bold mb-0">⚖️ Perbandingan Risiko Negara (VS Mode)</h2>
        <p class="text-muted mb-0">Analisis Komparatif Kerentanan Rantai Pasok Global Antara Dua Negara</p>
    </div>

    <!-- Country Selector Form -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4 bg-white" style="border-radius: 16px;">
            <form id="compareForm">
                @csrf
                <div class="row g-3 align-items-end">
                    
                    <!-- Country 1 -->
                    <div class="col-lg-4 col-md-5 col-12">
                        <label for="country1" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Negara Pertama</label>
                        <select id="country1" name="country1" class="form-select border-light shadow-none" 
                                style="font-weight: 500; font-size: 0.88rem; background-color: #f8fafc; height: 42px; border-radius: 10px;">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ $loop->index == 0 ? 'selected' : '' }}>
                                    {{ $country->country_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Country 2 -->
                    <div class="col-lg-4 col-md-5 col-12">
                        <label for="country2" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Negara Kedua</label>
                        <select id="country2" name="country2" class="form-select border-light shadow-none"
                                style="font-weight: 500; font-size: 0.88rem; background-color: #f8fafc; height: 42px; border-radius: 10px;">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ $loop->index == 1 ? 'selected' : '' }}>
                                    {{ $country->country_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-lg-4 col-md-2 col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" id="btnCompare" class="btn text-white flex-fill d-flex align-items-center justify-content-center gap-2" 
                                    style="background: linear-gradient(135deg, #475569 0%, #334155 100%); border: none; height: 42px; border-radius: 10px; font-weight: 600; font-size: 0.88rem; transition: all 0.2s;">
                                <i class="bi bi-bar-chart-fill"></i> <span id="btnText">Bandingkan</span>
                            </button>
                            <button type="button" id="swapBtn" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" 
                                    style="width: 42px; height: 42px; border-radius: 10px; border-color: #cbd5e1; color: #475569;">
                                <i class="bi bi-arrow-left-right"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Error Banner -->
    <div id="errorBanner" class="alert alert-danger shadow-sm border-0 mb-4 d-none" style="border-radius: 16px; background-color: #fef2f2; color: #991b1b;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
                <h6 class="fw-bold mb-0">Gangguan Pengambilan Data API</h6>
                <p class="mb-0 small" id="errorMessage">Sebagian data belum tersedia, silakan coba kembali.</p>
            </div>
        </div>
    </div>

    <!-- COMPARISON RESULTS (Initially Hidden) -->
    <div id="comparisonContainer" class="d-none position-relative">
        
        <!-- Full Overlay Loading for the container -->
        <div id="compareOverlay" class="position-absolute w-100 h-100 d-none flex-column align-items-center justify-content-center" style="border-radius: 16px;">
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
            <h5 class="fw-bold text-dark mb-1">Mengambil Data Multi-API...</h5>
            <p class="text-muted small">Menyinkronkan World Bank, Open-Meteo, & ExchangeRate</p>
        </div>

        <!-- VS Visual Section -->
        <div class="row g-4 mb-4 align-items-stretch">
            
            <!-- Country A Profile -->
            <div class="col-md-5 col-12">
                <div class="card border-0 shadow-sm h-100 p-4 text-center bg-white hover-card" style="border-radius: 16px;">
                    <div class="d-flex flex-column align-items-center">
                        <img id="flagA" src="" alt="Flag"
                             style="width: 100px; height: 65px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.06);">
                        <h4 id="nameA" class="fw-bold text-dark mb-1">...</h4>
                        <span id="codeA" class="badge px-2.5 py-1 mb-3" style="font-size: 0.72rem; background-color: #e2e8f0; color: #475569;">...</span>

                        <!-- Risk Dial -->
                        <div class="my-3 position-relative d-flex align-items-center justify-content-center rounded-circle"
                             style="width: 110px; height: 110px; border: 8px solid #f1f5f9; background-color: #fff; box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);">
                            <div id="riskCircleA" class="position-absolute w-100 h-100 rounded-circle risk-circle" style="border: 8px solid #cbd5e1; margin: -8px; opacity: 0.15;"></div>
                            <div class="text-center">
                                <span id="riskScoreA" class="fw-bold text-dark" style="font-size: 1.8rem; line-height: 1;">0</span>
                                <small class="text-muted d-block" style="font-size: 0.65rem; font-weight:600; text-transform: uppercase;">RISK</small>
                            </div>
                        </div>

                        <!-- Mini Stats -->
                        <div class="w-100 row g-2 mt-3 text-center border-top border-light pt-3">
                            <div class="col-4 border-end border-light">
                                <small class="text-muted d-block" style="font-size: 0.65rem;"><i class="bi bi-building"></i> Ibu Kota</small>
                                <span id="capitalA" class="fw-semibold text-dark" style="font-size: 0.78rem;">...</span>
                            </div>
                            <div class="col-4 border-end border-light">
                                <small class="text-muted d-block" style="font-size: 0.65rem;"><i class="bi bi-cash-coin"></i> Mata Uang</small>
                                <span id="currencyA" class="fw-semibold text-dark" style="font-size: 0.78rem;">...</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block" style="font-size: 0.65rem;"><i class="bi bi-geo-alt"></i> Pelabuhan</small>
                                <span id="portA" class="fw-bold text-dark" style="font-size: 0.78rem;">...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VS Circle Badge -->
            <div class="col-md-2 col-12 d-flex align-items-center justify-content-center my-md-0 my-3">
                <div class="rounded-circle shadow d-flex align-items-center justify-content-center text-white fw-bold hover-card"
                     style="width: 64px; height: 64px; background: linear-gradient(135deg, #475569, #1e293b); font-size: 1.4rem; border: 4px solid #fff;">
                    VS
                </div>
            </div>

            <!-- Country B Profile -->
            <div class="col-md-5 col-12">
                <div class="card border-0 shadow-sm h-100 p-4 text-center bg-white hover-card" style="border-radius: 16px;">
                    <div class="d-flex flex-column align-items-center">
                        <img id="flagB" src="" alt="Flag"
                             style="width: 100px; height: 65px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.06);">
                        <h4 id="nameB" class="fw-bold text-dark mb-1">...</h4>
                        <span id="codeB" class="badge px-2.5 py-1 mb-3" style="font-size: 0.72rem; background-color: #e2e8f0; color: #475569;">...</span>

                        <!-- Risk Dial -->
                        <div class="my-3 position-relative d-flex align-items-center justify-content-center rounded-circle"
                             style="width: 110px; height: 110px; border: 8px solid #f1f5f9; background-color: #fff; box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);">
                            <div id="riskCircleB" class="position-absolute w-100 h-100 rounded-circle risk-circle" style="border: 8px solid #cbd5e1; margin: -8px; opacity: 0.15;"></div>
                            <div class="text-center">
                                <span id="riskScoreB" class="fw-bold text-dark" style="font-size: 1.8rem; line-height: 1;">0</span>
                                <small class="text-muted d-block" style="font-size: 0.65rem; font-weight:600; text-transform: uppercase;">RISK</small>
                            </div>
                        </div>

                        <!-- Mini Stats -->
                        <div class="w-100 row g-2 mt-3 text-center border-top border-light pt-3">
                            <div class="col-4 border-end border-light">
                                <small class="text-muted d-block" style="font-size: 0.65rem;"><i class="bi bi-building"></i> Ibu Kota</small>
                                <span id="capitalB" class="fw-semibold text-dark" style="font-size: 0.78rem;">...</span>
                            </div>
                            <div class="col-4 border-end border-light">
                                <small class="text-muted d-block" style="font-size: 0.65rem;"><i class="bi bi-cash-coin"></i> Mata Uang</small>
                                <span id="currencyB" class="fw-semibold text-dark" style="font-size: 0.78rem;">...</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block" style="font-size: 0.65rem;"><i class="bi bi-geo-alt"></i> Pelabuhan</small>
                                <span id="portB" class="fw-bold text-dark" style="font-size: 0.78rem;">...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Comparative Metrics Table & Chart -->
        <div class="row g-4 mb-4">
            
            <!-- Table Card -->
            <div class="col-xl-7 col-12">
                <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-header bg-white py-3 border-light">
                        <h6 class="fw-bold mb-0 text-dark">📋 Perbandingan Parameter Metrik</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="table-light text-secondary">
                                    <tr>
                                        <th class="ps-4" width="30%">Parameter</th>
                                        <th width="35%" id="thNameA">Negara A</th>
                                        <th width="35%" id="thNameB">Negara B</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">Skor Risiko</td>
                                        <td id="tdRiskA" class="fw-bold">...</td>
                                        <td id="tdRiskB" class="fw-bold">...</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">Level Risiko</td>
                                        <td id="tdLevelA">...</td>
                                        <td id="tdLevelB">...</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">Temperatur</td>
                                        <td id="tdTempA" class="fw-bold">...</td>
                                        <td id="tdTempB" class="fw-bold">...</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">Kondisi Cuaca</td>
                                        <td id="tdWeatherA" class="text-secondary fw-semibold">...</td>
                                        <td id="tdWeatherB" class="text-secondary fw-semibold">...</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">Total Pelabuhan</td>
                                        <td id="tdPortA" class="fw-bold">...</td>
                                        <td id="tdPortB" class="fw-bold">...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grouped Bar Chart -->
            <div class="col-xl-5 col-12">
                <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 16px;">
                    <div class="card-header bg-white py-3 border-light">
                        <h6 class="fw-bold mb-0 text-dark">📊 Grafik Komparatif Side-by-Side</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                        <div style="width: 100%; max-height: 260px;">
                            <canvas id="groupedCompareChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Supply Chain Recommendation Box -->
        <div class="card border-0 shadow-sm text-white mb-4 hover-card" style="border-radius: 16px; background-color: #0f172a;">
            <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: rgba(255,255,255,0.08) !important;">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    🤖 AI Supply Chain Intelligence Insight
                </h6>
                <span class="badge" style="background-color: rgba(16, 185, 129, 0.2); color: #10b981; font-size: 0.65rem;">
                    <i class="bi bi-cpu-fill"></i> Analisis berdasarkan data real-time Multi-API
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-lg-6 col-12">
                        <h6 class="text-secondary fw-semibold text-uppercase mb-2" style="font-size: 0.68rem; letter-spacing: 0.05em; color: #94a3b8 !important;">Sintesis Analisis</h6>
                        <p id="aiParagraph1" class="mb-3" style="font-size: 0.85rem; line-height: 1.6; color: #cbd5e1;">...</p>
                        <div class="d-flex flex-wrap gap-2 text-center" style="font-size: 0.72rem;">
                            <span class="badge px-3 py-1.5" style="background-color: rgba(255,255,255,0.08); border-radius: 6px;">📈 Selisih Risiko: <span id="aiDiffRisk">0</span>%</span>
                            <span class="badge px-3 py-1.5" style="background-color: rgba(255,255,255,0.08); border-radius: 6px;">🌡 Selisih Suhu: <span id="aiDiffTemp">0</span> °C</span>
                            <span class="badge px-3 py-1.5" style="background-color: rgba(255,255,255,0.08); border-radius: 6px;">⚓ Selisih Pelabuhan: <span id="aiDiffPort">0</span></span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12 border-start-lg border-light" style="border-color: rgba(255,255,255,0.08) !important;">
                        <h6 class="text-secondary fw-semibold text-uppercase mb-2" style="font-size: 0.68rem; letter-spacing: 0.05em; color: #10b981 !important;">Rekomendasi Rantai Pasok</h6>
                        <div class="p-3 mb-0" style="border-radius: 12px; background-color: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.15);">
                            <p id="aiParagraph2" class="mb-0 text-white" style="font-size: 0.85rem; line-height: 1.6;">...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /#comparisonContainer -->
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // UI Elements
    const form = document.getElementById('compareForm');
    const container = document.getElementById('comparisonContainer');
    const overlay = document.getElementById('compareOverlay');
    const errorBanner = document.getElementById('errorBanner');
    const btnCompare = document.getElementById('btnCompare');
    const btnText = document.getElementById('btnText');
    const swapBtn = document.getElementById('swapBtn');

    let compareChart = null;

    // Swap Logic
    swapBtn.addEventListener('click', function(){
        let a = document.getElementById('country1');
        let b = document.getElementById('country2');
        let temp = a.value;
        a.value = b.value;
        b.value = temp;
    });

    // Form Submit (AJAX interception)
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const c1 = document.getElementById('country1').value;
        const c2 = document.getElementById('country2').value;

        // UI Loading State
        errorBanner.classList.add('d-none');
        container.classList.remove('d-none');
        overlay.classList.remove('d-none');
        overlay.classList.add('d-flex');
        
        btnCompare.disabled = true;
        btnText.textContent = "Menyinkronkan API...";

        try {
            const res = await fetch("{{ route('compare.compare') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ country1: c1, country2: c2 })
            });

            if (!res.ok) throw new Error("Gagal mengambil data dari server");
            
            const data = await res.json();
            if (data.error) throw new Error(data.error);

            renderComparison(data.countryA, data.countryB, data.insight);

        } catch (err) {
            console.error("Comparison Error:", err);
            errorBanner.classList.remove('d-none');
            container.classList.add('d-none');
        } finally {
            overlay.classList.remove('d-flex');
            overlay.classList.add('d-none');
            btnCompare.disabled = false;
            btnText.textContent = "Bandingkan";
        }
    });

    function getRiskColor(level) {
        if (level === 'High') return '#ef4444'; // Red Pastel
        if (level === 'Medium') return '#f59e0b'; // Yellow Pastel
        return '#10b981'; // Green Pastel
    }

    function renderComparison(a, b, insight) {
        // --- Country A ---
        document.getElementById('flagA').src = a.flag || 'https://flagcdn.com/w320/un.png';
        document.getElementById('nameA').textContent = a.name;
        document.getElementById('codeA').textContent = a.code;
        document.getElementById('capitalA').textContent = a.capital;
        document.getElementById('currencyA').textContent = a.currency;
        document.getElementById('portA').textContent = a.port_count;
        document.getElementById('riskScoreA').textContent = a.risk_score;
        document.getElementById('riskCircleA').style.borderColor = getRiskColor(a.risk_level);

        // --- Country B ---
        document.getElementById('flagB').src = b.flag || 'https://flagcdn.com/w320/un.png';
        document.getElementById('nameB').textContent = b.name;
        document.getElementById('codeB').textContent = b.code;
        document.getElementById('capitalB').textContent = b.capital;
        document.getElementById('currencyB').textContent = b.currency;
        document.getElementById('portB').textContent = b.port_count;
        document.getElementById('riskScoreB').textContent = b.risk_score;
        document.getElementById('riskCircleB').style.borderColor = getRiskColor(b.risk_level);

        // --- Table Rendering (with comparison colors) ---
        document.getElementById('thNameA').textContent = a.name;
        document.getElementById('thNameB').textContent = b.name;

        // Risk Score
        const rA = document.getElementById('tdRiskA');
        const rB = document.getElementById('tdRiskB');
        rA.innerHTML = `${a.risk_score} <small class="text-muted fw-normal">/100</small>`;
        rB.innerHTML = `${b.risk_score} <small class="text-muted fw-normal">/100</small>`;
        rA.style.color = a.risk_score < b.risk_score ? '#10b981' : (a.risk_score > b.risk_score ? '#ef4444' : '#475569');
        rB.style.color = b.risk_score < a.risk_score ? '#10b981' : (b.risk_score > a.risk_score ? '#ef4444' : '#475569');

        // Risk Level Badge
        const lvlA = document.getElementById('tdLevelA');
        const lvlB = document.getElementById('tdLevelB');
        lvlA.innerHTML = `<span class="badge text-white px-2.5 py-1" style="background-color: ${getRiskColor(a.risk_level)};">${a.risk_level}</span>`;
        lvlB.innerHTML = `<span class="badge text-white px-2.5 py-1" style="background-color: ${getRiskColor(b.risk_level)};">${b.risk_level}</span>`;

        // Temperature (closer to 22 is better, but let's just color blue/orange)
        const tA = document.getElementById('tdTempA');
        const tB = document.getElementById('tdTempB');
        tA.textContent = a.temperature + ' °C';
        tB.textContent = b.temperature + ' °C';
        tA.style.color = a.temperature > 35 ? '#ef4444' : '#0ea5e9';
        tB.style.color = b.temperature > 35 ? '#ef4444' : '#0ea5e9';

        // Weather
        document.getElementById('tdWeatherA').textContent = a.weather;
        document.getElementById('tdWeatherB').textContent = b.weather;

        // Ports (higher is better)
        const pA = document.getElementById('tdPortA');
        const pB = document.getElementById('tdPortB');
        pA.textContent = a.port_count;
        pB.textContent = b.port_count;
        pA.style.color = a.port_count > b.port_count ? '#10b981' : '#475569';
        pB.style.color = b.port_count > a.port_count ? '#10b981' : '#475569';

        // --- AI Insight Rendering ---
        document.getElementById('aiParagraph1').innerHTML = insight.paragraph1;
        document.getElementById('aiParagraph2').innerHTML = insight.paragraph2;
        document.getElementById('aiDiffRisk').textContent = insight.riskDiff;
        document.getElementById('aiDiffTemp').textContent = insight.tempDiff;
        document.getElementById('aiDiffPort').textContent = insight.portDiff;

        // --- Chart.js Rendering ---
        renderChart(a, b);
    }

    function renderChart(a, b) {
        if (compareChart !== null) {
            compareChart.destroy();
        }

        const ctx = document.getElementById('groupedCompareChart').getContext('2d');
        compareChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Skor Risiko', 'Temperatur (°C)', 'Pelabuhan'],
                datasets: [
                    {
                        label: a.name,
                        data: [a.risk_score, a.temperature, a.port_count],
                        backgroundColor: 'rgba(71, 85, 105, 0.8)', // Slate-Grey A
                        borderColor: '#475569',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: b.name,
                        data: [b.risk_score, b.temperature, b.port_count],
                        backgroundColor: 'rgba(56, 189, 248, 0.85)', // Blue Pastel B
                        borderColor: '#0284c7',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 11 } } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Poppins', size: 11 } } }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) { label += context.parsed.y; }
                                return label;
                            }
                        }
                    },
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { family: 'Poppins', size: 11 } } }
                }
            }
        });
    }

    // Automatically trigger comparison on first load if we want to show something initially, 
    // but the instruction implies it should happen when "Bandingkan" is pressed.
});
</script>
@endpush