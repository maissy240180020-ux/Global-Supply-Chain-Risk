@extends('layouts.app')

@section('title', '🇺🇳 ' . $country->country_name . ' | Detail Negara')

@section('content')

<style>
    .hover-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important; }
    .monitor-btn:hover { transform: scale(1.05); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
</style>

@php
    $color = '#10b981'; // Green (Low)
    $badge = 'bg-success text-white';
    $riskText = 'Risiko Rendah';
    $saran = 'Pemantauan Rutin';
    $icon = 'bi-shield-check';
    if ($country->risk_level == 'Medium') {
        $color = '#f59e0b';
        $badge = 'bg-warning text-dark';
        $riskText = 'Risiko Sedang';
        $saran = 'Perlu Kewaspadaan';
        $icon = 'bi-shield-exclamation';
    } elseif ($country->risk_level == 'High') {
        $color = '#ef4444';
        $badge = 'bg-danger text-white';
        $riskText = 'Risiko Tinggi';
        $saran = 'Pantau Ketat';
        $icon = 'bi-shield-x';
    }

    $wLower = strtolower($country->weather ?? '');
    $wIcon = 'bi-cloud-sun-fill';
    $wColor = 'text-info';
    if (str_contains($wLower, 'cerah') || str_contains($wLower, 'clear') || str_contains($wLower, 'sunny') || str_contains($wLower, 'hot')) {
        $wIcon = 'bi-sun-fill';
        $wColor = 'text-warning';
    } elseif (str_contains($wLower, 'hujan') || str_contains($wLower, 'rain') || str_contains($wLower, 'shower')) {
        $wIcon = 'bi-cloud-rain-fill';
        $wColor = 'text-primary';
    }
@endphp

<div class="container-fluid">

    <!-- Top Action Row -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <a href="{{ route('countries.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 hover-card" 
               style="border-radius: 10px; border-color: #cbd5e1; color: #475569; font-size: 0.88rem; font-weight: 500; height: 38px;">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- QUICK SUMMARY -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white hover-card" style="border-radius: 12px; cursor: default;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; background-color: #f0f9ff;">
                        <i class="bi {{ $wIcon }} fs-4 {{ $wColor }}"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Status Cuaca</small>
                        <strong class="text-dark fs-6">{{ $country->weather ?? 'Kondisi Stabil' }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white hover-card" style="border-radius: 12px; cursor: default;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; background-color: {{ $color }}20;">
                        <i class="bi {{ $icon }} fs-4" style="color: {{ $color }};"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Status Risiko</small>
                        <strong class="fs-6" style="color: {{ $color }};">{{ $riskText }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white hover-card" style="border-radius: 12px; cursor: default;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; background-color: #f3f4f6;">
                        <i class="bi bi-broadcast-pin fs-4 text-dark"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Rekomendasi Sistem</small>
                        <strong class="text-dark fs-6">{{ $saran }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <!-- SECTION 1: IDENTITAS & GEOGRAFI -->
        <div class="col-lg-7 col-12">
            <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-globe-americas text-primary"></i> Identitas & Geografi Wilayah
                    </h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <!-- Hero Info -->
                    <div class="d-flex align-items-center flex-wrap gap-4 mb-4 pb-4 border-bottom">
                        @if($country->flag)
                            <img src="{{ $country->flag }}" alt="{{ $country->country_name }}"
                                 style="width: 120px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 10px rgba(0,0,0,0.06);">
                        @else
                            <div class="d-flex align-items-center justify-content-center rounded border bg-light text-secondary" style="width: 120px; height: 80px; font-size: 2.5rem;">🌍</div>
                        @endif
                        <div class="flex-fill">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h2 class="fw-bold text-dark mb-0">{{ $country->country_name }}</h2>
                                <span class="badge bg-secondary px-2.5 py-1" style="font-size: 0.72rem; background-color: #64748b !important;">{{ $country->country_code }}</span>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                Ibu Kota: <strong class="text-dark">{{ $country->capital ?? '-' }}</strong>
                            </p>
                        </div>
                    </div>

                    <!-- Geo Stats Grid -->
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-4 shadow-sm" style="background-color: #e0f2fe; border: 1px solid #bae6fd;">
                                <small class="text-muted d-block mb-1" style="font-size: 0.75rem;"><i class="bi bi-map"></i> Wilayah (Region)</small>
                                <strong class="text-dark fs-6">{{ $extraData['region'] }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-4 shadow-sm" style="background-color: #fef3c7; border: 1px solid #fde68a;">
                                <small class="text-muted d-block mb-1" style="font-size: 0.75rem;"><i class="bi bi-arrows-fullscreen"></i> Luas Wilayah (Area)</small>
                                <strong class="text-dark fs-6">{{ $extraData['area'] }} km²</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-4 shadow-sm" style="background-color: #dcfce7; border: 1px solid #bbf7d0;">
                                <small class="text-muted d-block mb-1" style="font-size: 0.75rem;"><i class="bi bi-people"></i> Total Populasi</small>
                                <strong class="text-dark fs-6" id="wb-population">
                                    <span class="spinner-border spinner-border-sm text-secondary" role="status" aria-hidden="true"></span>
                                </strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-4 shadow-sm" style="background-color: #f3e8ff; border: 1px solid #e9d5ff;">
                                <small class="text-muted d-block mb-1" style="font-size: 0.75rem;"><i class="bi bi-chat-quote"></i> Bahasa Resmi</small>
                                <strong class="text-dark text-truncate d-block fs-6" title="{{ $extraData['language'] }}">{{ $extraData['language'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: EKONOMI & PERDAGANGAN -->
        <div class="col-lg-5 col-12">
            <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-graph-up-arrow text-success"></i> Ekonomi & Perdagangan
                    </h6>
                </div>
                <div class="card-body p-4 pt-0">
                    
                    <div class="d-flex align-items-center justify-content-between p-3 mb-3 rounded-4 shadow-sm" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <div>
                            <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Mata Uang</small>
                            <strong class="text-dark fs-5">{{ $country->currency }}</strong>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Nilai Tukar (Live)</small>
                            <strong class="text-success fs-5">1 USD = {{ number_format((float)$extraData['exchange_rate'], 2) }} {{ $country->currency }}</strong>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush mt-2">
                        <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-light">
                            <span class="text-muted fw-medium"><i class="bi bi-cash-coin me-2 text-primary"></i>Produk Domestik Bruto (GDP)</span>
                            <div class="text-end" id="wb-gdp">
                                <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                            </div>
                        </li>
                        <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-light">
                            <span class="text-muted fw-medium"><i class="bi bi-percent me-2 text-danger"></i>Tingkat Inflasi</span>
                            <div class="text-end" id="wb-inflation">
                                <span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true"></span>
                            </div>
                        </li>
                        <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-light">
                            <span class="text-muted fw-medium"><i class="bi bi-box-arrow-up-right me-2 text-success"></i>Total Ekspor</span>
                            <div class="text-end" id="wb-export">
                                <span class="spinner-border spinner-border-sm text-success" role="status" aria-hidden="true"></span>
                            </div>
                        </li>
                        <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-light">
                            <span class="text-muted fw-medium"><i class="bi bi-box-arrow-in-down-left me-2 text-warning"></i>Total Impor</span>
                            <div class="text-end" id="wb-import">
                                <span class="spinner-border spinner-border-sm text-warning" role="status" aria-hidden="true"></span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- SECTION 3: CUACA & LINGKUNGAN -->
        <div class="col-lg-5 col-12">
            <div class="card border-0 shadow-lg h-100 hover-card" style="border-radius: 16px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; position: relative; overflow: hidden;">
                <!-- Decorative Circle -->
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%; pointer-events: none;"></div>
                
                <div class="card-body p-4 position-relative z-1">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h6 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-cloud-sun text-info fs-5"></i> Cuaca Terkini
                        </h6>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-4 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
                        <div>
                            <span class="text-light opacity-75 d-block mb-1" style="font-size: 0.85rem;"><i class="bi bi-geo-alt-fill text-danger"></i> {{ $country->capital ?? $country->country_name }}</span>
                            <h3 class="fw-bold mb-0" style="font-size: 3.2rem; letter-spacing: -1px;">{{ $country->temperature ?? '-' }}°C</h3>
                            <span class="badge bg-white bg-opacity-25 mt-2 fw-normal px-3 py-2 rounded-pill">{{ $country->weather ?? 'Data Cuaca Kosong' }}</span>
                        </div>
                        <i class="bi {{ $wIcon }} {{ $wColor }}" style="font-size: 5rem; text-shadow: 0 10px 20px rgba(0,0,0,0.3); transform: translateY(-10px);"></i>
                    </div>

                    <div class="row g-3">
                        <div class="col-4 text-center">
                            <div class="bg-white bg-opacity-10 rounded-4 p-2 h-100 d-flex flex-column justify-content-center">
                                <i class="bi bi-droplet text-info fs-4 mb-2"></i>
                                <strong class="d-block fs-6 mb-1">{{ $extraData['humidity'] }}%</strong>
                                <small class="text-light opacity-75" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Kelembapan</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="bg-white bg-opacity-10 rounded-4 p-2 h-100 d-flex flex-column justify-content-center">
                                <i class="bi bi-wind text-light fs-4 mb-2"></i>
                                <strong class="d-block fs-6 mb-1">{{ $extraData['wind_speed'] }} km/h</strong>
                                <small class="text-light opacity-75" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Angin</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="bg-white bg-opacity-10 rounded-4 p-2 h-100 d-flex flex-column justify-content-center">
                                <i class="bi bi-cloud-rain text-primary fs-4 mb-2"></i>
                                <strong class="d-block fs-6 mb-1">{{ $extraData['rainfall'] }} mm</strong>
                                <small class="text-light opacity-75" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Hujan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: RISIKO RANTAI PASOK -->
        <div class="col-lg-7 col-12">
            <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-shield-exclamation text-danger"></i> Evaluasi Risiko Rantai Pasok
                    </h6>
                </div>
                <div class="card-body p-4 pt-0 d-flex flex-column">
                    
                    <div class="mb-4 p-4 rounded-4 shadow-sm" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-dark fs-5">Skor Kerentanan (Risk Score)</span>
                            <span class="badge {{ $badge }} fs-6 px-3 py-2 shadow-sm" style="border-radius: 8px; font-weight: 600;">
                                {{ $riskText }} ({{ round($country->risk_score) }}/100)
                            </span>
                        </div>
                        <div class="progress mb-2 bg-light shadow-inner" style="height: 16px; border-radius: 16px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated {{ str_replace(' text-white','',str_replace(' text-dark','',$badge)) }}" style="width: {{ $country->risk_score }}%; border-radius: 16px;"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted mt-2 fw-medium" style="font-size: 0.75rem;">
                            <span><i class="bi bi-emoji-smile text-success"></i> 0 (Aman)</span>
                            <span>100 (Sangat Berbahaya) <i class="bi bi-emoji-frown text-danger"></i></span>
                        </div>
                    </div>

                    <div class="p-4 mb-0 border-0 shadow-sm flex-fill d-flex flex-column justify-content-center" style="border-radius: 16px; background-color: {{ $color }}10;">
                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: {{ $color }};">
                            <i class="bi bi-robot fs-5"></i> AI Supply Chain Insights
                        </h6>
                        <p class="mb-0 text-dark" style="font-size: 0.95rem; line-height: 1.7;">
                            @if($country->risk_level == 'High')
                                <strong class="text-danger">Peringatan Kritis:</strong> Negara <strong>{{ $country->country_name }}</strong> memiliki tingkat kerentanan tinggi. Sangat disarankan untuk memantau cuaca ekstrem secara ketat dan melakukan diversifikasi rute pelabuhan logistik guna menghindari kemacetan suplai dan kerugian operasional akibat gangguan stabilitas regional.
                            @elseif($country->risk_level == 'Medium')
                                <strong class="text-warning">Perhatian Sedang:</strong> Stabilitas rantai pasok berada dalam tingkat menengah. Operasional pengiriman disarankan berjalan dengan pengawasan rutin terhadap fluktuasi ekonomi mikro (inflasi) dan perubahan iklim lokal. Pastikan asuransi kargo aktif.
                            @else
                                <strong class="text-success">Sangat Stabil:</strong> Wilayah ini menunjukkan kestabilan tinggi untuk rantai pasok global. Jalur pelabuhan dan kegiatan operasional logistik direkomendasikan berjalan penuh sebagai gerbang distribusi perdagangan prioritas tanpa hambatan signifikan.
                            @endif
                        </p>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countryCode = '{{ $country->country_code }}'.toLowerCase();

    const indicators = {
        'wb-population': 'SP.POP.TOTL',      // Population, total
        'wb-gdp': 'NY.GDP.MKTP.CD',          // GDP (current US$)
        'wb-inflation': 'FP.CPI.TOTL.ZG',    // Inflation, consumer prices (annual %)
        'wb-export': 'NE.EXP.GNFS.CD',       // Exports of goods and services (current US$)
        'wb-import': 'NE.IMP.GNFS.CD'        // Imports of goods and services (current US$)
    };

    function formatNumber(value, isPercentage, isCurrency) {
        if (value === null || value === undefined) return "Data tidak tersedia";
        
        let formatted = "";
        let valNum = parseFloat(value);
        
        if (isPercentage) {
            return `<strong class="text-danger fs-6">${valNum.toFixed(2)} %</strong>`;
        }

        if (valNum >= 1e12) {
            formatted = (valNum / 1e12).toFixed(2) + " Triliun";
        } else if (valNum >= 1e9) {
            formatted = (valNum / 1e9).toFixed(2) + " Miliar";
        } else if (valNum >= 1e6) {
            formatted = (valNum / 1e6).toFixed(2) + " Juta";
        } else {
            formatted = valNum.toLocaleString('id-ID');
        }

        if (isCurrency) {
            return `<strong class="text-dark fs-6">${formatted} USD</strong>`;
        } else {
            return `<strong class="text-dark fs-6">${formatted} Jiwa</strong>`;
        }
    }

    async function fetchIndicatorData(elementId, indicatorCode, isPercentage = false, isCurrency = true) {
        const url = `https://api.worldbank.org/v2/country/${countryCode}/indicator/${indicatorCode}?format=json&per_page=5`;
        const el = document.getElementById(elementId);
        
        try {
            const response = await fetch(url);
            const data = await response.json();
            
            if (data && data[1] && data[1].length > 0) {
                const validData = data[1].find(item => item.value !== null);
                
                if (validData) {
                    const valueFormatted = formatNumber(validData.value, isPercentage, isCurrency);
                    const year = validData.date;
                    el.innerHTML = `${valueFormatted} <br><small class="text-muted" style="font-size: 0.72rem;">Data Tahun ${year}</small>`;
                } else {
                    el.innerHTML = `<span class="text-muted fw-bold" style="font-size: 0.85rem;">Data tidak tersedia</span>`;
                }
            } else {
                el.innerHTML = `<span class="text-muted fw-bold" style="font-size: 0.85rem;">Data tidak tersedia</span>`;
            }
        } catch (error) {
            console.error('Error fetching WB data for ' + indicatorCode + ':', error);
            el.innerHTML = `<span class="text-danger fw-bold" style="font-size: 0.85rem;">Data tidak tersedia</span>`;
        }
    }

    // Fetch all indicators concurrently
    fetchIndicatorData('wb-population', indicators['wb-population'], false, false);
    fetchIndicatorData('wb-gdp', indicators['wb-gdp'], false, true);
    fetchIndicatorData('wb-inflation', indicators['wb-inflation'], true, false);
    fetchIndicatorData('wb-export', indicators['wb-export'], false, true);
    fetchIndicatorData('wb-import', indicators['wb-import'], false, true);
});
</script>
@endpush