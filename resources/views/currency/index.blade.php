@extends('layouts.app')

@section('title', 'Dashboard Nilai Tukar Realtime')

@section('content')

<!-- Tambahkan ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
    .loading-shimmer {
        animation: shimmer 1.5s infinite linear;
        background: linear-gradient(to right, #f6f7f8 4%, #edeef1 25%, #f6f7f8 36%);
        background-size: 1000px 100%;
    }
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
    .badge-live {
        animation: pulseLive 2s infinite;
    }
    @keyframes pulseLive {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
</style>

<div class="container-fluid py-3">

    <!-- Header & Base Dropdown -->
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0">💱 Live Currency Tracker</h2>
            <p class="text-muted mb-0 d-flex align-items-center gap-2">
                Pemantauan Nilai Tukar Real-Time & Konversi
                <span class="badge bg-danger badge-live shadow-sm rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                    <i class="bi bi-record-circle-fill"></i> Live from ExchangeRate API
                </span>
            </p>
        </div>
        
        <!-- Pemilih Base Currency -->
        <div class="bg-white p-2 rounded shadow-sm border border-light d-flex align-items-center gap-2">
            <label for="baseSelect" class="fw-semibold text-secondary mb-0">Base Currency:</label>
            <select id="baseSelect" class="form-select form-select-sm border-0 bg-light" style="font-weight: 500; cursor: pointer;">
                @foreach($supported as $code)
                    <option value="{{ $code }}" {{ $base == $code ? 'selected' : '' }}>
                        {{ $currencyMeta[$code]['flag'] ?? '🌍' }} {{ $code }} - {{ $currencyMeta[$code]['name'] ?? 'Mata Uang ' . $code }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Error Banner -->
    <div id="errorBanner" class="alert alert-danger shadow-sm border-0 mb-4 {{ isset($error) && $error ? '' : 'd-none' }}" style="border-radius: 16px; background-color: #fef2f2; color: #991b1b;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
                <h6 class="fw-bold mb-0">Gangguan Koneksi API</h6>
                <p class="mb-0 small" id="errorMessage">{{ $error ?? 'Data nilai tukar sedang tidak tersedia, silakan coba kembali.' }}</p>
            </div>
        </div>
    </div>

    <div class="row g-4" id="mainContent" class="{{ isset($error) && $error ? 'd-none' : '' }}">
        <!-- Live Chart Section -->
        <div class="col-lg-8 col-12">
            <div class="card border-0 shadow-sm h-100 position-relative" style="border-radius: 16px; overflow: hidden;">
                <!-- Loading Overlay Chart -->
                <div id="chartLoadingOverlay" class="d-none position-absolute top-0 start-0 w-100 h-100 bg-white justify-content-center align-items-center" style="z-index: 10; opacity: 0.85;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>

                <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        @php
                            $targetCurr = $base === 'USD' ? 'IDR' : 'USD';
                            $startRate = isset($kurs['rates'][$targetCurr]) ? $kurs['rates'][$targetCurr] : 1.0;
                            $decimals = $startRate < 1.0 ? 4 : 2;
                        @endphp
                        <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                            <span id="chartBaseLabel">{{ $base }}</span> / <span id="chartTargetLabel" class="text-primary">{{ $targetCurr }}</span>
                        </h4>
                        <h2 id="liveRateValue" class="fw-bold text-success mb-0" style="font-size: 2.2rem; letter-spacing: -1px;">
                            {{ isset($kurs) ? number_format($startRate, $decimals) : '...' }}
                        </h2>
                    </div>
                    <div class="d-flex flex-column align-items-end">
                        <label class="text-muted fw-semibold mb-1" style="font-size: 0.75rem;">Pilih Mata Uang Target:</label>
                        <select id="chartTargetSelect" class="form-select form-select-sm fw-bold border-1" style="width: 140px; border-radius: 8px;">
                            @foreach($supported as $cCode)
                                <option value="{{ $cCode }}" {{ $cCode === $targetCurr ? 'selected' : '' }}>
                                    {{ $cCode }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body px-2 pb-0">
                    <div id="realtimeChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Calculator Section -->
        <div class="col-lg-4 col-12">
            <div class="card border-0 shadow-sm h-100 position-relative" style="border-radius: 16px;">
                <!-- Loading Overlay Calc -->
                <div id="calcLoadingOverlay" class="d-none position-absolute top-0 start-0 w-100 h-100 bg-white justify-content-center align-items-center rounded-4" style="z-index: 10; opacity: 0.85;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>

                <div class="card-header bg-white py-3 border-light">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-calculator text-primary"></i> Kalkulator Konversi
                    </h5>
                </div>
                
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <!-- Calculator Form -->
                    <div class="mb-4">
                        <label for="calcAmount" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Jumlah Uang</label>
                        <div class="input-group input-group-lg shadow-sm" style="border-radius: 12px; overflow: hidden;">
                            <span class="input-group-text bg-light border-0 fw-bold text-muted" id="amountAddon">{{ $base }}</span>
                            <input type="number" id="calcAmount" class="form-control border-0 bg-light" value="1000" min="1" step="any" style="font-weight: 600;">
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-4 align-items-end">
                        <div class="col-5">
                            <label for="calcFrom" class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.8rem;">Dari</label>
                            <select id="calcFrom" class="form-select border-light shadow-sm fw-semibold" style="border-radius: 10px;">
                                @foreach($supported as $code)
                                    <option value="{{ $code }}" {{ $code == $base ? 'selected' : '' }}>{{ $code }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-2 text-center pb-1">
                            <button id="swapBtn" class="btn btn-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto" style="width: 36px; height: 36px; transition: transform 0.2s;">
                                <i class="bi bi-arrow-left-right text-white"></i>
                            </button>
                        </div>
                        
                        <div class="col-5">
                            <label for="calcTo" class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.8rem;">Ke</label>
                            <select id="calcTo" class="form-select border-light shadow-sm fw-semibold" style="border-radius: 10px;">
                                @foreach($supported as $code)
                                    @php $isDefault = ($base == 'USD' && $code == 'IDR') || ($base != 'USD' && $code == 'USD'); @endphp
                                    <option value="{{ $code }}" {{ $isDefault ? 'selected' : '' }}>{{ $code }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Result Box -->
                    <div class="p-4 bg-gradient rounded-4 text-center border-0 shadow-sm mt-auto" style="background: linear-gradient(145deg, #f8f9fa, #e9ecef);">
                        <span class="text-muted d-block mb-1 fw-bold tracking-wider" style="font-size: 0.75rem; letter-spacing: 1px;">HASIL KONVERSI</span>
                        <h2 class="fw-bolder mb-2 text-dark" id="calcResult" style="font-size: clamp(1.2rem, 4vw, 1.8rem); letter-spacing: -0.5px; word-break: break-word;">
                            ...
                        </h2>
                        <span class="badge bg-white text-secondary border fw-semibold px-3 py-2 shadow-sm rounded-pill" id="calcFormula" style="font-size: 0.8rem;">
                            -
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let globalRates = @json($kurs['rates'] ?? []);
    let currentGlobalBase = "{{ $base }}";
    
    // Elements
    const baseSelect = document.getElementById('baseSelect');
    const chartTargetSelect = document.getElementById('chartTargetSelect');
    const chartBaseLabel = document.getElementById('chartBaseLabel');
    const chartTargetLabel = document.getElementById('chartTargetLabel');
    const liveRateValue = document.getElementById('liveRateValue');
    
    const calcAmount = document.getElementById('calcAmount');
    const calcFrom = document.getElementById('calcFrom');
    const calcTo = document.getElementById('calcTo');
    const calcResult = document.getElementById('calcResult');
    const calcFormula = document.getElementById('calcFormula');
    const amountAddon = document.getElementById('amountAddon');
    const swapBtn = document.getElementById('swapBtn');
    
    const chartLoading = document.getElementById('chartLoadingOverlay');
    const calcLoading = document.getElementById('calcLoadingOverlay');
    const errorBanner = document.getElementById('errorBanner');
    const errorMessage = document.getElementById('errorMessage');

    // Chart Initialization
    let chart;
    let chartData = [];
    
    function initChart() {
        const options = {
            series: [{ name: `Nilai Tukar`, data: [] }],
            chart: {
                type: 'area',
                height: 380,
                animations: { enabled: true, easing: 'linear', dynamicAnimation: { speed: 1500 } },
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 100] } },
            colors: ['#10b981'],
            xaxis: {
                type: 'datetime',
                labels: { style: { colors: '#94a3b8' }, datetimeFormatter: { hour: 'HH:mm:ss', minute: 'HH:mm:ss', second: 'HH:mm:ss' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8', fontWeight: 600 },
                    formatter: function (value) { return value.toFixed(value < 1.0 ? 5 : 2); }
                }
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4, yaxis: { lines: { show: true } }, xaxis: { lines: { show: false } } },
            tooltip: { x: { format: 'HH:mm:ss' } }
        };
        chart = new ApexCharts(document.querySelector("#realtimeChart"), options);
        chart.render();
    }
    initChart();

    function populateChartMockData(baseRate) {
        chartData = [];
        let time = new Date().getTime() - 60000;
        let lastRate = baseRate;
        for(let i = 0; i < 40; i++) {
            let change = (Math.random() - 0.5) * (baseRate * 0.0002);
            lastRate = lastRate + change;
            chartData.push([time, lastRate]);
            time += 1500;
        }
        chart.updateSeries([{ data: chartData }]);
    }
    
    // Format helpers
    function formatCurrency(val, currency) {
        let decimals = val < 1.0 && val > 0 ? 4 : 2;
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }).format(val) + ' ' + currency;
    }
    function formatRate(val) {
        let decimals = val < 1.0 ? 4 : 2;
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }).format(val);
    }

    // --- API Fetch Function ---
    async function fetchExchangeRates(base, showChartLoad = false, showCalcLoad = false) {
        if (showChartLoad) chartLoading.classList.remove('d-none');
        if (showCalcLoad) calcLoading.classList.remove('d-none');
        chartLoading.classList.add('d-flex');
        calcLoading.classList.add('d-flex');
        errorBanner.classList.add('d-none');

        try {
            const url = new URL(window.location.origin + '/currency');
            url.searchParams.set('base', base);
            url.searchParams.set('_t', new Date().getTime());

            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
            if (!res.ok) throw new Error("HTTP " + res.status);
            
            const data = await res.json();
            if (!data.success) throw new Error(data.error);

            // Perbarui state secara global untuk keperluan chart jika base sama dengan globalBase
            if (base === currentGlobalBase) {
                globalRates = data.kurs.rates;
                globalRates[currentGlobalBase] = 1.0;
            }
            
            return data.kurs.rates;
        } catch (err) {
            console.error("ExchangeRate API Error:", err);
            errorBanner.classList.remove('d-none');
            errorMessage.textContent = 'Data nilai tukar sedang tidak tersedia, silakan coba kembali.';
            calcResult.textContent = "Error";
            calcFormula.innerHTML = "-";
            liveRateValue.textContent = "Error";
            return null;
        } finally {
            if (showChartLoad) chartLoading.classList.add('d-none');
            if (showCalcLoad) calcLoading.classList.add('d-none');
            chartLoading.classList.remove('d-flex');
            calcLoading.classList.remove('d-flex');
        }
    }

    // --- CHART UPDATE LOGIC ---
    async function updateChartSection() {
        const target = chartTargetSelect.value;
        const rate = globalRates[target] || 1.0;
        
        chartBaseLabel.textContent = currentGlobalBase;
        chartTargetLabel.textContent = target;
        liveRateValue.textContent = formatRate(rate);
        
        populateChartMockData(rate);
    }

    // --- CALCULATOR UPDATE LOGIC ---
    async function updateCalculatorSection() {
        const amount = parseFloat(calcAmount.value) || 0;
        const from = calcFrom.value;
        const to = calcTo.value;
        amountAddon.textContent = from;

        // Ambil rate secara live setiap kali (memenuhi request "selalu mengambil kurs terbaru dari API")
        const rates = await fetchExchangeRates(from, false, true);
        if (!rates) return;
        rates[from] = 1.0;

        const conversionRate = rates[to] || 1.0;
        const result = amount * conversionRate;

        calcResult.textContent = formatCurrency(result, to);
        calcFormula.innerHTML = `1 ${from} = <strong>${formatRate(conversionRate)} ${to}</strong>`;
    }

    // --- EVENT LISTENERS ---

    // 1. Global Base Currency Changed
    baseSelect.addEventListener('change', async function(e) {
        currentGlobalBase = e.target.value;
        calcFrom.value = currentGlobalBase;
        
        // Panggil API untuk base baru, tampilkan loading di kedua sisi
        await fetchExchangeRates(currentGlobalBase, true, true);
        updateChartSection();
        updateCalculatorSection();
    });

    // 2. Chart Target Changed
    chartTargetSelect.addEventListener('change', async function() {
        // Hanya perlu update visual karena globalRates dari global base sudah ada, 
        // namun untuk menjamin Real-Time (sesuai instruksi), kita panggil API lagi.
        await fetchExchangeRates(currentGlobalBase, true, false);
        updateChartSection();
    });

    // 3. Calc Selectors Changed
    calcFrom.addEventListener('change', updateCalculatorSection);
    calcTo.addEventListener('change', updateCalculatorSection);
    swapBtn.addEventListener('click', function() {
        const temp = calcFrom.value;
        calcFrom.value = calcTo.value;
        calcTo.value = temp;
        swapBtn.style.transform = swapBtn.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)';
        updateCalculatorSection();
    });

    // 4. Calc Amount Changed (Debounced)
    let debounceTimer;
    calcAmount.addEventListener('input', function() {
        // Tampilkan loading state visual segera (UX trick)
        calcResult.textContent = "...";
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            updateCalculatorSection();
        }, 500); // Debounce 500ms
    });

    // Initial render
    if(Object.keys(globalRates).length > 0) {
        updateChartSection();
        updateCalculatorSection();
    }
});
</script>
@endpush