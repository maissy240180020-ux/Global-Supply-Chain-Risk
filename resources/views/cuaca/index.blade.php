@extends('layouts.app')

@section('title', 'Pantauan Cuaca Global')

@section('content')

<style>
    .hover-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-card:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.06) !important; }
    
    .loading-shimmer {
        animation: shimmer 2s infinite linear;
        background: linear-gradient(to right, #f6f7f8 4%, #edeef1 25%, #f6f7f8 36%);
        background-size: 1000px 100%;
    }
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-cloud-haze2-fill text-info"></i> Pantauan Cuaca Global
            </h2>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">Monitor kondisi cuaca ekstrem di pusat logistik utama secara real-time.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <label class="fw-semibold text-muted small mb-0 text-nowrap"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Lokasi:</label>
                <select id="countrySelect" class="form-select bg-white shadow-sm border-light fw-medium text-dark" style="width: auto; min-width: 220px; border-radius: 10px; height: 42px;" onchange="refreshWeather()">
                    <option value="world" {{ $countryCode == 'world' ? 'selected' : '' }}>🌍 Seluruh Dunia (Global)</option>
                    @foreach($allCountries as $c)
                        <option value="{{ $c->country_code }}" {{ $countryCode == $c->country_code ? 'selected' : '' }}>{{ $c->country_name }}</option>
                    @endforeach
                </select>
            </div>
            <button id="refreshBtn" class="btn btn-primary text-white fw-semibold shadow-sm px-4 d-flex align-items-center gap-2" style="border-radius: 10px; height: 42px;" onclick="refreshWeather()">
                <i class="bi bi-arrow-clockwise"></i> Segarkan Data
            </button>
        </div>
    </div>

    <!-- Error Banner -->
    <div id="errorBanner" class="alert alert-danger shadow-sm border-0 mb-4 {{ $error ? '' : 'd-none' }}" style="border-radius: 16px; background-color: #fef2f2; color: #991b1b;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
                <h6 class="fw-bold mb-0">Gangguan Koneksi Cuaca</h6>
                <p class="mb-0 small" id="errorMessage">{{ $error ?? 'Data cuaca tidak dapat dimuat. Silakan coba kembali.' }}</p>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="d-none justify-content-center align-items-center py-5 bg-white shadow-sm mb-4" style="border-radius: 16px;">
        <div class="d-flex flex-column align-items-center">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
            <span class="fw-bold text-muted">Menghubungkan ke satelit Open-Meteo...</span>
        </div>
    </div>

    <div id="weatherContent" class="{{ $error ? 'd-none' : '' }}">
        <!-- Stats Cards -->
        <div class="row g-4 mb-4" id="statsContainer">
            <!-- Rata-rata Suhu -->
            <div class="col-md-3">
                <div class="card shadow-sm border-0 hover-card h-100" style="border-radius: 16px; background-color: #fef3c7;">
                    <div class="card-body p-4 d-flex align-items-center gap-4">
                        <div class="bg-white text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                            <i class="bi bi-thermometer-sun fs-2"></i>
                        </div>
                        <div>
                            <p class="text-warning text-darken fw-bold mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Rata-rata Suhu</p>
                            <h3 class="fw-bolder mb-0 text-dark" id="statTemp">{{ $stats['avg_temp'] }}°C</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lokasi Cuaca Ekstrem -->
            <div class="col-md-3">
                <div class="card shadow-sm border-0 hover-card h-100" style="border-radius: 16px; background-color: #ffe4e6;">
                    <div class="card-body p-4 d-flex align-items-center gap-4">
                        <div class="bg-white text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                            <i class="bi bi-cloud-lightning-rain-fill fs-2"></i>
                        </div>
                        <div>
                            <p class="text-danger fw-bold mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Titik Risiko Tinggi</p>
                            <h3 class="fw-bolder mb-0 text-dark" id="statExtreme">{{ $stats['extreme_count'] }} <span class="fs-6 fw-normal text-muted">Lokasi</span></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kecepatan Angin Maksimal -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 hover-card h-100" style="border-radius: 16px; background-color: #e0f2fe;">
                    <div class="card-body p-4 d-flex align-items-center gap-4">
                        <div class="bg-white text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                            <i class="bi bi-wind fs-2"></i>
                        </div>
                        <div>
                            <p class="text-info text-darken fw-bold mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Angin Terkencang</p>
                            <h3 class="fw-bolder mb-0 text-dark">
                                <span id="statWind">{{ $stats['max_wind'] }}</span> <span class="fs-6 fw-normal text-muted">km/h di</span> 
                                <span id="statWindLoc" class="text-primary">{{ $stats['max_wind_loc'] }}</span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Map Section -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100 hover-card" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-map text-primary me-2"></i> Radar Cuaca Live</h6>
                    </div>
                    <div class="card-body p-0">
                        <div id="weatherMap" style="height: 480px; width: 100%; z-index: 1;"></div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100 hover-card" style="border-radius: 16px;">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-bar-chart-fill text-warning me-2"></i> Perbandingan Suhu (°C)</h6>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div id="tempChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let map = null;
    let markersLayer = null;
    let tempChart = null;
    
    // Inisialisasi Data dari PHP (Load Pertama)
    const initialData = @json($weatherData);

    document.addEventListener('DOMContentLoaded', function () {
        initMap();
        initChart();
        if (initialData && initialData.length > 0) {
            renderMapMarkers(initialData);
            renderChart(initialData);
        }
    });

    function initMap() {
        // Hancurkan map jika sudah ada untuk menghindari '_leaflet_events' error
        if (map !== null && map !== undefined) {
            map.remove();
        }
        
        map = L.map('weatherMap').setView([20, 0], 2);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; Carto'
        }).addTo(map);
        
        markersLayer = L.layerGroup().addTo(map);
    }

    function initChart() {
        const options = {
            series: [{ name: 'Suhu (°C)', data: [] }],
            chart: { type: 'bar', height: 400, toolbar: { show: false } },
            plotOptions: {
                bar: { borderRadius: 4, horizontal: true, distributed: true }
            },
            dataLabels: { enabled: true, formatter: function (val) { return val + "°C" } },
            xaxis: { categories: [] },
            legend: { show: false },
            colors: ['#0dcaf0', '#0d6efd', '#ffc107', '#dc3545', '#20c997', '#6f42c1', '#fd7e14', '#e83e8c', '#6610f2', '#198754']
        };
        tempChart = new ApexCharts(document.querySelector("#tempChart"), options);
        tempChart.render();
    }

    function renderMapMarkers(data) {
        markersLayer.clearLayers();
        let bounds = [];

        data.forEach(item => {
            // Tentukan warna marker berdasar risiko
            let markerColor = '#0dcaf0'; // Low / Default
            if (item.risk_level === 'Medium') markerColor = '#0d6efd';
            if (item.risk_level === 'High') markerColor = '#ffc107';
            if (item.risk_level === 'Extreme') markerColor = '#dc3545';

            const customIcon = L.divIcon({
                className: 'custom-weather-icon',
                html: `<div style="background-color:${markerColor}; width:32px; height:32px; border-radius:50%; border:3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center; color:white; font-size:16px; transition: transform 0.2s;"><i class="bi ${item.icon}"></i></div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16],
                popupAnchor: [0, -16]
            });

            const popupContent = `
                <div style="min-width: 220px; font-family: inherit;">
                    <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                        <i class="bi ${item.icon} fs-4 text-dark"></i>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">${item.country_name}</h6>
                            <span class="text-muted small">${item.condition}</span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small"><i class="bi bi-thermometer-half"></i> Suhu</span>
                        <strong class="text-dark">${item.temp}°C</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small"><i class="bi bi-wind"></i> Angin</span>
                        <strong class="text-dark">${item.wind} km/h</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small"><i class="bi bi-droplet"></i> Curah Hujan</span>
                        <strong class="text-dark">${item.rain} mm</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-muted small"><i class="bi bi-water"></i> Kelembapan</span>
                        <strong class="text-dark">${item.humidity}%</strong>
                    </div>
                    
                    <div class="mt-2 text-center">
                        <span class="badge bg-${item.risk_color} w-100 py-2 shadow-sm" style="border-radius:8px;">Risiko Cuaca: ${item.risk_level}</span>
                    </div>
                </div>
            `;

            L.marker([item.lat, item.lng], {icon: customIcon})
             .bindPopup(popupContent, {
                 closeButton: false,
                 className: 'modern-popup'
             })
             .addTo(markersLayer);
             
            bounds.push([item.lat, item.lng]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, {padding: [50, 50], maxZoom: 6});
        }
    }

    function renderChart(data) {
        // Ambil top 10 negara secara acak untuk grafik agar tidak terlalu padat
        const chartData = data.slice(0, 10);
        const categories = chartData.map(item => item.country_code);
        const seriesData = chartData.map(item => item.temp);
        
        tempChart.updateSeries([{ data: seriesData }]);
        tempChart.updateOptions({ xaxis: { categories: categories } });
    }

    function refreshWeather() {
        const country = document.getElementById('countrySelect').value;
        const btn = document.getElementById('refreshBtn');
        const content = document.getElementById('weatherContent');
        const loading = document.getElementById('loadingOverlay');
        const errorBanner = document.getElementById('errorBanner');

        // UI Loading State
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyinkronkan...';
        btn.disabled = true;
        content.classList.add('d-none');
        loading.classList.remove('d-none');
        loading.classList.add('d-flex');
        errorBanner.classList.add('d-none');

        // Update URL hash/query without reloading
        const url = new URL(window.location.href);
        url.searchParams.set('country', country);
        window.history.pushState({}, '', url);

        // Tambah timestamp agar browser tidak menggunakan cache
        url.searchParams.set('_t', new Date().getTime());

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => {
            if (!res.ok) throw new Error("HTTP " + res.status);
            return res.json();
        })
        .then(res => {
            if (res.error) {
                showError(res.error);
            } else {
                try {
                    // Update Stats
                    if (res.stats) {
                        document.getElementById('statTemp').innerText = (res.stats.avg_temp || 0) + '°C';
                        document.getElementById('statExtreme').innerText = res.stats.extreme_count || 0;
                        document.getElementById('statWind').innerText = res.stats.max_wind || 0;
                        document.getElementById('statWindLoc').innerText = res.stats.max_wind_loc || '-';
                    }
                    
                    // Re-init map fully just to be safe
                    initMap();
                    
                    // Update Map & Chart
                    if (res.data) {
                        renderMapMarkers(res.data);
                        renderChart(res.data);
                    }
                    
                    content.classList.remove('d-none');
                } catch(e) {
                    showError("Client Error", e);
                }
            }
        })
        .catch(err => {
            showError("Network Error", err);
        })
        .finally(() => {
            btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Segarkan Data';
            btn.disabled = false;
            loading.classList.remove('d-flex');
            loading.classList.add('d-none');
        });
    }

    function showError(logContext, errorObj = null) {
        // Log teknis untuk developer
        console.error("Cuaca API Error [" + logContext + "]:", errorObj);
        
        // Tampilkan pesan ramah pengguna
        document.getElementById('errorBanner').classList.remove('d-none');
        document.getElementById('errorMessage').textContent = 'Data cuaca gagal dimuat dari satelit saat ini. Silakan coba kembali beberapa saat lagi.';
    }
</script>

<style>
/* Leaflet Popup Modernization */
.modern-popup .leaflet-popup-content-wrapper {
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    padding: 4px;
}
.modern-popup .leaflet-popup-tip {
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}
.custom-weather-icon {
    background: transparent;
    border: none;
}
.custom-weather-icon div:hover {
    transform: scale(1.15);
}
</style>
@endpush