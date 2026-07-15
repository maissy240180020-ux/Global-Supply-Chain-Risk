@extends('layouts.app')

@section('title', 'Integrasi World Bank API')

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0">🏦 World Bank API Integration</h2>
            <p class="text-muted mb-0">Tren Ekonomi Makro: GDP, Inflasi, Populasi, Ekspor, & Impor (PDF Halaman 2)</p>
        </div>

        <!-- Country Selector -->
        <div class="bg-white p-2 rounded shadow-sm border border-light d-flex align-items-center gap-2">
            <label for="country_id" class="fw-semibold text-secondary mb-0">Negara:</label>
            <form action="{{ route('world-bank.index') }}" method="GET" id="countrySelectForm" class="m-0">
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

    @if($selectedCountry)
        <!-- Kartu Metrik Terbaru -->
        <div class="row g-3 mb-4">
            <div class="col-md">
                <div class="card dashboard-card h-100 p-3 text-center">
                    <small class="text-muted d-block mb-1">GDP Terbaru</small>
                    <h3 class="fw-bold mb-0 text-dark">
                        @if($gdp && isset($gdp[0]['value']) && $gdp[0]['value'])
                            ${{ number_format($gdp[0]['value'] / 1e12, 2) }} T
                        @else
                            N/A
                        @endif
                    </h3>
                    <small class="text-muted">Tahun {{ $gdp[0]['date'] ?? '' }}</small>
                </div>
            </div>

            <div class="col-md">
                <div class="card dashboard-card h-100 p-3 text-center">
                    <small class="text-muted d-block mb-1">Inflasi Terbaru</small>
                    <h3 class="fw-bold mb-0 text-dark">
                        @if($inflation && isset($inflation[0]['value']) && $inflation[0]['value'])
                            {{ number_format($inflation[0]['value'], 1) }}%
                        @else
                            N/A
                        @endif
                    </h3>
                    <small class="text-muted">Tahun {{ $inflation[0]['date'] ?? '' }}</small>
                </div>
            </div>

            <div class="col-md">
                <div class="card dashboard-card h-100 p-3 text-center">
                    <small class="text-muted d-block mb-1">Ekspor Baru</small>
                    <h3 class="fw-bold mb-0 text-dark">
                        @if($exports && isset($exports[0]['value']) && $exports[0]['value'])
                            ${{ number_format($exports[0]['value'] / 1e9, 2) }} B
                        @else
                            N/A
                        @endif
                    </h3>
                    <small class="text-muted">Tahun {{ $exports[0]['date'] ?? '' }}</small>
                </div>
            </div>

            <div class="col-md">
                <div class="card dashboard-card h-100 p-3 text-center">
                    <small class="text-muted d-block mb-1">Impor Baru</small>
                    <h3 class="fw-bold mb-0 text-dark">
                        @if($imports && isset($imports[0]['value']) && $imports[0]['value'])
                            ${{ number_format($imports[0]['value'] / 1e9, 2) }} B
                        @else
                            N/A
                        @endif
                    </h3>
                    <small class="text-muted">Tahun {{ $imports[0]['date'] ?? '' }}</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Grafik Tren -->
            <div class="col-lg-8 mb-4">
                <div class="card dashboard-card shadow-sm border-0 h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">📈 Grafik Tren Ekonomi 5 Tahun Terakhir</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 320px; position: relative;">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Historis -->
            <div class="col-lg-4 mb-4">
                <div class="card dashboard-card shadow-sm border-0 h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">📋 Detail Historis</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
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
                                                <td>
                                                    @if($gdp[$i]['value'])
                                                        ${{ number_format($gdp[$i]['value'] / 1e9, 1) }} B
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="{{ isset($inflation[$i]['value']) && $inflation[$i]['value'] > 5 ? 'text-danger' : 'text-success' }}">
                                                    {{ isset($inflation[$i]['value']) && $inflation[$i]['value'] ? number_format($inflation[$i]['value'], 1) . '%' : '-' }}
                                                </td>
                                                <td>
                                                    {{ isset($population[$i]['value']) && $population[$i]['value'] ? number_format($population[$i]['value'] / 1e6, 1) . ' M' : '-' }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">Silakan pilih negara untuk menampilkan data integrasi World Bank API.</div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('trendChart');
    if (!ctx) return;

    var gdpData = @json($gdp);
    var inflationData = @json($inflation);

    if (!gdpData || !inflationData) return;

    // Balik urutan agar tahun dari paling lampau ke paling baru (kiri ke kanan)
    var labels = [];
    var gdpValues = [];
    var inflationValues = [];

    for (var i = gdpData.length - 1; i >= 0; i--) {
        labels.push(gdpData[i].date);
        gdpValues.push(gdpData[i].value ? (gdpData[i].value / 1e12) : null); // T
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
                    backgroundColor: 'rgba(71, 85, 105, 0.1)',
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
                    },
                    grid: {
                        drawOnChartArea: true
                    }
                },
                'y-inflation': {
                    type: 'linear',
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Laju Inflasi (%)',
                        color: '#f59e0b'
                    },
                    grid: {
                        drawOnChartArea: false // Mencegah grid tabrakan
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
