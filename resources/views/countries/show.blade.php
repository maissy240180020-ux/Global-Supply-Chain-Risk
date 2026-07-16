@extends('layouts.app')

@section('title', 'Detail Negara | SIMRPG')

@section('content')

<div class="container-fluid">

    <!-- Top Action Row -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <a href="{{ route('countries.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" 
               style="border-radius: 10px; border-color: #cbd5e1; color: #475569; font-size: 0.88rem; font-weight: 500; height: 38px; transition: all 0.2s;">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        <div>
            <a href="/dashboard?country_id={{ $country->id }}" class="btn text-white d-inline-flex align-items-center gap-2" 
               style="background: linear-gradient(135deg, #475569 0%, #334155 100%); border: none; border-radius: 10px; font-size: 0.88rem; font-weight: 600; height: 38px; transition: all 0.2s;">
                <i class="bi bi-speedometer2"></i> Monitor Negara
            </a>
        </div>
    </div>

    <!-- Country Hero Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
        <div class="card-body p-4 bg-white">
            <div class="d-flex align-items-center flex-wrap gap-4">
                <!-- Flag -->
                @if($country->flag)
                    <img src="{{ $country->flag }}" alt="{{ $country->country_name }}"
                         style="width: 100px; height: 66px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 10px rgba(0,0,0,0.06);">
                @else
                    <div class="d-flex align-items-center justify-content-center rounded border bg-light text-secondary" style="width: 100px; height: 66px; font-size: 2rem;">
                        🌍
                    </div>
                @endif

                <!-- Country Info -->
                <div class="flex-fill">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h2 class="fw-bold text-dark mb-0">{{ $country->country_name }}</h2>
                        <span class="badge bg-secondary px-2.5 py-1" style="font-size: 0.72rem; background-color: #64748b !important;">
                            {{ $country->country_code }}
                        </span>
                    </div>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.85rem;">
                        <i class="bi bi-geo-alt-fill me-1"></i> Ibu Kota: <strong>{{ $country->capital ?? '-' }}</strong> | Mata Uang: <strong>{{ $country->currency }}</strong>
                    </p>
                </div>

                <!-- Risk Badge Pillar -->
                <div class="text-md-end text-start">
                    @php
                        $color = '#10b981'; // Green (Low)
                        $badge = 'bg-success text-white';
                        if ($country->risk_level == 'Medium') {
                            $color = '#f59e0b';
                            $badge = 'bg-warning text-dark';
                        } elseif ($country->risk_level == 'High') {
                            $color = '#ef4444';
                            $badge = 'bg-danger text-white';
                        }
                    @endphp
                    <small class="text-muted d-block uppercase fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Status Risiko Rantai Pasok</small>
                    <span class="badge {{ $badge }} fs-6 px-3 py-1.5" style="font-weight: 700; border-radius: 8px;">
                        {{ $country->risk_level == 'High' ? 'Tinggi' : ($country->risk_level == 'Medium' ? 'Sedang' : 'Rendah') }} ({{ round($country->risk_score) }}/100)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Details Map vs Economy -->
    <div class="row g-4 mb-4">
        
        <!-- Left Column: Geography & Coordinates & Mini Map -->
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-light">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-globe-americas text-secondary"></i> Geografi & Koordinat Wilayah
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3" style="background-color: #f8fafc !important;">
                                <small class="text-muted d-block mb-1" style="font-size: 0.72rem;">Garis Lintang (Latitude)</small>
                                <strong class="text-dark" style="font-size: 0.95rem;">{{ $country->latitude ?? '-' }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3" style="background-color: #f8fafc !important;">
                                <small class="text-muted d-block mb-1" style="font-size: 0.72rem;">Garis Bujur (Longitude)</small>
                                <strong class="text-dark" style="font-size: 0.95rem;">{{ $country->longitude ?? '-' }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3" style="background-color: #f8fafc !important;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-secondary bg-white shadow-none" style="width: 40px; height: 40px;">
                                    <i class="bi bi-people-fill fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Estimasi Populasi</small>
                                    <strong class="text-dark" style="font-size: 1.05rem;">{{ number_format($country->population ?? 0) }} Penduduk</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mini Leaflet Map -->
                    <div class="rounded overflow-hidden border border-light shadow-sm">
                        <div id="countryMiniMap" style="height: 250px; z-index: 1;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Economics & Weather & Risk details -->
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-light">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-graph-up-arrow text-secondary"></i> Indikator Keuangan & Risiko
                    </h6>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    
                    <!-- Financial Metrics -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6 col-12">
                            <div class="p-3 rounded-3 d-flex align-items-center gap-3 border-light" style="background-color: #f8fafc; border: 1px solid #f1f5f9;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-success bg-white shadow-sm" style="width: 44px; height: 44px; min-width: 44px;">
                                    <i class="bi bi-cash-coin fs-5"></i>
                                </div>
                                <div style="overflow: hidden;">
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Produk Domestik Bruto (GDP)</small>
                                    <strong class="text-dark d-block text-truncate" style="font-size: 0.92rem;" title="{{ number_format($country->gdp ?? 0, 2) }}">
                                        ${{ number_format($country->gdp ?? 0, 2) }}
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="p-3 rounded-3 d-flex align-items-center gap-3 border-light" style="background-color: #f8fafc; border: 1px solid #f1f5f9;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-danger bg-white shadow-sm" style="width: 44px; height: 44px; min-width: 44px;">
                                    <i class="bi bi-percent fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Tingkat Inflasi</small>
                                    <strong class="text-dark" style="font-size: 0.92rem;">
                                        {{ number_format($country->inflation ?? 0, 2) }} %
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Risk Bar -->
                    <div class="mb-4 p-3.5 rounded-3 bg-light border-0" style="background-color: #f8fafc !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold text-secondary" style="font-size: 0.8rem;">Skor Risiko Kerentanan</span>
                            <strong style="color: {{ $color }}; font-size: 0.95rem;">{{ round($country->risk_score) }} / 100</strong>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 12px;">
                            <div class="progress-bar" style="background-color: {{ $color }}; width: {{ $country->risk_score }}%; border-radius: 12px;"></div>
                        </div>
                        <small class="text-muted d-block mt-2" style="font-size: 0.7rem; line-height: 1.4;">
                            *Skor risiko dikalkulasikan berdasarkan riwayat cuaca, logistik pelabuhan, inflasi, dan GDP.
                        </small>
                    </div>

                    <!-- Weather Info -->
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="py-3 px-4 rounded-3 d-flex align-items-center justify-content-between" 
                                 style="background: linear-gradient(135deg, #475569 0%, #334155 100%); color: white; border-radius: 12px; min-height: 95px;">
                                <div>
                                    <small class="text-light d-block mb-1" style="font-size: 0.72rem; opacity: 0.85; line-height: 1.2;">Kondisi Cuaca Real-Time</small>
                                    <h4 class="fw-bold mb-0 text-white" style="line-height: 1.2;">{{ $country->weather ?? 'N/A' }}</h4>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fw-bold text-white" style="font-size: 2.2rem; line-height: 1.1;">{{ $country->temperature ?? '-' }}°C</span>
                                    @php
                                        $wLower = strtolower($country->weather ?? '');
                                        $wIcon = 'bi-cloud-sun-fill';
                                        if (str_contains($wLower, 'cerah') || str_contains($wLower, 'clear') || str_contains($wLower, 'sunny') || str_contains($wLower, 'hot')) {
                                            $wIcon = 'bi-sun-fill text-warning';
                                        } elseif (str_contains($wLower, 'hujan') || str_contains($wLower, 'rain') || str_contains($wLower, 'shower')) {
                                            $wIcon = 'bi-cloud-rain-fill text-info';
                                        }
                                    @endphp
                                    <i class="bi {{ $wIcon }}" style="font-size: 2.5rem; line-height: 1;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- AI Supply Chain Stability Terminal Card -->
    <div class="card border-0 shadow-sm text-white mb-4" style="border-radius: 16px; background-color: #0f172a;">
        <div class="card-header bg-transparent border-bottom py-3" style="border-color: rgba(255,255,255,0.08) !important;">
            <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                🤖 AI Supply Chain Stability Report
            </h6>
        </div>
        <div class="card-body p-4">
            <h6 class="text-secondary fw-semibold text-uppercase mb-2" style="font-size: 0.68rem; letter-spacing: 0.05em; color: #94a3b8 !important;">Analisis Kerentanan Rantai Pasok</h6>
            <p class="mb-3" style="font-size: 0.85rem; line-height: 1.65; color: #cbd5e1;">
                Negara <strong>{{ $country->country_name }}</strong> dikategorikan memiliki tingkat risiko rantai pasok <strong>{{ $country->risk_level }}</strong> dengan skor indeks <strong>{{ round($country->risk_score) }}/100</strong>. Secara ekonomi, GDP nasional tercatat sebesar <strong>${{ number_format($country->gdp ?? 0, 2) }}</strong> dengan rasio inflasi tahunan berada di kisaran <strong>{{ number_format($country->inflation ?? 0, 2) }}%</strong>.
            </p>
            <div class="p-3 mb-0" style="border-radius: 12px; background-color: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08);">
                <p class="mb-0 text-white" style="font-size: 0.82rem; line-height: 1.6;">
                    💡 <strong>Rekomendasi Rantai Pasok:</strong> 
                    @if($country->risk_level == 'High')
                        Negara ini memiliki tingkat kerentanan tinggi. Sangat disarankan untuk memantau cuaca ekstrem secara ketat dan melakukan diversifikasi rute pelabuhan logistik guna menghindari kemacetan suplai.
                    @elseif($country->risk_level == 'Medium')
                        Stabilitas rantai pasok berada dalam tingkat menengah. Operasional pengiriman disarankan berjalan dengan pengawasan rutin terhadap fluktuasi ekonomi mikro (inflasi) dan perubahan iklim lokal.
                    @else
                        Wilayah ini menunjukkan kestabilan tinggi untuk rantai pasok global. Jalur pelabuhan dan kegiatan operasional logistik direkomendasikan berjalan penuh sebagai gerbang distribusi perdagangan prioritas.
                    @endif
                </p>
            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Mini Map Setup for Country Details
    var lat = {{ $country->latitude ?? 20 }};
    var lng = {{ $country->longitude ?? 0 }};
    var countryName = '{{ $country->country_name }}';
    var riskColor = '{{ $color }}';

    var miniMap = L.map('countryMiniMap', {
        zoomControl: false,
        attributionControl: false
    }).setView([lat, lng], 5);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 20
    }).addTo(miniMap);

    var markerHtml = `
        <div style="
            background-color: ${riskColor}; 
            width: 16px; 
            height: 16px; 
            border-radius: 50%; 
            border: 3px solid white; 
            box-shadow: 0 0 6px rgba(0,0,0,0.5);
        "></div>
    `;

    var customIcon = L.divIcon({
        html: markerHtml,
        className: 'custom-div-icon',
        iconSize: [16, 16],
        iconAnchor: [8, 8]
    });

    L.marker([lat, lng], { icon: customIcon })
        .addTo(miniMap)
        .bindPopup(`<strong>${countryName}</strong><br>Status: {{ $country->risk_level }}`)
        .openPopup();
});
</script>
@endpush