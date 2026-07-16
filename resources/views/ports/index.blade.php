@extends('layouts.app')

@section('title', 'Dashboard Lokasi Pelabuhan Global')

@section('content')
<!-- Leaflet MarkerCluster Stylesheet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<div class="container-fluid">

    <!-- Header & Filter -->
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0">🚢 Port Intelligence Center</h2>
            <p class="text-muted mb-0">Peta & Pemantauan Lokasi Pelabuhan Internasional Realtime Berbasis Data Rantai Pasok</p>
        </div>
        <div class="bg-white p-2 rounded shadow-sm border border-light d-flex align-items-center gap-2">
            <label for="country_code" class="fw-semibold text-secondary mb-0 text-nowrap">Filter Negara:</label>
            <form action="{{ route('pelabuhan.index') }}" method="GET" id="countryFilterForm" class="m-0">
                <select name="country_code" id="country_code" class="form-select form-select-sm border-0 bg-light"
                        style="font-weight: 500;" onchange="this.form.submit()">
                    <option value="">🌍 Semua Negara</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->country_code }}" {{ $selectedCountryCode == $c->country_code ? 'selected' : '' }}>
                            {{ $c->country_name }} ({{ $c->country_code }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                          style="width: 50px; height: 50px; background-color: rgba(2, 132, 199, 0.12); color: #0284c7;">
                        <i class="bi bi-geo-alt-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">Total Pelabuhan</small>
                        <h3 class="fw-bold mb-0 mt-1 text-dark" id="statsTotalPorts">{{ $totalPorts }}</h3>
                        <small class="text-muted">Pelabuhan terdaftar dalam sistem</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                          style="width: 50px; height: 50px; background-color: rgba(16, 185, 129, 0.12); color: #10b981;">
                        <i class="bi bi-flag-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">Negara Tercakup</small>
                        <h3 class="fw-bold mb-0 mt-1 text-dark" id="statsTotalCountries">{{ $totalCountriesWithPorts }}</h3>
                        <small class="text-muted">Negara dengan data pelabuhan</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                          style="width: 50px; height: 50px; background-color: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                        <i class="bi bi-eye-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">Ditampilkan Saat Ini</small>
                        <h3 class="fw-bold mb-0 mt-1 text-dark" id="statsActivePorts">{{ count($ports) }}</h3>
                        <small class="text-muted">{{ $selectedCountryCode ? 'Filter aktif: '.$selectedCountryCode : 'Semua pelabuhan tampil' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout: Map + Sidebar -->
    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
        <div class="card-header bg-white py-3 border-light d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-map-fill text-primary"></i> Peta Interaktif Lokasi Pelabuhan
            </h5>
            <span class="badge rounded-pill fw-semibold px-3 py-1.5" id="headerPortCount"
                  style="background-color: rgba(2, 132, 199, 0.1); color: #0284c7; font-size: 0.72rem; letter-spacing: 0.04em;">
                {{ count($ports) }} PELABUHAN
            </span>
        </div>

        <div class="card-body p-0">
            <div class="row g-0" style="min-height: 580px;">

                <!-- Sidebar: Search, Tabs, List, Details -->
                <div class="col-lg-4 col-md-5 border-end border-light d-flex flex-column" style="max-height: 580px; background-color: #fafbfc;">
                    
                    <!-- Tabs Header -->
                    <div class="p-3 border-bottom border-light bg-white">
                        <ul class="nav nav-pills nav-fill bg-light p-1 rounded" id="portTabs" style="font-size: 0.85rem; font-weight: 500;">
                            <li class="nav-item">
                                <a class="nav-link active py-2 border-0" id="local-tab" href="#" data-tab="local" style="border-radius: 8px;">
                                    🚢 Terdaftar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 border-0" id="global-tab" href="#" data-tab="global" style="border-radius: 8px;">
                                    🌐 Cari Global (OSM)
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Content: Terdaftar -->
                    <div class="tab-pane-custom flex-grow-1 d-flex flex-column overflow-hidden" id="tab-local-content">
                        <!-- Search Box Local -->
                        <div class="p-3 border-bottom border-light bg-white">
                            <div class="position-relative">
                                <input type="text" id="portSearch"
                                       class="form-control pe-4 border-light shadow-none"
                                       placeholder="🔍 Cari pelabuhan lokal..."
                                       style="font-size: 0.85rem; border-radius: 10px; background-color: #f8fafc; height: 38px;">
                                <span id="clearSearch"
                                      class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted"
                                      style="cursor: pointer; display: none; font-size: 0.85rem;">✕</span>
                            </div>
                            <div id="searchResultCount" class="mt-2 text-muted" style="font-size: 0.72rem; font-weight: 500;"></div>
                        </div>

                        <!-- Port List Local -->
                        <div class="overflow-y-auto flex-grow-1" id="portListContainer" style="overflow-y: auto;">
                            <div id="portList">
                                <!-- Rendered dynamically by JS -->
                            </div>
                            <div id="noSearchResults" class="p-4 text-center text-muted" style="display: none;">
                                <i class="bi bi-search fs-2 d-block mb-2 text-secondary"></i>
                                <p class="mb-0" style="font-size: 0.85rem;">Pelabuhan tidak ditemukan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content: Cari Global -->
                    <div class="tab-pane-custom flex-grow-1 d-flex flex-column overflow-hidden d-none" id="tab-global-content">
                        <!-- Search Box Global -->
                        <div class="p-3 border-bottom border-light bg-white">
                            <div class="input-group">
                                <input type="text" id="globalSearchInput"
                                       class="form-control border-light shadow-none"
                                       placeholder="🔍 Nama pelabuhan (cth: Rotterdam)..."
                                       style="font-size: 0.85rem; border-radius: 10px 0 0 10px; background-color: #f8fafc; height: 38px;">
                                <button class="btn btn-primary px-3" id="globalSearchBtn" type="button" style="border-radius: 0 10px 10px 0; font-size: 0.85rem;">Cari</button>
                            </div>
                            <div class="mt-2" style="font-size: 0.7rem; color: #64748b; font-style: italic;">
                                Mencari data real-time langsung ke satelit OpenStreetMap global.
                            </div>
                        </div>

                        <!-- Port List Global -->
                        <div class="overflow-y-auto flex-grow-1" id="globalListContainer" style="overflow-y: auto;">
                            <div id="globalSearchResultList">
                                <div class="p-4 text-center text-muted">
                                    <i class="bi bi-cloud-arrow-down fs-2 d-block mb-2 text-primary"></i>
                                    Masukkan nama pelabuhan di atas untuk melacak secara global.
                                </div>
                            </div>
                            <div id="globalSearchLoading" class="p-4 text-center text-muted" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                                <p class="mb-0" style="font-size: 0.82rem;">Menghubungkan ke API satelit OSM...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Active Port Details Panel (dynamic slide-up) -->
                    <div id="portDetailsPanel" class="border-top border-light bg-white p-3 shadow" style="display: none; max-height: 280px; overflow-y: auto; z-index: 10;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                ⚓ <span id="detailPortName">Nama Pelabuhan</span>
                            </h6>
                            <button type="button" class="btn-close btn-sm" id="closeDetailsBtn" style="font-size: 0.75rem;"></button>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 0.78rem;">
                            <span id="detailCountryFlag"></span> <strong id="detailCountryName">Negara</strong> (<span id="detailCountryCode">CODE</span>) 
                            <br><span class="text-secondary" style="font-size: 0.7rem;">Coords: <span id="detailCoords">0, 0</span></span>
                        </p>

                        <!-- Live Weather Section -->
                        <div class="bg-light p-2.5 rounded mb-3 border border-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-dark" style="font-size: 0.78rem;">⛅ Cuaca Real-time</span>
                                <span class="badge" id="riskBadge" style="font-size: 0.68rem; padding: 3px 8px;">RISIKO LOW</span>
                            </div>
                            <div class="row g-2 text-center">
                                <div class="col-4 border-end border-light">
                                    <small class="text-muted d-block" style="font-size: 0.65rem;">Suhu</small>
                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;" id="weatherTemp">-- °C</span>
                                </div>
                                <div class="col-4 border-end border-light">
                                    <small class="text-muted d-block" style="font-size: 0.65rem;">Kecepatan Angin</small>
                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;" id="weatherWind">-- km/h</span>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block" style="font-size: 0.65rem;">Kondisi</small>
                                    <span class="fw-semibold text-secondary" style="font-size: 0.78rem; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; display: block;" id="weatherDesc">--</span>
                                </div>
                            </div>
                        </div>

                        <!-- Live Vessel Traffic Section -->
                        <div class="mb-1">
                            <div class="fw-bold text-dark mb-1.5 d-flex justify-content-between align-items-center" style="font-size: 0.78rem;">
                                <span>🚢 Lalu Lintas Kapal Terdekat (Simulasi Real-time)</span>
                                <span class="badge bg-success" style="font-size: 0.6rem; animation: pulse 2s infinite;">LIVE FEED</span>
                            </div>
                            <div style="font-size: 0.72rem;" id="vesselList">
                                <!-- Vessels will be generated here -->
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Map -->
                <div class="col-lg-8 col-md-7 position-relative">
                    <div id="portMapFull" style="height: 580px; width: 100%;"></div>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- CSS for animations -->
<style>
    @keyframes pulse {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }
    .port-item:hover {
        background-color: #f1f5f9 !important;
    }
    .port-item.active-item {
        background-color: #eff6ff !important;
        border-left: 4px solid #0284c7 !important;
    }
</style>
@endsection

@push('scripts')
<!-- Leaflet MarkerCluster JS -->
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = '{{ csrf_token() }}';

    // 1. Data Parsing & Setup
    let portsData = @json($ports);
    const allCountries = @json($countries->keyBy('country_code'));

    // 2. Initialize Leaflet Map
    let defaultLat = 20, defaultLng = 0, defaultZoom = 2;
    if (portsData.length > 0) {
        defaultLat = parseFloat(portsData[0].latitude);
        defaultLng = parseFloat(portsData[0].longitude);
        defaultZoom = 4;
    }

    const portMap = L.map('portMapFull').setView([defaultLat, defaultLng], defaultZoom);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 18
    }).addTo(portMap);

    // Custom Icon Generator
    function createPortIcon(isActive = false) {
        const color = isActive ? '#f59e0b' : '#0284c7';
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="36" viewBox="0 0 28 36">
            <path d="M14 0C6.27 0 0 6.27 0 14c0 9.63 14 22 14 22s14-12.37 14-22C28 6.27 21.73 0 14 0z" fill="${color}" stroke="white" stroke-width="2"/>
            <text x="14" y="18" text-anchor="middle" fill="white" font-size="13" font-weight="bold" font-family="Arial">⚓</text>
        </svg>`;
        return L.divIcon({
            html: svg,
            className: '',
            iconSize: [28, 36],
            iconAnchor: [14, 36],
            popupAnchor: [0, -36]
        });
    }

    // 3. Marker Clustering Setup
    const markersGroup = L.markerClusterGroup({
        maxClusterRadius: 40,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true
    });
    
    const markers = {};

    function addPortMarker(port) {
        const countryInfo = allCountries[port.country_code];
        const countryName = countryInfo ? countryInfo.country_name : port.country_code;
        const flagHtml = countryInfo && countryInfo.flag
            ? `<img src="${countryInfo.flag}" alt="${countryName}" style="height:14px; border-radius:2px; border:1px solid #ddd; vertical-align: middle;"> `
            : '';

        const popupContent = `
            <div style="font-family: 'Poppins', sans-serif; min-width: 180px; padding: 4px 0;">
                <div style="font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 4px;">
                    ⚓ ${port.port_name}
                </div>
                <div style="font-size: 0.75rem; color: #475569; margin-bottom: 3px;">
                    ${flagHtml} <strong>${countryName}</strong> (${port.country_code})
                </div>
                <div style="font-size: 0.7rem; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 5px; margin-top: 5px;">
                    <i>🌐</i> ${parseFloat(port.latitude).toFixed(4)}, ${parseFloat(port.longitude).toFixed(4)}
                </div>
            </div>`;

        const marker = L.marker([parseFloat(port.latitude), parseFloat(port.longitude)], {
            icon: createPortIcon(false)
        }).bindPopup(popupContent);

        // Link click to detail panel selection
        marker.on('click', function () {
            selectPort(port, false); // select port but don't refly map since they clicked on the marker itself
        });

        markersGroup.addLayer(marker);
        markers[port.id + '_' + port.port_name] = marker;
    }

    // Load initial markers
    portsData.forEach(port => {
        addPortMarker(port);
    });
    portMap.addLayer(markersGroup);

    // 4. Lazy rendering for Sidebar list
    const portListEl = document.getElementById('portList');
    const noResultsEl = document.getElementById('noSearchResults');
    const searchInput = document.getElementById('portSearch');
    const clearSearchBtn = document.getElementById('clearSearch');
    const resultCountEl = document.getElementById('searchResultCount');

    let activePortId = null;

    function renderLocalPorts(filteredPorts) {
        portListEl.innerHTML = '';
        if (filteredPorts.length === 0) {
            noResultsEl.style.display = 'block';
            return;
        }
        noResultsEl.style.display = 'none';

        // Limit to first 100 ports to prevent page freeze
        const sliceLimit = 100;
        const displayPorts = filteredPorts.slice(0, sliceLimit);

        displayPorts.forEach(port => {
            const countryInfo = allCountries[port.country_code];
            const countryName = countryInfo ? countryInfo.country_name : port.country_code;
            const flagUrl = countryInfo ? countryInfo.flag : null;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `port-item w-100 text-start border-0 px-3 py-2.5 border-bottom border-light d-flex align-items-center gap-2 ${activePortId === port.id ? 'active-item' : ''}`;
            btn.style.background = 'none';
            btn.style.transition = 'background-color 0.15s';
            btn.setAttribute('data-port-id', port.id);

            let iconHtml = flagUrl 
                ? `<img src="${flagUrl}" alt="${port.country_code}" style="width: 22px; height: 14px; object-fit: cover; border-radius: 2px; border: 1px solid #e2e8f0;">`
                : `<i class="bi bi-geo-alt-fill" style="color: #0284c7; font-size: 1rem;"></i>`;

            btn.innerHTML = `
                <div class="flex-shrink-0">${iconHtml}</div>
                <div class="min-w-0 flex-grow-1">
                    <div class="fw-semibold text-dark text-truncate" style="font-size: 0.8rem;">${port.port_name}</div>
                    <div class="text-muted" style="font-size: 0.7rem;">${countryName}</div>
                </div>
            `;

            btn.addEventListener('click', function () {
                selectPort(port, true);
            });

            portListEl.appendChild(btn);
        });

        // Add note if truncated
        if (filteredPorts.length > sliceLimit) {
            const footerNote = document.createElement('div');
            footerNote.className = 'p-2 text-center text-muted border-bottom border-light';
            footerNote.style.fontSize = '0.72rem';
            footerNote.style.fontStyle = 'italic';
            footerNote.textContent = `Menampilkan ${sliceLimit} dari total ${filteredPorts.length} pelabuhan. Gunakan pencarian untuk menyaring detail.`;
            portListEl.appendChild(footerNote);
        }
    }

    // Initial render of local list
    renderLocalPorts(portsData);

    // Local Search filtering
    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        clearSearchBtn.style.display = query.length > 0 ? 'block' : 'none';

        const filtered = portsData.filter(port => {
            const countryInfo = allCountries[port.country_code];
            const countryName = countryInfo ? countryInfo.country_name.toLowerCase() : '';
            return port.port_name.toLowerCase().includes(query) || 
                   port.country_code.toLowerCase().includes(query) ||
                   countryName.includes(query);
        });

        renderLocalPorts(filtered);
        
        if (query.length > 0) {
            resultCountEl.textContent = `${filtered.length} pelabuhan cocok`;
        } else {
            resultCountEl.textContent = '';
        }
    });

    clearSearchBtn.addEventListener('click', function () {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
    });

    // 5. Selecting a Port & Fetching Realtime Data
    const detailsPanel = document.getElementById('portDetailsPanel');
    const detailPortName = document.getElementById('detailPortName');
    const detailCountryFlag = document.getElementById('detailCountryFlag');
    const detailCountryName = document.getElementById('detailCountryName');
    const detailCountryCode = document.getElementById('detailCountryCode');
    const detailCoords = document.getElementById('detailCoords');
    const weatherTemp = document.getElementById('weatherTemp');
    const weatherWind = document.getElementById('weatherWind');
    const weatherDesc = document.getElementById('weatherDesc');
    const riskBadge = document.getElementById('riskBadge');
    const vesselList = document.getElementById('vesselList');

    function selectPort(port, flyMap = true) {
        activePortId = port.id;
        
        // Highlight in sidebar
        document.querySelectorAll('#portList .port-item').forEach(item => {
            if (parseInt(item.getAttribute('data-port-id')) === port.id) {
                item.classList.add('active-item');
            } else {
                item.classList.remove('active-item');
            }
        });

        // Open panel
        detailsPanel.style.display = 'block';

        // Load Basic Details
        const countryInfo = allCountries[port.country_code];
        detailPortName.textContent = port.port_name;
        detailCountryName.textContent = countryInfo ? countryInfo.country_name : port.country_code;
        detailCountryCode.textContent = port.country_code;
        detailCoords.textContent = `${parseFloat(port.latitude).toFixed(4)}, ${parseFloat(port.longitude).toFixed(4)}`;

        if (countryInfo && countryInfo.flag) {
            detailCountryFlag.innerHTML = `<img src="${countryInfo.flag}" alt="${port.country_code}" style="height: 14px; border-radius: 2px; vertical-align: middle;">`;
        } else {
            detailCountryFlag.innerHTML = '🌐';
        }

        // Fly Map if requested
        if (flyMap) {
            portMap.flyTo([parseFloat(port.latitude), parseFloat(port.longitude)], 12, { animate: true, duration: 1.2 });
            
            // Highlight Marker
            const markerKey = port.id + '_' + port.port_name;
            const marker = markers[markerKey];
            if (marker) {
                // If it is in cluster, make sure we open the popup
                setTimeout(() => {
                    marker.openPopup();
                }, 1000);
            }
        }

        // Realtime Weather Call
        weatherTemp.textContent = 'Memuat...';
        weatherWind.textContent = 'Memuat...';
        weatherDesc.textContent = 'Memuat...';
        riskBadge.className = 'badge bg-secondary';
        riskBadge.textContent = 'LOADING...';

        fetch(`https://api.open-meteo.com/v1/forecast?latitude=${port.latitude}&longitude=${port.longitude}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m,precipitation&timezone=auto`)
            .then(res => res.json())
            .then(data => {
                if (data && data.current) {
                    const temp = data.current.temperature_2m;
                    const wind = data.current.wind_speed_10m;
                    const code = data.current.weather_code;
                    const precip = data.current.precipitation || 0.0;
                    
                    weatherTemp.textContent = `${temp} °C`;
                    weatherWind.textContent = `${wind} km/h`;
                    
                    const weatherText = translateWeatherCode(code);
                    weatherDesc.textContent = weatherText;

                    // Calculate Supply Chain Weather Risk Score
                    let riskScore = 20; // base score
                    let riskNotes = [];

                    if (wind > 35) {
                        riskScore += 45;
                        riskNotes.push('Badai/Angin Kencang');
                    } else if (wind > 20) {
                        riskScore += 20;
                        riskNotes.push('Angin Sedang');
                    }

                    if (precip > 5.0) {
                        riskScore += 35;
                        riskNotes.push('Hujan Lebat');
                    } else if (precip > 1.0) {
                        riskScore += 15;
                        riskNotes.push('Hujan Ringan');
                    }

                    if (temp < -5 || temp > 40) {
                        riskScore += 20;
                        riskNotes.push('Suhu Ekstrem');
                    }

                    // Display Risk
                    if (riskScore >= 60) {
                        riskBadge.className = 'badge bg-danger text-white';
                        riskBadge.textContent = 'RISIKO HIGH';
                        riskBadge.title = `Gangguan Operasional Berat: ${riskNotes.join(', ')}`;
                    } else if (riskScore >= 35) {
                        riskBadge.className = 'badge bg-warning text-dark';
                        riskBadge.textContent = 'RISIKO MEDIUM';
                        riskBadge.title = `Operasional Terganggu Ringan: ${riskNotes.join(', ')}`;
                    } else {
                        riskBadge.className = 'badge bg-success text-white';
                        riskBadge.textContent = 'RISIKO LOW';
                        riskBadge.title = 'Operasional Normal & Aman';
                    }
                } else {
                    weatherTemp.textContent = 'Error';
                    weatherWind.textContent = 'Error';
                    weatherDesc.textContent = 'Gagal memuat cuaca';
                }
            })
            .catch(err => {
                weatherTemp.textContent = 'Offline';
                weatherWind.textContent = 'Offline';
                weatherDesc.textContent = 'Koneksi gagal';
            });

        // Simulated Realtime Vessel Feeds
        generateVessels(port.port_name);
    }

    // Weather Helper
    function translateWeatherCode(code) {
        if (code === 0) return 'Cerah';
        if ([1, 2, 3].includes(code)) return 'Berawan';
        if ([45, 48].includes(code)) return 'Berkabut';
        if ([51, 53, 55, 56, 57].includes(code)) return 'Gerimis';
        if ([61, 63, 65, 80, 81, 82].includes(code)) return 'Hujan';
        if ([66, 67].includes(code)) return 'Hujan Dingin';
        if ([71, 73, 75, 77, 85, 86].includes(code)) return 'Salju';
        if ([95, 96, 99].includes(code)) return 'Badai Petir';
        return 'Normal';
    }

    // Vessel Generator
    function generateVessels(portName) {
        vesselList.innerHTML = '';
        const vesselNames = [
            'MV Ocean Express', 'COSCO Rotterdam', 'Ever Legend', 'Maersk Horizon',
            'CMA CGM Triton', 'Pacific Highway', 'MSC Geneva', 'OOCL Hong Kong',
            'NYK Venus', 'Hapag-Lloyd Hamburg', 'Ever Glory', 'Tanjung Star'
        ];
        const statusList = ['Sandar - Loading', 'Sandar - Unloading', 'Labuh Jangkar (Anchored)', 'Manufer Merapat', 'Menunggu Pandu'];
        const types = ['Container Ship', 'Bulk Carrier', 'LNG Carrier', 'General Cargo'];

        // Shuffle arrays helper
        const shuffle = arr => arr.sort(() => 0.5 - Math.random());
        const activeVessels = shuffle(vesselNames).slice(0, 3);

        activeVessels.forEach(vessel => {
            const type = types[Math.floor(Math.random() * types.length)];
            const status = statusList[Math.floor(Math.random() * statusList.length)];
            const speed = status.includes('Sandar') ? '0.0 knots' : `${(Math.random() * 8 + 2).toFixed(1)} knots`;
            const dest = allCountries[Object.keys(allCountries)[Math.floor(Math.random() * Object.keys(allCountries).length)]];
            const destName = dest ? dest.country_name : 'Singapura';

            const item = document.createElement('div');
            item.className = 'border-bottom border-light pb-1.5 mb-1.5';
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-primary">${vessel}</span>
                    <span class="badge bg-light text-dark" style="font-size: 0.62rem;">${type}</span>
                </div>
                <div class="d-flex justify-content-between text-muted" style="font-size: 0.65rem;">
                    <span>Status: <strong>${status}</strong></span>
                    <span>Kec: ${speed}</span>
                </div>
                <div style="font-size: 0.65rem; color: #64748b;">
                    Tujuan: ➔ ${destName}
                </div>
            `;
            vesselList.appendChild(item);
        });
    }

    document.getElementById('closeDetailsBtn').addEventListener('click', function () {
        detailsPanel.style.display = 'none';
        activePortId = null;
        document.querySelectorAll('#portList .port-item').forEach(item => item.classList.remove('active-item'));
    });

    // 6. Tab Switching Logic
    const localTab = document.getElementById('local-tab');
    const globalTab = document.getElementById('global-tab');
    const localContent = document.getElementById('tab-local-content');
    const globalContent = document.getElementById('tab-global-content');

    localTab.addEventListener('click', function (e) {
        e.preventDefault();
        localTab.classList.add('active');
        globalTab.classList.remove('active');
        localContent.classList.remove('d-none');
        globalContent.classList.add('d-none');
    });

    globalTab.addEventListener('click', function (e) {
        e.preventDefault();
        globalTab.classList.add('active');
        localTab.classList.remove('active');
        globalContent.classList.remove('d-none');
        localContent.classList.add('d-none');
    });

    // 7. Global Search (OSM Nominatim) API Call
    const globalInput = document.getElementById('globalSearchInput');
    const globalBtn = document.getElementById('globalSearchBtn');
    const globalResults = document.getElementById('globalSearchResultList');
    const globalLoading = document.getElementById('globalSearchLoading');

    function executeGlobalSearch() {
        const query = globalInput.value.trim();
        if (query.length < 3) {
            alert('Masukkan minimal 3 karakter untuk mencari.');
            return;
        }

        globalResults.innerHTML = '';
        globalLoading.style.display = 'block';

        fetch(`/pelabuhan/search-global?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                globalLoading.style.display = 'none';
                if (data.error) {
                    globalResults.innerHTML = `<div class="alert alert-danger m-3" style="font-size:0.8rem;">${data.error}</div>`;
                    return;
                }

                if (!Array.isArray(data) || data.length === 0) {
                    globalResults.innerHTML = `
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-exclamation-triangle fs-2 d-block mb-2 text-warning"></i>
                            Pelabuhan tidak ditemukan di satelit OSM global. Coba kata kunci lain.
                        </div>`;
                    return;
                }

                data.forEach(item => {
                    const countryInfo = allCountries[item.country_code];
                    const countryName = countryInfo ? countryInfo.country_name : item.country_code;

                    const row = document.createElement('div');
                    row.className = 'p-3 border-bottom border-light bg-white d-flex align-items-start justify-content-between gap-2';
                    row.innerHTML = `
                        <div class="min-w-0">
                            <div class="fw-bold text-dark" style="font-size: 0.82rem;">${item.port_name}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">${countryName} (${item.country_code})</div>
                            <div class="text-secondary text-truncate" style="font-size: 0.65rem;" title="${item.display_name}">${item.display_name}</div>
                        </div>
                        <button class="btn btn-sm btn-outline-success text-nowrap add-global-btn" 
                                style="font-size:0.75rem; border-radius:6px; font-weight:600;"
                                data-name="${item.port_name}"
                                data-code="${item.country_code}"
                                data-lat="${item.latitude}"
                                data-lng="${item.longitude}">
                            + Tambah
                        </button>
                    `;

                    row.querySelector('.add-global-btn').addEventListener('click', function () {
                        const btn = this;
                        btn.disabled = true;
                        btn.textContent = 'Menyimpan...';

                        // Save port to local database
                        fetch('/pelabuhan/store-global', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                port_name: btn.getAttribute('data-name'),
                                country_code: btn.getAttribute('data-code'),
                                latitude: btn.getAttribute('data-lat'),
                                longitude: btn.getAttribute('data-lng')
                            })
                        })
                        .then(res => res.json())
                        .then(resData => {
                            if (resData.success && resData.port) {
                                const newPort = resData.port;
                                
                                // Add to local array
                                portsData.push(newPort);
                                addPortMarker(newPort);

                                // Update sidebar stats
                                document.getElementById('statsTotalPorts').textContent = parseInt(document.getElementById('statsTotalPorts').textContent) + 1;
                                document.getElementById('statsActivePorts').textContent = parseInt(document.getElementById('statsActivePorts').textContent) + 1;
                                document.getElementById('headerPortCount').textContent = `${portsData.length} PELABUHAN`;

                                // Notify success
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: `Pelabuhan ${newPort.port_name} berhasil ditambahkan!`,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                // Switch tab back to local and show/select
                                localTab.click();
                                searchInput.value = newPort.port_name;
                                searchInput.dispatchEvent(new Event('input'));
                                selectPort(newPort, true);
                            } else {
                                alert('Gagal menyimpan pelabuhan.');
                                btn.disabled = false;
                                btn.textContent = '+ Tambah';
                            }
                        })
                        .catch(err => {
                            alert('Gagal menghubungi server.');
                            btn.disabled = false;
                            btn.textContent = '+ Tambah';
                        });
                    });

                    globalResults.appendChild(row);
                });
            })
            .catch(err => {
                globalLoading.style.display = 'none';
                globalResults.innerHTML = `<div class="alert alert-danger m-3" style="font-size:0.8rem;">Gagal melacak: Koneksi terputus.</div>`;
            });
    }

    globalBtn.addEventListener('click', executeGlobalSearch);
    globalInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            executeGlobalSearch();
        }
    });

});
</script>
@endpush