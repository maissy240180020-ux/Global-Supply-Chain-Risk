@extends('layouts.app')

@section('title', 'Dashboard Visualisasi Data Global')

@section('content')
<div class="container-fluid">

    <!-- Header Section -->
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0">📊 Global Supply Chain Risk Visualization</h2>
            <p class="text-muted mb-0">Pusat Analisis Statistik & Pemantauan Risiko Rantai Pasok Terintegrasi</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius: 10px; font-weight: 500; font-size: 0.88rem;">
                <i class="bi bi-printer"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- KPI Summary Cards Grid -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Negara -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                          style="width: 50px; height: 50px; background-color: rgba(2, 132, 199, 0.1); color: #0284c7;">
                        <i class="bi bi-globe fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">Negara Dipantau</small>
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $totalCountries }}</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Negara aktif dalam sistem</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Rata-Rata Risiko -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                          style="width: 50px; height: 50px; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">Rata-rata Risiko</small>
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $avgGlobalRisk }} <span style="font-size:0.85rem; font-weight:500;" class="text-muted">/100</span></h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Indeks kerentanan global</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Risiko Tinggi -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                          style="width: 50px; height: 50px; background-color: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">Risiko Tinggi</small>
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $highRiskCount }} <span style="font-size:0.85rem; font-weight:500;" class="text-muted">Negara</span></h3>
                        <small class="text-danger" style="font-size: 0.75rem; font-weight: 500;">Butuh pengawasan ketat</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Pelabuhan -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                          style="width: 50px; height: 50px; background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="bi bi-anchor fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">Pelabuhan Global</small>
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $totalPorts }}</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Infrastruktur terintegrasi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Grid Row (2x2) -->
    <div class="row g-4 mb-4">
        <!-- Chart 1: Distribusi Tingkat Risiko -->
        <div class="col-xl-4 col-lg-5 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-light">
                    <h6 class="fw-bold mb-0 text-dark">🔔 Distribusi Tingkat Risiko Negara</h6>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="min-height: 320px;">
                    <div style="width: 100%; max-width: 260px;">
                        <canvas id="riskDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Top 10 Highest Risk Countries -->
        <div class="col-xl-8 col-lg-7 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-light">
                    <h6 class="fw-bold mb-0 text-dark">🔥 Top 10 Negara Kerentanan Rantai Pasok Tertinggi</h6>
                </div>
                <div class="card-body" style="min-height: 320px;">
                    <canvas id="topRiskChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 3: Top Countries by Ports Count -->
        <div class="col-xl-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-light">
                    <h6 class="fw-bold mb-0 text-dark">🚢 10 Negara Infrastruktur Pelabuhan Terbanyak</h6>
                </div>
                <div class="card-body" style="min-height: 320px;">
                    <canvas id="topPortsChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 4: Correlation Bubble Chart -->
        <div class="col-xl-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-light">
                    <h6 class="fw-bold mb-0 text-dark">📈 Analisis Korelasi: Skor Risiko vs Tingkat Inflasi</h6>
                </div>
                <div class="card-body" style="min-height: 320px;">
                    <canvas id="correlationChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Country Profile Tool & Radar Chart Comparison -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-sliders text-primary"></i> Alat Profiling & Perbandingan Radar Negara
            </h5>
            <div class="d-flex align-items-center gap-2">
                <label for="radarCountrySelector" class="fw-semibold text-secondary mb-0" style="font-size: 0.85rem;">Pilih Negara:</label>
                <select id="radarCountrySelector" class="form-select form-select-sm border-light" style="width: 220px; font-weight: 500; border-radius: 8px;">
                    @foreach($countries as $c)
                        <option value="{{ $c->country_code }}" {{ $c->country_code === 'ID' ? 'selected' : '' }}>
                            {{ $c->country_name }} ({{ $c->country_code }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-body bg-light p-4">
            <div class="row g-4 align-items-center">
                <!-- Country Profile Detail Card -->
                <div class="col-xl-4 col-lg-5 col-12">
                    <div class="card border-0 shadow bg-white h-100" style="border-radius: 16px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <img id="profileFlag" src="" alt="Flag" style="width: 60px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                                <div>
                                    <h4 class="fw-bold text-dark mb-0" id="profileCountryName">Negara</h4>
                                    <span class="badge bg-primary px-2.5 py-1" style="font-size:0.7rem;" id="profileCountryCode">CODE</span>
                                </div>
                            </div>
                            <div class="vstack gap-3" style="font-size: 0.85rem;">
                                <div class="d-flex justify-content-between border-bottom border-light pb-2">
                                    <span class="text-muted"><i class="bi bi-bank me-2"></i>Ibu Kota</span>
                                    <strong class="text-dark" id="profileCapital">--</strong>
                                </div>
                                <div class="d-flex justify-content-between border-bottom border-light pb-2">
                                    <span class="text-muted"><i class="bi bi-cash-stack me-2"></i>Mata Uang</span>
                                    <strong class="text-dark" id="profileCurrency">--</strong>
                                </div>
                                <div class="d-flex justify-content-between border-bottom border-light pb-2">
                                    <span class="text-muted"><i class="bi bi-people me-2"></i>Populasi</span>
                                    <strong class="text-dark" id="profilePopulation">--</strong>
                                </div>
                                <div class="d-flex justify-content-between border-bottom border-light pb-2">
                                    <span class="text-muted"><i class="bi bi-thermometer-half me-2"></i>Suhu / Cuaca</span>
                                    <span class="badge bg-light text-dark fw-bold" id="profileWeather">--</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom border-light pb-2">
                                    <span class="text-muted"><i class="bi bi-anchor me-2"></i>Total Pelabuhan</span>
                                    <strong class="text-dark" id="profilePorts">--</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"><i class="bi bi-shield-exclamation me-2"></i>Skor Risiko</span>
                                    <span class="badge" id="profileRiskBadge" style="font-size:0.8rem; font-weight:600; padding: 4px 10px;">--</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Radar Chart Comparison -->
                <div class="col-xl-8 col-lg-7 col-12">
                    <div class="card border-0 shadow bg-white" style="border-radius: 16px;">
                        <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 380px;">
                            <h6 class="fw-bold text-dark text-start w-100 mb-4">🕷️ Metrik Relatif terhadap Rata-Rata Global (Skala 0-100)</h6>
                            <div style="width: 100%; max-width: 420px;">
                                <canvas id="comparisonRadarChart"></canvas>
                            </div>
                            <div class="mt-3 text-muted text-center" style="font-size: 0.72rem; font-style: italic;">
                                Catatan: Nilai dinormalisasi dari 0 hingga 100 agar dapat dibandingkan secara berimbang.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // ==========================================
    // 1. Data Injection from Backend
    // ==========================================
    const riskCounts = @json($riskCounts);
    const topRiskData = @json($topRisk);
    const topPortsData = @json($topPorts);
    const correlationData = @json($correlationData);
    const globalAverages = @json($globalAverages);
    const countriesList = @json($countries->keyBy('country_code'));

    // ==========================================
    // 2. Chart 1: Risk Distribution (Doughnut)
    // ==========================================
    new Chart(document.getElementById('riskDistributionChart'), {
        type: 'doughnut',
        data: {
            labels: ['Risiko Tinggi', 'Risiko Sedang', 'Risiko Rendah'],
            datasets: [{
                data: [riskCounts.High, riskCounts.Medium, riskCounts.Low],
                backgroundColor: [
                    'rgba(239, 68, 68, 0.85)',  // Red
                    'rgba(245, 158, 11, 0.85)', // Orange
                    'rgba(16, 185, 129, 0.85)'  // Green
                ],
                borderColor: ['#fff', '#fff', '#fff'],
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: { family: 'Poppins', size: 11 }
                    }
                }
            }
        }
    });

    // ==========================================
    // 3. Chart 2: Top 10 Highest Risk Countries (Horizontal Bar)
    // ==========================================
    new Chart(document.getElementById('topRiskChart'), {
        type: 'bar',
        data: {
            labels: topRiskData.map(c => c.country_name),
            datasets: [{
                label: 'Skor Risiko Rantai Pasok',
                data: topRiskData.map(c => c.risk_score),
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderColor: '#ef4444',
                borderWidth: 1.5,
                borderRadius: 6,
                barThickness: 16
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            scales: {
                x: {
                    max: 100,
                    grid: { display: false },
                    ticks: { font: { family: 'Poppins', size: 10 } }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { family: 'Poppins', size: 10, weight: 'bold' } }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // ==========================================
    // 4. Chart 3: Top 10 Countries by Port Count (Vertical Bar)
    // ==========================================
    new Chart(document.getElementById('topPortsChart'), {
        type: 'bar',
        data: {
            labels: topPortsData.map(c => c.country_name),
            datasets: [{
                label: 'Jumlah Pelabuhan Terdaftar',
                data: topPortsData.map(c => c.total),
                backgroundColor: 'rgba(2, 132, 199, 0.8)',
                borderColor: '#0284c7',
                borderWidth: 1.5,
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Poppins', size: 10, weight: '500' } }
                },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { family: 'Poppins', size: 10 } }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // ==========================================
    // 5. Chart 4: Correlation Bubble Chart (Risk vs Inflation)
    // ==========================================
    new Chart(document.getElementById('correlationChart'), {
        type: 'bubble',
        data: {
            datasets: [{
                label: 'Negara Dipantau',
                data: correlationData,
                backgroundColor: 'rgba(245, 158, 11, 0.7)',
                borderColor: '#f59e0b',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Tingkat Inflasi (%)',
                        font: { family: 'Poppins', size: 11, weight: 'bold' }
                    },
                    grid: { color: '#f8fafc' }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Skor Risiko Rantai Pasok',
                        font: { family: 'Poppins', size: 11, weight: 'bold' }
                    },
                    max: 100,
                    grid: { color: '#f8fafc' }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const raw = context.raw;
                            return `${raw.country}: Inflasi ${raw.x}%, Risiko ${raw.y}`;
                        }
                    }
                }
            }
        }
    });

    // ==========================================
    // 6. Interactive Country Profile & Radar Comparison Chart
    // ==========================================
    const countrySelector = document.getElementById('radarCountrySelector');
    
    // DOM Elements for Country Profile
    const profileFlag = document.getElementById('profileFlag');
    const profileCountryName = document.getElementById('profileCountryName');
    const profileCountryCode = document.getElementById('profileCountryCode');
    const profileCapital = document.getElementById('profileCapital');
    const profileCurrency = document.getElementById('profileCurrency');
    const profilePopulation = document.getElementById('profilePopulation');
    const profileWeather = document.getElementById('profileWeather');
    const profilePorts = document.getElementById('profilePorts');
    const profileRiskBadge = document.getElementById('profileRiskBadge');

    let radarChart = null;

    function updateCountryProfile(countryCode) {
        const country = countriesList[countryCode];
        if (!country) return;

        // Update Text & Badges
        profileFlag.src = country.flag || 'https://flagcdn.com/w320/un.png';
        profileCountryName.textContent = country.country_name;
        profileCountryCode.textContent = country.country_code;
        profileCapital.textContent = country.capital || '-';
        profileCurrency.textContent = country.currency || 'USD';
        
        // Format Population
        const pop = parseInt(country.population);
        profilePopulation.textContent = pop > 1000000 
            ? `${(pop / 1000000).toFixed(1)} Juta jiwa` 
            : `${pop.toLocaleString()} jiwa`;

        profileWeather.textContent = `${country.temperature}°C / ${country.weather}`;
        
        // Weather badge color
        if (country.temperature > 30 || country.temperature < 5) {
            profileWeather.className = 'badge bg-warning text-dark fw-bold';
        } else {
            profileWeather.className = 'badge bg-info text-white fw-bold';
        }

        profilePorts.textContent = `${country.port_count} pelabuhan`;

        // Risk Level Badge styling
        const score = country.risk_score;
        const level = country.risk_level;
        profileRiskBadge.textContent = `${score} (${level})`;
        
        if (level === 'High') {
            profileRiskBadge.className = 'badge bg-danger text-white';
        } else if (level === 'Medium') {
            profileRiskBadge.className = 'badge bg-warning text-dark';
        } else {
            profileRiskBadge.className = 'badge bg-success text-white';
        }

        // Render / Update Radar Chart
        renderRadarChart(country);
    }

    function renderRadarChart(country) {
        // Normalisasi data terpilih ke skala 0-100
        const selectedMetrics = [
            parseFloat(country.risk_score || 0),                                        // Skor Risiko direct
            Math.min(100, parseFloat(country.inflation || 0) * 5),                      // Inflasi (100% = 20% inflation)
            Math.min(100, Math.max(0, (parseFloat(country.temperature || 25) + 5) * 2)), // Suhu (-5 hingga 45 C)
            Math.min(100, (parseInt(country.population || 0) / 10000000)),              // Populasi (100% = 1M)
            Math.min(100, parseInt(country.port_count || 0) * 8)                       // Jumlah Pelabuhan (100% = 12 ports)
        ];

        // Normalisasi rata-rata global ke skala 0-100
        const globalMetrics = [
            parseFloat(globalAverages.risk_score),
            Math.min(100, parseFloat(globalAverages.inflation) * 5),
            Math.min(100, Math.max(0, (parseFloat(globalAverages.temperature) + 5) * 2)),
            Math.min(100, (parseInt(globalAverages.population) / 10000000)),
            Math.min(100, parseFloat(globalAverages.port_count) * 8)
        ];

        const radarData = {
            labels: ['Skor Risiko', 'Tingkat Inflasi', 'Temperatur Lingkungan', 'Skala Populasi', 'Infrastruktur Pelabuhan'],
            datasets: [
                {
                    label: country.country_name,
                    data: selectedMetrics,
                    fill: true,
                    backgroundColor: 'rgba(2, 132, 199, 0.2)',
                    borderColor: '#0284c7',
                    pointBackgroundColor: '#0284c7',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#0284c7'
                },
                {
                    label: 'Rata-rata Global',
                    data: globalMetrics,
                    fill: true,
                    backgroundColor: 'rgba(148, 163, 184, 0.2)',
                    borderColor: '#94a3b8',
                    pointBackgroundColor: '#94a3b8',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#94a3b8'
                }
            ]
        };

        if (radarChart) {
            radarChart.data = radarData;
            radarChart.update();
        } else {
            radarChart = new Chart(document.getElementById('comparisonRadarChart'), {
                type: 'radar',
                data: radarData,
                options: {
                    responsive: true,
                    scales: {
                        r: {
                            angleLines: { display: true, color: '#f1f5f9' },
                            grid: { color: '#f1f5f9' },
                            suggestedMin: 0,
                            suggestedMax: 100,
                            ticks: { display: false }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { font: { family: 'Poppins', size: 11 } }
                        }
                    }
                }
            });
        }
    }

    // Trigger initial load on dropdown change
    countrySelector.addEventListener('change', function () {
        updateCountryProfile(this.value);
    });

    // Load initial default (Indonesia ID)
    updateCountryProfile('ID');
});
</script>
@endsection