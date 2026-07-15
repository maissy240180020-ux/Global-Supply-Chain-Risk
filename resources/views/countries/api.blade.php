@extends('layouts.app')

@section('title', 'Integrasi & REST API')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold">🔌 Dashboard Integrasi & REST API</h2>
        <p class="text-muted">Pusat Integrasi API Eksternal & Dokumentasi REST API Lokal (PDF Halaman 2, 3 & 9)</p>
    </div>

    <!-- Navigasi Tab Premium -->
    <div class="card dashboard-card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom-0 pb-0">
            <ul class="nav nav-tabs border-bottom-0" id="apiTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-secondary" id="worldbank-tab" data-bs-toggle="tab" data-bs-target="#worldbank" type="button" role="tab" aria-controls="worldbank" aria-selected="true">
                        🏦 1. World Bank API (Page 2)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-secondary" id="restcountries-tab" data-bs-toggle="tab" data-bs-target="#restcountries" type="button" role="tab" aria-controls="restcountries" aria-selected="false">
                        🌍 2. REST Countries API (Page 3)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-secondary" id="localapi-tab" data-bs-toggle="tab" data-bs-target="#localapi" type="button" role="tab" aria-controls="localapi" aria-selected="false">
                        💻 3. REST API Mahasiswa (Page 9)
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="apiTabsContent">
                
                <!-- TAB 1: WORLD BANK API -->
                <div class="tab-pane fade show active" id="worldbank" role="tabpanel" aria-labelledby="worldbank-tab">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-1">🏦 Integrasi World Bank API</h5>
                            <p class="text-muted mb-0" style="font-size:0.85rem;">Tren Makroekonomi: GDP, Inflasi, Populasi, Ekspor & Impor</p>
                        </div>
                        <div class="bg-light p-2 rounded d-flex align-items-center gap-2">
                            <label for="wb_country_id" class="fw-semibold text-secondary mb-0" style="font-size: 0.85rem;">Negara:</label>
                            <form action="{{ route('countries.api') }}" method="GET" id="wbCountryForm" class="m-0">
                                <select name="country_id" id="wb_country_id" class="form-select form-select-sm border-0 bg-white" style="font-weight: 500;" onchange="this.form.submit()">
                                    @foreach($dbCountries as $c)
                                        <option value="{{ $c->id }}" {{ $selectedCountry && $selectedCountry->id == $c->id ? 'selected' : '' }}>
                                            {{ $c->country_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>

                    @if($selectedCountry)
                        <!-- Metrik Utama -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-3 border rounded text-center bg-light">
                                    <small class="text-muted d-block mb-1">GDP Terbaru</small>
                                    <h4 class="fw-bold mb-0 text-dark">
                                        @if($gdp && isset($gdp[0]['value']) && $gdp[0]['value'])
                                            ${{ number_format($gdp[0]['value'] / 1e12, 2) }} T
                                        @else
                                            N/A
                                        @endif
                                    </h4>
                                    <small class="text-muted" style="font-size: 0.75rem;">Tahun {{ $gdp[0]['date'] ?? '' }}</small>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-3 border rounded text-center bg-light">
                                    <small class="text-muted d-block mb-1">Laju Inflasi</small>
                                    <h4 class="fw-bold mb-0 text-dark">
                                        @if($inflation && isset($inflation[0]['value']) && $inflation[0]['value'])
                                            {{ number_format($inflation[0]['value'], 1) }}%
                                        @else
                                            N/A
                                        @endif
                                    </h4>
                                    <small class="text-muted" style="font-size: 0.75rem;">Tahun {{ $inflation[0]['date'] ?? '' }}</small>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-3 border rounded text-center bg-light">
                                    <small class="text-muted d-block mb-1">Ekspor (Barang & Jasa)</small>
                                    <h4 class="fw-bold mb-0 text-dark">
                                        @if($exports && isset($exports[0]['value']) && $exports[0]['value'])
                                            ${{ number_format($exports[0]['value'] / 1e9, 2) }} B
                                        @else
                                            N/A
                                        @endif
                                    </h4>
                                    <small class="text-muted" style="font-size: 0.75rem;">Tahun {{ $exports[0]['date'] ?? '' }}</small>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-3 border rounded text-center bg-light">
                                    <small class="text-muted d-block mb-1">Impor (Barang & Jasa)</small>
                                    <h4 class="fw-bold mb-0 text-dark">
                                        @if($imports && isset($imports[0]['value']) && $imports[0]['value'])
                                            ${{ number_format($imports[0]['value'] / 1e9, 2) }} B
                                        @else
                                            N/A
                                        @endif
                                    </h4>
                                    <small class="text-muted" style="font-size: 0.75rem;">Tahun {{ $imports[0]['date'] ?? '' }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Grafik Tren & Tabel Data -->
                        <div class="row">
                            <div class="col-lg-8 mb-3">
                                <div class="p-3 border rounded">
                                    <h6 class="fw-bold mb-3">📈 Tren Perkembangan Ekonomi</h6>
                                    <div style="height: 300px; position: relative;">
                                        <canvas id="trendChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <div class="p-3 border rounded" style="max-height:360px; overflow-y:auto;">
                                    <h6 class="fw-bold mb-3">📋 Data Historis (5 Tahun)</h6>
                                    <table class="table table-sm table-hover align-middle mb-0" style="font-size:0.8rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tahun</th>
                                                <th>GDP</th>
                                                <th>Inflasi</th>
                                                <th>Populasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($i = 0; $i < 5; $i++)
                                                @if(isset($gdp[$i]))
                                                    <tr>
                                                        <td class="fw-bold">{{ $gdp[$i]['date'] }}</td>
                                                        <td>{{ $gdp[$i]['value'] ? '$' . number_format($gdp[$i]['value'] / 1e9, 1) . 'B' : '-' }}</td>
                                                        <td>{{ isset($inflation[$i]['value']) && $inflation[$i]['value'] ? number_format($inflation[$i]['value'], 1) . '%' : '-' }}</td>
                                                        <td>{{ isset($population[$i]['value']) && $population[$i]['value'] ? number_format($population[$i]['value'] / 1e6, 1) . 'M' : '-' }}</td>
                                                    </tr>
                                                @endif
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- TAB 2: REST COUNTRIES API -->
                <div class="tab-pane fade" id="restcountries" role="tabpanel" aria-labelledby="restcountries-tab">
                    <h5 class="fw-bold mb-3">🌍 Integrasi REST Countries API</h5>
                    <p class="text-muted mb-4" style="font-size:0.85rem;">Menyajikan data dasar dari API global untuk pencocokan koordinat, bendera, dan mata uang.</p>
                    
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-bordered table-hover align-middle mb-0" style="font-size:0.85rem;">
                            <thead class="table-light sticky-top" style="z-index: 10;">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 80px;">Bendera</th>
                                    <th>Nama Negara</th>
                                    <th>Ibukota</th>
                                    <th>Wilayah</th>
                                    <th>Populasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($restCountries as $country)
                                    @if(is_array($country) && isset($country['name']['common']))
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if(isset($country['flags']) && isset($country['flags']['png']))
                                                <img src="{{ $country['flags']['png'] }}" width="45" style="border-radius:2px; border:1px solid #ddd;">
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="fw-semibold">{{ $country['name']['common'] }}</td>
                                        <td>{{ $country['capital'][0] ?? '-' }}</td>
                                        <td>{{ $country['region'] ?? '-' }}</td>
                                        <td>{{ number_format($country['population'] ?? 0) }}</td>
                                    </tr>
                                    @endif
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Gagal memuat data dari REST Countries API.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 3: LOCAL REST API FOR STUDENT -->
                <div class="tab-pane fade" id="localapi" role="tabpanel" aria-labelledby="localapi-tab">
                    <h5 class="fw-bold mb-3">💻 REST API Endpoint Mahasiswa</h5>
                    <p class="text-muted mb-4" style="font-size:0.85rem;">Berikut adalah 5 REST API yang wajib dibangun oleh mahasiswa sesuai spesifikasi proyek di halaman 9 dokumen PDF:</p>
                    
                    <div class="row g-3">
                        <!-- GET /api/countries -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-success font-monospace">GET</span>
                                        <span class="text-muted" style="font-size:0.75rem;">Data Negara</span>
                                    </div>
                                    <h6 class="fw-bold font-monospace mb-2">/api/countries</h6>
                                    <p class="text-muted mb-3" style="font-size:0.8rem;">Mengembalikan daftar lengkap negara di database beserta metrik dasar (GDP, inflasi, populasi, dsb.) dalam format JSON.</p>
                                </div>
                                <a href="/api/countries" target="_blank" class="btn btn-sm btn-outline-primary w-100 mt-2">
                                    <i class="bi bi-box-arrow-up-right"></i> Uji API Endpoint
                                </a>
                            </div>
                        </div>

                        <!-- GET /api/risk -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-success font-monospace">GET</span>
                                        <span class="text-muted" style="font-size:0.75rem;">Skor & Level Risiko</span>
                                    </div>
                                    <h6 class="fw-bold font-monospace mb-2">/api/risk</h6>
                                    <p class="text-muted mb-3" style="font-size:0.8rem;">Mengembalikan nilai skor risiko dan tingkatan risiko (*High, Medium, Low*) seluruh negara hasil kalkulasi sistem.</p>
                                </div>
                                <a href="/api/risk" target="_blank" class="btn btn-sm btn-outline-primary w-100 mt-2">
                                    <i class="bi bi-box-arrow-up-right"></i> Uji API Endpoint
                                </a>
                            </div>
                        </div>

                        <!-- GET /api/ports -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-success font-monospace">GET</span>
                                        <span class="text-muted" style="font-size:0.75rem;">Pelabuhan Dunia</span>
                                    </div>
                                    <h6 class="fw-bold font-monospace mb-2">/api/ports</h6>
                                    <p class="text-muted mb-3" style="font-size:0.8rem;">Mengembalikan daftar koordinat dan nama pelabuhan internasional dari *World Port Index Dataset*.</p>
                                </div>
                                <a href="/api/ports" target="_blank" class="btn btn-sm btn-outline-primary w-100 mt-2">
                                    <i class="bi bi-box-arrow-up-right"></i> Uji API Endpoint
                                </a>
                            </div>
                        </div>

                        <!-- GET /api/news -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-success font-monospace">GET</span>
                                        <span class="text-muted" style="font-size:0.75rem;">Sentimen Berita</span>
                                    </div>
                                    <h6 class="fw-bold font-monospace mb-2">/api/news</h6>
                                    <p class="text-muted mb-3" style="font-size:0.8rem;">Mengembalikan artikel berita rantai pasok global beserta hasil kalkulasi sentimen (*Lexicon Based*) secara dinamis.</p>
                                </div>
                                <a href="/api/news" target="_blank" class="btn btn-sm btn-outline-primary w-100 mt-2">
                                    <i class="bi bi-box-arrow-up-right"></i> Uji API Endpoint
                                </a>
                            </div>
                        </div>

                        <!-- GET /api/currency -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-success font-monospace">GET</span>
                                        <span class="text-muted" style="font-size:0.75rem;">Kurs Mata Uang</span>
                                    </div>
                                    <h6 class="fw-bold font-monospace mb-2">/api/currency</h6>
                                    <p class="text-muted mb-3" style="font-size:0.8rem;">Mengembalikan data kurs realtime konversi mata uang dasar USD terhadap valuta asing global.</p>
                                </div>
                                <a href="/api/currency" target="_blank" class="btn btn-sm btn-outline-primary w-100 mt-2">
                                    <i class="bi bi-box-arrow-up-right"></i> Uji API Endpoint
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Pertahankan tab aktif saat halaman melakukan submit dropdown
    var activeTab = localStorage.getItem('activeApiTab');
    if (activeTab) {
        var triggerEl = document.querySelector('#' + activeTab);
        if (triggerEl) {
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        }
    }

    var tabElements = document.querySelectorAll('button[data-bs-toggle="tab"]');
    tabElements.forEach(function(el) {
        el.addEventListener('shown.bs.tab', function(event) {
            localStorage.setItem('activeApiTab', event.target.id);
        });
    });

    // Chart World Bank
    var ctx = document.getElementById('trendChart');
    if (!ctx) return;

    var gdpData = @json($gdp);
    var inflationData = @json($inflation);

    if (!gdpData || !inflationData) return;

    var labels = [];
    var gdpValues = [];
    var inflationValues = [];

    for (var i = gdpData.length - 1; i >= 0; i--) {
        labels.push(gdpData[i].date);
        gdpValues.push(gdpData[i].value ? (gdpData[i].value / 1e12) : null);
        inflationValues.push(inflationData[i] && inflationData[i].value ? inflationData[i].value : null);
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'GDP ($ Triliun)',
                    data: gdpValues,
                    borderColor: '#475569',
                    backgroundColor: 'rgba(71, 85, 105, 0.08)',
                    yAxisID: 'y-gdp',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Inflasi (%)',
                    data: inflationValues,
                    borderColor: '#f59e0b',
                    backgroundColor: 'transparent',
                    yAxisID: 'y-inflation',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                'y-gdp': {
                    type: 'linear',
                    position: 'left',
                    title: {
                        display: true,
                        text: 'GDP ($ Triliun)',
                        color: '#475569'
                    }
                },
                'y-inflation': {
                    type: 'linear',
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Inflasi (%)',
                        color: '#f59e0b'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection