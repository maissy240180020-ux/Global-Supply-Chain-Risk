@extends('layouts.app')

@section('title', 'Dashboard Pelabuhan Global')

@section('content')
<!-- Leaflet MarkerCluster Stylesheet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<!-- Google Fonts Outfit & JetBrains Mono for a highly premium layout -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<div class="container-fluid" style="font-family: 'Outfit', sans-serif;">

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h2 class="fw-bold text-dark d-flex align-items-center gap-2 m-0" style="font-size: 1.8rem; letter-spacing: -0.02em;">
            <span style="color: #3b82f6;">⚓</span> Dashboard Pelabuhan Global
        </h2>
        <div class="d-flex align-items-center gap-2">
            <!-- Dynamic Realtime Clock -->
            <div class="clock-widget">
                <i class="bi bi-clock-fill text-secondary"></i>
                <span id="liveClock" style="font-family: 'JetBrains Mono', monospace; font-weight: 500;">--.--.--</span>
            </div>
            <!-- Online status marker with pulse animation -->
            <div class="status-widget">
                <span class="status-dot"></span>
                <span class="text-secondary" style="font-size: 0.85rem; font-weight: 500;">Online</span>
            </div>
        </div>
    </div>

    <!-- 4 KPI Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Pelabuhan -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="gradient-border-card kpi-card kpi-total">
                <div class="circle-icon bg-icon-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-anchor" viewBox="0 0 16 16">
                        <path d="M8 1a2.5 2.5 0 0 1 .67 4.915A3 3 0 0 1 11 8.86V10c2.51-.115 4-1.285 4-2.5C15 6.07 12 6 12 6V5s3 .07 3 2.5c0 2-3 3-7 3s-7-1-7-2.5C1 5.07 4 5 4 5v1s-3 .07-3 2.5C1 9.715 2.49 10.885 5 11v-1.14a3.001 3.001 0 0 1 2.33-2.945A2.5 2.5 0 0 1 8 1zm0 1a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="fw-bold text-dark mb-0 lh-1" id="statsTotalPorts" style="font-size: 2rem;">{{ $totalPorts }}</h3>
                    <small class="text-muted fw-medium d-block mt-1">Total Pelabuhan</small>
                </div>
            </div>
        </div>
        <!-- Card 2: Low Risk -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="gradient-border-card kpi-card kpi-low">
                <div class="circle-icon bg-icon-green">
                    <i class="bi bi-shield-check" style="font-size: 1.4rem;"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 lh-1" id="statsLowCongestion" style="font-size: 2rem; color: #10b981;">--</h3>
                    <small class="text-muted fw-medium d-block mt-1">Low Risk</small>
                </div>
            </div>
        </div>
        <!-- Card 3: Medium Risk -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="gradient-border-card kpi-card kpi-medium">
                <div class="circle-icon bg-icon-yellow">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.2rem;"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 lh-1" id="statsMedCongestion" style="font-size: 2rem; color: #d97706;">--</h3>
                    <small class="text-muted fw-medium d-block mt-1">Medium Risk</small>
                </div>
            </div>
        </div>
        <!-- Card 4: High Risk -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="gradient-border-card kpi-card kpi-high">
                <div class="circle-icon bg-icon-red">
                    <i class="bi bi-octagon-fill" style="font-size: 1.2rem;"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 lh-1" id="statsHighCongestion" style="font-size: 2rem; color: #ef4444;">--</h3>
                    <small class="text-muted fw-medium d-block mt-1">High Risk</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="gradient-border-card filter-card p-4 mb-4">
        <div class="row g-3 align-items-end">
            <!-- Cari Pelabuhan -->
            <div class="col-lg-4 col-md-5 col-12">
                <label for="portSearchInput" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Cari Pelabuhan</label>
                <div class="position-relative">
                    <input type="text" id="portSearchInput" class="form-control form-control-lg border-light bg-light" 
                           placeholder="Nama pelabuhan, negara..." 
                           style="border-radius: 12px; font-size: 0.95rem; padding-left: 16px; padding-right: 40px; border: 1px solid #e2e8f0; height: 48px;">
                    <span id="clearSearchBtn" class="position-absolute top-50 end-0 translate-middle-y me-3 text-secondary" style="cursor: pointer; display: none;">✕</span>
                </div>
            </div>
            <!-- Filter Negara -->
            <div class="col-lg-3 col-md-4 col-12">
                <label for="countrySelect" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Filter Negara</label>
                <select id="countrySelect" class="form-select form-select-lg border-light bg-light" 
                        style="border-radius: 12px; font-size: 0.95rem; border: 1px solid #e2e8f0; height: 48px;">
                    <option value="">Semua Negara</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->country_code }}" {{ $selectedCountryCode == $c->country_code ? 'selected' : '' }}>
                            {{ $c->country_name }} ({{ $c->country_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Buttons Group -->
            <div class="col-lg-5 col-md-12 col-12 d-flex gap-2">
                <button type="button" id="triggerSearchBtn" class="btn btn-search flex-grow-1 d-flex align-items-center justify-content-center gap-1 px-2">
                    <i class="bi bi-search"></i> Cari
                </button>
                <button type="button" id="resetMapBtn" class="btn btn-light border flex-grow-1 d-flex align-items-center justify-content-center gap-1 px-2" title="Reset Map View">
                    <i class="bi bi-arrows-fullscreen"></i> Reset
                </button>
                <button type="button" id="refreshDataBtn" class="btn btn-light border flex-grow-1 d-flex align-items-center justify-content-center gap-1 px-2" title="Refresh Data">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Main Section: Map + List -->
    <div class="row g-4">
        <!-- Interactive Map (Left) -->
        <div class="col-lg-8 col-12">
            <div class="gradient-border-card map-card p-0 position-relative" style="min-height: 520px; height: 580px; overflow: hidden; border: 1px solid #f1f5f9;">
                <div id="portMapFull" style="height: 100%; width: 100%; z-index: 1;"></div>
                
                <!-- Map Legend -->
                <div class="map-legend" style="position: absolute; bottom: 20px; left: 20px; background: rgba(255,255,255,0.9); padding: 10px 15px; border-radius: 8px; z-index: 400; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h6 class="fw-bold mb-2" style="font-size: 0.8rem;">Tingkat Risiko Logistik</h6>
                    <div class="d-flex align-items-center gap-2 mb-1"><span style="width: 12px; height: 12px; background: #10b981; border-radius: 50%; display: inline-block;"></span> <span style="font-size: 0.75rem;">Low Risk</span></div>
                    <div class="d-flex align-items-center gap-2 mb-1"><span style="width: 12px; height: 12px; background: #f59e0b; border-radius: 50%; display: inline-block;"></span> <span style="font-size: 0.75rem;">Medium Risk</span></div>
                    <div class="d-flex align-items-center gap-2"><span style="width: 12px; height: 12px; background: #ef4444; border-radius: 50%; display: inline-block;"></span> <span style="font-size: 0.75rem;">High Risk</span></div>
                </div>
                
                <!-- Dynamic Port Details Panel (Glassmorphism Map Overlay) -->
                <div id="portDetailsPanel" class="detail-overlay p-4" style="display: none;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold text-dark m-0" id="detailPortName">Nama Pelabuhan</h5>
                            <span class="capsule-detail m-0 mt-1" id="detailCapsuleText">Negara • WPI</span>
                        </div>
                        <button type="button" class="btn-close btn-sm p-1.5 bg-white shadow-sm rounded-circle" id="closeDetailsBtn"></button>
                    </div>
                    
                    <p class="text-secondary mb-3" style="font-size: 0.78rem;">
                        Koordinat: <strong class="text-dark" id="detailCoords">0, 0</strong>
                    </p>

                    <!-- Weather Info Section -->
                    <div class="p-3 bg-white border border-light rounded-4 mb-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark" style="font-size: 0.8rem;">⛅ Cuaca Real-time</span>
                            <span class="badge" id="riskBadge" style="font-size: 0.7rem; padding: 4px 8px; font-weight: 600;">LOADING</span>
                        </div>
                        <div class="row text-center mt-3 g-1">
                            <div class="col-4 border-end">
                                <small class="text-muted d-block" style="font-size: 0.65rem;">Suhu</small>
                                <span class="fw-bold text-dark" style="font-size: 0.85rem;" id="weatherTemp">-- °C</span>
                            </div>
                            <div class="col-4 border-end">
                                <small class="text-muted d-block" style="font-size: 0.65rem;">Angin</small>
                                <span class="fw-bold text-dark" style="font-size: 0.85rem;" id="weatherWind">-- km/h</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block" style="font-size: 0.65rem;">Kondisi</small>
                                <span class="fw-semibold text-secondary text-truncate d-block" style="font-size: 0.8rem;" id="weatherDesc">--</span>
                            </div>
                        </div>
                    </div>

                    <!-- Simulated Vessel Traffic Section -->
                    <div>
                        <div class="fw-bold text-dark mb-2 d-flex justify-content-between align-items-center" style="font-size: 0.8rem;">
                            <span>🚢 Lalu Lintas Kapal (Live Feed)</span>
                            <span class="badge bg-success" style="font-size: 0.6rem; animation: pulse-live 1.5s infinite; background-color: #10b981 !important;">LIVE</span>
                        </div>
                        <div style="font-size: 0.72rem;" id="vesselList">
                            <!-- Populated dynamically by JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Pelabuhan (Right) -->
        <div class="col-lg-4 col-12">
            <div class="gradient-border-card list-card p-4 d-flex flex-column" style="height: 580px;">
                <!-- Header of the list -->
                <div class="d-flex justify-content-between align-items-center pb-3 border-bottom border-light mb-3">
                    <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                        <i class="bi bi-list-task text-secondary"></i> Daftar Pelabuhan
                    </h5>
                    <span class="badge" id="listPortCountBadge" style="background-color: #dbeafe; color: #2563eb; font-weight: 600; font-size: 0.78rem;">{{ count($ports) }}</span>
                </div>

                <!-- Scrollable list of ports (Table format) -->
                <div class="table-responsive flex-grow-1 pe-1" id="portListWrapper" style="overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem; border-collapse: separate; border-spacing: 0;">
                        <thead class="table-light sticky-top" style="z-index: 2;">
                            <tr>
                                <th scope="col" class="border-0 rounded-start text-secondary fw-semibold px-3" style="width: 50%;">Pelabuhan</th>
                                <th scope="col" class="border-0 text-center text-secondary fw-semibold" style="width: 25%;">Status</th>
                                <th scope="col" class="border-0 rounded-end text-center text-secondary fw-semibold" style="width: 25%;">Risiko</th>
                            </tr>
                        </thead>
                        <tbody id="portList">
                            <!-- Loaded dynamically by JS -->
                        </tbody>
                    </table>
                    <div id="noSearchResults" class="p-4 text-center text-muted" style="display: none;">
                        <i class="bi bi-search fs-2 d-block mb-2 text-secondary"></i>
                        <p class="mb-0" style="font-size: 0.85rem;">Pelabuhan tidak ditemukan.</p>
                    </div>
                </div>

                <!-- Pagination Controls -->
                <div class="border-top border-light pt-3 mt-2 d-flex flex-column align-items-center bg-white" id="paginationWrapper" style="flex-shrink: 0;">
                    <div class="d-flex justify-content-between w-100 px-2 mb-1" style="font-size: 0.9rem; font-weight: 500;">
                        <a href="#" id="prevPageBtn" class="text-decoration-none" style="color: #64748b; cursor: pointer;">&laquo; Previous</a>
                        <a href="#" id="nextPageBtn" class="text-decoration-none" style="color: #2563eb; cursor: pointer;">Next &raquo;</a>
                    </div>
                    <div class="text-secondary text-center" style="font-size: 0.75rem;" id="paginationInfo">
                        Showing 1 to 50 of 3711 results
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap Modal for Sync Pelabuhan Global (OSM) -->
<div class="modal fade" id="globalSyncModal" tabindex="-1" aria-labelledby="globalSyncModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 18px; border: none; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.12);">
            <div class="modal-header border-light bg-light py-3 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="globalSyncModalLabel">
                    🌐 Cari & Tambah Pelabuhan Global
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="input-group mb-3 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                    <input type="text" id="globalSearchInput" class="form-control border-0 bg-light" 
                           placeholder="Nama pelabuhan (cth: Rotterdam, Tanjung Priok)..." 
                           style="height: 48px; font-size: 0.9rem;">
                    <button class="btn btn-dark px-4" id="globalSearchBtn" type="button" style="height: 48px; font-weight: 600;">Cari</button>
                </div>
                <div class="text-secondary mb-3" style="font-size: 0.72rem; font-style: italic;">
                    Menyambungkan secara realtime langsung ke satelit OpenStreetMap global.
                </div>
                
                <!-- Loading indicator -->
                <div id="globalSearchLoading" class="text-center py-4 text-muted" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                    <p class="mb-0" style="font-size: 0.82rem;">Menghubungkan ke API satelit OSM...</p>
                </div>

                <!-- Results list -->
                <div id="globalSearchResultList" class="overflow-y-auto" style="max-height: 280px;">
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-cloud-arrow-down fs-2 d-block mb-2 text-primary"></i>
                        Masukkan nama pelabuhan di atas untuk melacak secara global.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Page CSS styles -->
<style>
    /* Styling for Gradient Border Cards */
    .gradient-border-card {
        position: relative;
        border: none;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .gradient-border-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
    }

    /* KPI Card Style overrides */
    .kpi-card {
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
    }

    /* Left Border Gradients */
    .kpi-total::before {
        background: linear-gradient(to bottom, #3b82f6, #f59e0b, #eab308);
    }
    .kpi-low::before {
        background: linear-gradient(to bottom, #10b981, #3b82f6);
    }
    .kpi-medium::before {
        background: linear-gradient(to bottom, #eab308, #ef4444);
    }
    .kpi-high::before {
        background: linear-gradient(to bottom, #ef4444, #f43f5e);
    }

    .filter-card::before, .map-card::before, .list-card::before {
        background: linear-gradient(to bottom, #f59e0b, #eab308);
    }

    /* Icon circles */
    .circle-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .bg-icon-blue { background-color: rgba(59, 130, 246, 0.08); color: #3b82f6; }
    .bg-icon-green { background-color: rgba(16, 185, 129, 0.08); color: #10b981; }
    .bg-icon-yellow { background-color: rgba(245, 158, 11, 0.08); color: #f59e0b; }
    .bg-icon-red { background-color: rgba(239, 68, 68, 0.08); color: #ef4444; }

    /* Widgets Header */
    .clock-widget, .status-widget {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse-dot 1.8s infinite;
    }

    @keyframes pulse-dot {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    @keyframes pulse-live {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }

    /* Buttons styling */
    .btn-search {
        background-color: #044b36;
        border-color: #044b36;
        color: #fff;
        font-weight: 600;
        border-radius: 12px;
        height: 48px;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .btn-search:hover {
        background-color: #033c2b;
        border-color: #033c2b;
        color: #fff;
    }

    .btn-sync {
        background-color: #172554;
        border-color: #172554;
        color: #fff;
        font-weight: 600;
        border-radius: 12px;
        height: 48px;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .btn-sync:hover {
        background-color: #101c40;
        border-color: #101c40;
        color: #fff;
    }

    /* Map Details Overlay */
    .detail-overlay {
        position: absolute;
        bottom: 20px;
        right: 20px;
        width: 330px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        z-index: 1000;
        max-height: 450px;
        overflow-y: auto;
    }

    /* Port items in the list */
    .port-card-item {
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        background: #fff;
        padding: 14px 16px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .port-card-item:hover {
        transform: translateY(-2px);
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .port-card-item.active-port {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.02);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.06);
    }

    .capsule-detail {
        background-color: #f1f5f9;
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 500;
        border-radius: 20px;
        padding: 3px 10px;
        display: inline-block;
        margin-top: 6px;
        margin-bottom: 6px;
    }

    .badge-low {
        background-color: #d1fae5;
        color: #065f46;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
        display: inline-block;
    }
    .badge-medium {
        background-color: #fef3c7;
        color: #92400e;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
        display: inline-block;
    }
    .badge-high {
        background-color: #fee2e2;
        color: #991b1b;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
        display: inline-block;
    }
    
    /* Scrollbar styling */
    #portListWrapper::-webkit-scrollbar {
        width: 5px;
    }
    #portListWrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    #portListWrapper::-webkit-scrollbar-track {
        background: transparent;
    }
</style>
@endsection

@push('scripts')
<!-- Leaflet MarkerCluster JS -->
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = '{{ csrf_token() }}';

    // 1. Digital Clock
    function updateClock() {
        const now = new Date();
        const pad = num => String(num).padStart(2, '0');
        const timeStr = `${pad(now.getHours())}.${pad(now.getMinutes())}.${pad(now.getSeconds())}`;
        document.getElementById('liveClock').textContent = timeStr;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Data Parsing & Mock Generators
    const rawPorts = @json($ports);
    const allCountries = @json($countries->keyBy('country_code'));

    const countryRegions = {
        'AL': 'Europe', 'ID': 'Asia', 'MY': 'Asia', 'SG': 'Asia', 'TH': 'Asia', 'VN': 'Asia',
        'PH': 'Asia', 'CN': 'Asia', 'JP': 'Asia', 'KR': 'Asia', 'IN': 'Asia', 'US': 'North America',
        'CA': 'North America', 'MX': 'North America', 'GB': 'Europe', 'FR': 'Europe', 'DE': 'Europe',
        'IT': 'Europe', 'ES': 'Europe', 'NL': 'Europe', 'AU': 'Oceania', 'NZ': 'Oceania', 'BR': 'South America',
        'AR': 'South America', 'ZA': 'Africa', 'EG': 'Africa', 'SA': 'Middle East', 'AE': 'Middle East',
        'TR': 'Middle East', 'RU': 'Europe/Asia', 'UA': 'Europe'
    };

    const portTypes = ['Container', 'Bulk', 'Ro-Ro', 'General Cargo', 'Oil Terminal'];
    const cities = ['Metropolis', 'Coastal City', 'Port Town', 'Capital', 'Harbor City', 'Bayside'];

    function getPortRegion(code) {
        if (code === 'AL') return 'Oceania';
        return countryRegions[code] || 'Global';
    }

    function enrichPortsData(data, randomize = false) {
        return data.map(port => {
            const seed = randomize ? Math.floor(Math.random() * 10000) : port.id;
            
            const city = cities[seed % cities.length];
            const portType = portTypes[seed % portTypes.length];
            
            const randVal = (seed * 17) % 100;
            let congestionLevel = 'Normal';
            let operationalStatus = 'Normal';
            let riskLevel = 'Low Risk';

            if (randVal > 85) {
                congestionLevel = 'Sangat Padat';
                operationalStatus = 'Terganggu';
                riskLevel = 'High Risk';
            } else if (randVal > 50) {
                congestionLevel = 'Padat';
                operationalStatus = 'Normal';
                riskLevel = 'Medium Risk';
            }

            const countryInfo = allCountries[port.country_code];
            const countryName = countryInfo ? countryInfo.country_name : port.country_code;

            return {
                ...port,
                city,
                portType,
                operationalStatus,
                congestionLevel,
                riskLevel,
                countryName,
                region: getPortRegion(port.country_code)
            };
        });
    }

    let portsData = enrichPortsData(rawPorts, false);

    // 3. Map Initialization
    let portMap = null;
    let markersGroup = null;
    let routesLayer = null;
    const markers = {};
    const hasLeaflet = typeof L !== 'undefined';
    
    let defaultLat = 20, defaultLng = 0, defaultZoom = 2;

    if (hasLeaflet) {
        try {
            portMap = L.map('portMapFull', { preferCanvas: true }).setView([defaultLat, defaultLng], defaultZoom);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 18
            }).addTo(portMap);

            setTimeout(function() {
                portMap.invalidateSize();
            }, 150);

            if (typeof L.markerClusterGroup !== 'undefined') {
                markersGroup = L.markerClusterGroup({
                    chunkedLoading: true,
                    maxClusterRadius: 50
                });
            } else {
                markersGroup = L.layerGroup();
            }
            routesLayer = L.layerGroup().addTo(portMap);

        } catch (e) {
            console.error("Leaflet map initialization failed:", e);
        }
    }

    // 4. Markers & Popups
    function addPortMarker(port) {
        if (!hasLeaflet || !portMap || !markersGroup) return;
        try {
            const countryInfo = allCountries[port.country_code];
            const flagHtml = countryInfo && countryInfo.flag
                ? `<img src="${countryInfo.flag}" alt="${port.countryName}" style="height:14px; border-radius:2px; border:1px solid #ddd; vertical-align: middle;"> `
                : '';

            let color = '#10b981';
            if (port.riskLevel === 'Medium Risk') color = '#f59e0b';
            if (port.riskLevel === 'High Risk') color = '#ef4444';

            const popupContent = `
                <div style="font-family: 'Outfit', sans-serif; min-width: 220px; padding: 5px;">
                    <h6 style="font-weight: 700; color: #0f172a; margin-bottom: 8px;">⚓ ${port.port_name}</h6>
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                        <tbody>
                            <tr><td class="text-secondary p-0 pb-1" width="40%">Negara</td><td class="p-0 pb-1">${flagHtml} <strong>${port.countryName}</strong></td></tr>
                            <tr><td class="text-secondary p-0 pb-1">Kota</td><td class="p-0 pb-1">${port.city}</td></tr>
                            <tr><td class="text-secondary p-0 pb-1">Koordinat</td><td class="p-0 pb-1">${parseFloat(port.latitude).toFixed(4)}, ${parseFloat(port.longitude).toFixed(4)}</td></tr>
                            <tr><td class="text-secondary p-0 pb-1">Jenis</td><td class="p-0 pb-1">${port.portType}</td></tr>
                            <tr><td class="text-secondary p-0 pb-1">Operasional</td><td class="p-0 pb-1">${port.operationalStatus}</td></tr>
                            <tr><td class="text-secondary p-0 pb-1">Kepadatan</td><td class="p-0 pb-1">${port.congestionLevel}</td></tr>
                            <tr><td class="text-secondary p-0 pb-1">Risiko</td><td class="p-0 pb-1"><span style="color: ${color}; font-weight: 700;">${port.riskLevel}</span></td></tr>
                        </tbody>
                    </table>
                </div>`;

            const marker = L.circleMarker([parseFloat(port.latitude), parseFloat(port.longitude)], {
                radius: 7,
                fillColor: color,
                color: '#ffffff',
                weight: 1.5,
                opacity: 1.0,
                fillOpacity: 0.95
            }).bindPopup(popupContent);

            marker.on('click', function () {
                selectPort(port, false);
            });

            markersGroup.addLayer(marker);
            markers[port.id + '_' + port.port_name] = marker;
        } catch (err) {
            console.error("Error adding port marker:", err);
        }
    }

    function reloadMapMarkers(filteredPorts) {
        if (!hasLeaflet || !portMap || !markersGroup) return;
        try {
            markersGroup.clearLayers();
            Object.keys(markers).forEach(key => delete markers[key]);
            
            filteredPorts.forEach(port => {
                addPortMarker(port);
            });
            
            portMap.addLayer(markersGroup);
            
            if (filteredPorts.length > 0 && filteredPorts.length < rawPorts.length) {
                const group = new L.featureGroup(filteredPorts.map(p => L.circleMarker([p.latitude, p.longitude])));
                portMap.fitBounds(group.getBounds().pad(0.1));
            } else if (filteredPorts.length === 0) {
                portMap.setView([defaultLat, defaultLng], defaultZoom);
            }
        } catch (err) {
            console.error("Error reloading map markers:", err);
        }
    }

    // 5. Global Shipping Routes
    function drawShippingRoutes() {
        if (!hasLeaflet || !routesLayer || portsData.length < 5) return;
        routesLayer.clearLayers();
        
        const hubs = portsData.slice(0, 20); 
        
        for (let i = 0; i < hubs.length - 1; i++) {
            for (let j = i + 1; j < hubs.length; j++) {
                if ((i + j) % 6 === 0) {
                    const latlngs = [
                        [hubs[i].latitude, hubs[i].longitude],
                        [hubs[j].latitude, hubs[j].longitude]
                    ];
                    
                    L.polyline(latlngs, {
                        color: '#3b82f6',
                        weight: 1.5,
                        opacity: 0.4,
                        dashArray: '5, 10'
                    }).addTo(routesLayer);
                }
            }
        }
    }

    // 6. Sidebar Table & Pagination
    let currentPage = 1;
    const pageSize = 50;
    let currentFilteredPorts = [];
    const portListEl = document.getElementById('portList');
    const noResultsEl = document.getElementById('noSearchResults');
    const listPortCountBadge = document.getElementById('listPortCountBadge');

    function updateKPIStats(filtered) {
        const total = filtered.length;
        const low = filtered.filter(p => p.riskLevel === 'Low Risk').length;
        const med = filtered.filter(p => p.riskLevel === 'Medium Risk').length;
        const high = filtered.filter(p => p.riskLevel === 'High Risk').length;

        document.getElementById('statsTotalPorts').textContent = total;
        document.getElementById('statsLowCongestion').textContent = low;
        document.getElementById('statsMedCongestion').textContent = med;
        document.getElementById('statsHighCongestion').textContent = high;
        listPortCountBadge.textContent = total;
    }

    function renderLocalPorts(filteredPorts) {
        currentFilteredPorts = filteredPorts;
        portListEl.innerHTML = '';
        
        const paginationWrapper = document.getElementById('paginationWrapper');
        
        if (filteredPorts.length === 0) {
            noResultsEl.style.display = 'block';
            if (paginationWrapper) paginationWrapper.style.setProperty('display', 'none', 'important');
            return;
        }
        noResultsEl.style.display = 'none';
        if (paginationWrapper) paginationWrapper.style.setProperty('display', 'flex', 'important');

        const totalItems = filteredPorts.length;
        const totalPages = Math.ceil(totalItems / pageSize);
        
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = Math.min(startIndex + pageSize, totalItems);
        const displayPorts = filteredPorts.slice(startIndex, endIndex);

        displayPorts.forEach(port => {
            const tr = document.createElement('tr');
            tr.style.cursor = 'pointer';
            
            let badgeClass = 'badge-low';
            if (port.riskLevel === 'Medium Risk') badgeClass = 'badge-medium';
            if (port.riskLevel === 'High Risk') badgeClass = 'badge-high';

            tr.innerHTML = `
                <td class="px-3">
                    <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">${port.port_name}</div>
                    <div class="text-muted text-truncate" style="font-size: 0.7rem; max-width: 180px;">${port.countryName}</div>
                </td>
                <td class="text-center text-muted" style="font-size: 0.75rem;">
                    ${port.operationalStatus}
                </td>
                <td class="text-center">
                    <span class="${badgeClass}">${port.riskLevel}</span>
                </td>
            `;

            tr.addEventListener('click', function () {
                selectPort(port, true);
            });

            portListEl.appendChild(tr);
        });

        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');
        const infoText = document.getElementById('paginationInfo');

        if (infoText) infoText.textContent = `Showing ${totalItems === 0 ? 0 : startIndex + 1} to ${endIndex} of ${totalItems} results`;
        if (prevBtn) {
            prevBtn.style.color = currentPage === 1 ? '#cbd5e1' : '#2563eb';
            prevBtn.style.pointerEvents = currentPage === 1 ? 'none' : 'auto';
        }
        if (nextBtn) {
            nextBtn.style.color = (currentPage === totalPages || totalPages === 0) ? '#cbd5e1' : '#2563eb';
            nextBtn.style.pointerEvents = (currentPage === totalPages || totalPages === 0) ? 'none' : 'auto';
        }
    }

    // 7. Filtering Logic
    const searchInput = document.getElementById('portSearchInput');
    const countrySelect = document.getElementById('countrySelect');
    
    function handleFiltering() {
        const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const selectedCountry = countrySelect ? countrySelect.value : '';
        
        const filtered = portsData.filter(port => {
            const matchesSearch = port.port_name.toLowerCase().includes(query) || 
                                  port.country_code.toLowerCase().includes(query) ||
                                  port.countryName.toLowerCase().includes(query);
            
            const matchesCountry = selectedCountry === '' || port.country_code === selectedCountry;

            return matchesSearch && matchesCountry;
        });

        currentPage = 1;
        renderLocalPorts(filtered);
        updateKPIStats(filtered);
        reloadMapMarkers(filtered);
    }

    if (searchInput) searchInput.addEventListener('input', handleFiltering);
    if (countrySelect) countrySelect.addEventListener('change', handleFiltering);
    const triggerSearchBtn = document.getElementById('triggerSearchBtn');
    if (triggerSearchBtn) triggerSearchBtn.addEventListener('click', handleFiltering);

    // 8. Select Port (Zoom)
    function selectPort(port, flyMap = true) {
        if (flyMap && hasLeaflet && portMap) {
            try {
                portMap.flyTo([parseFloat(port.latitude), parseFloat(port.longitude)], 12, { animate: true, duration: 1.2 });
                const markerKey = port.id + '_' + port.port_name;
                const marker = markers[markerKey];
                if (marker) {
                    setTimeout(() => {
                        marker.openPopup();
                    }, 1000);
                }
            } catch (err) {
                console.error("Map flyTo error:", err);
            }
        }
    }

    // 9. Extra Features
    const resetMapBtn = document.getElementById('resetMapBtn');
    if (resetMapBtn) {
        resetMapBtn.addEventListener('click', () => {
            if (portMap) {
                portMap.setView([defaultLat, defaultLng], defaultZoom);
                portMap.closePopup();
            }
        });
    }

    const refreshDataBtn = document.getElementById('refreshDataBtn');
    if (refreshDataBtn) {
        refreshDataBtn.addEventListener('click', () => {
            portsData = enrichPortsData(rawPorts, true);
            handleFiltering();
            drawShippingRoutes();
            
            Swal.fire({
                icon: 'success',
                title: 'Data Refreshed',
                text: 'Port operations and risk metrics updated.',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    }

    const prevBtn = document.getElementById('prevPageBtn');
    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                renderLocalPorts(currentFilteredPorts);
                document.getElementById('portListWrapper').scrollTop = 0;
            }
        });
    }

    const nextBtn = document.getElementById('nextPageBtn');
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const totalPages = Math.ceil(currentFilteredPorts.length / pageSize);
            if (currentPage < totalPages) {
                currentPage++;
                renderLocalPorts(currentFilteredPorts);
                document.getElementById('portListWrapper').scrollTop = 0;
            }
        });
    }

    // Initialize View
    updateKPIStats(portsData);
    renderLocalPorts(portsData);
    reloadMapMarkers(portsData);
    drawShippingRoutes();

});
</script>
@endpush