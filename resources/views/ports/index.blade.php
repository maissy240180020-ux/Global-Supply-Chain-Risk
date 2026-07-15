@extends('layouts.app')

@section('title', 'Dashboard Lokasi Pelabuhan Global')

@section('content')

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
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $totalPorts }}</h3>
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
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $totalCountriesWithPorts }}</h3>
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
                        <h3 class="fw-bold mb-0 mt-1 text-dark">{{ count($ports) }}</h3>
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
            <span class="badge rounded-pill fw-semibold px-3 py-1.5"
                  style="background-color: rgba(2, 132, 199, 0.1); color: #0284c7; font-size: 0.72rem; letter-spacing: 0.04em;">
                {{ count($ports) }} PELABUHAN
            </span>
        </div>

        <div class="card-body p-0">
            <div class="row g-0" style="min-height: 520px;">

                <!-- Sidebar: Search + List -->
                <div class="col-lg-3 col-md-4 border-end border-light d-flex flex-column" style="max-height: 520px;">

                    <!-- Search Box -->
                    <div class="p-3 border-bottom border-light">
                        <div class="position-relative">
                            <input type="text" id="portSearch"
                                   class="form-control pe-4 border-light"
                                   placeholder="🔍 Cari pelabuhan..."
                                   style="font-size: 0.88rem; border-radius: 10px; background-color: #f8fafc;">
                            <span id="clearSearch"
                                  class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted"
                                  style="cursor: pointer; display: none; font-size: 0.85rem;">✕</span>
                        </div>
                        <div id="searchResultCount" class="mt-2 text-muted" style="font-size: 0.75rem;"></div>
                    </div>

                    <!-- Port List -->
                    <div class="overflow-y-auto flex-grow-1" id="portListContainer" style="overflow-y: auto;">
                        <div id="portList">
                            @forelse($ports as $port)
                                @php
                                    // Cari nama negara berdasarkan country_code
                                    $countryObj = $countries->firstWhere('country_code', $port->country_code);
                                    $countryName = $countryObj ? $countryObj->country_name : $port->country_code;
                                    $flagUrl = $countryObj ? $countryObj->flag : null;
                                @endphp
                                <button type="button"
                                        class="port-item w-100 text-start border-0 px-3 py-3 border-bottom border-light d-flex align-items-center gap-2"
                                        data-port-name="{{ strtolower($port->port_name) }}"
                                        data-country-name="{{ strtolower($countryName) }}"
                                        data-lat="{{ $port->latitude }}"
                                        data-lng="{{ $port->longitude }}"
                                        data-display-name="{{ $port->port_name }}"
                                        data-country-display="{{ $countryName }}"
                                        data-country-code="{{ $port->country_code }}"
                                        data-flag="{{ $flagUrl }}"
                                        style="background: none; transition: background-color 0.15s;">
                                    <div class="flex-shrink-0">
                                        @if($flagUrl)
                                            <img src="{{ $flagUrl }}" alt="{{ $port->country_code }}"
                                                 style="width: 24px; height: 16px; object-fit: cover; border-radius: 2px; border: 1px solid #e2e8f0;">
                                        @else
                                            <i class="bi bi-geo-alt-fill" style="color: #0284c7; font-size: 1.1rem;"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="fw-semibold text-dark port-name-text" style="font-size: 0.82rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $port->port_name }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $countryName }}</div>
                                    </div>
                                </button>
                            @empty
                                <div class="p-4 text-center text-muted" id="noPortsMessage">
                                    <i class="bi bi-info-circle fs-2 d-block mb-2 text-warning"></i>
                                    Tidak ada pelabuhan yang tersedia.
                                </div>
                            @endforelse
                        </div>
                        <div id="noSearchResults" class="p-4 text-center text-muted" style="display: none;">
                            <i class="bi bi-search fs-2 d-block mb-2 text-secondary"></i>
                            <p class="mb-0" style="font-size: 0.85rem;">Pelabuhan tidak ditemukan.</p>
                        </div>
                    </div>

                </div>

                <!-- Map -->
                <div class="col-lg-9 col-md-8">
                    <div id="portMapFull" style="height: 520px; width: 100%;"></div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // =============================================
    // 1. Init Leaflet Map
    // =============================================
    const portsData = @json($ports);
    const allCountries = @json($countries->keyBy('country_code'));

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

    // Custom port icon (anchor-style)
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

    // =============================================
    // 2. Add all markers
    // =============================================
    const markers = {};

    portsData.forEach(port => {
        const countryInfo = allCountries[port.country_code];
        const countryName = countryInfo ? countryInfo.country_name : port.country_code;
        const flagHtml = countryInfo && countryInfo.flag
            ? `<img src="${countryInfo.flag}" alt="${countryName}" style="height:16px; border-radius:2px; border:1px solid #ddd; vertical-align: middle;"> `
            : '';

        const popupContent = `
            <div style="font-family: 'Poppins', sans-serif; min-width: 180px; padding: 4px 0;">
                <div style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-bottom: 4px;">
                    ⚓ ${port.port_name}
                </div>
                <div style="font-size: 0.78rem; color: #475569; margin-bottom: 3px;">
                    ${flagHtml} <strong>${countryName}</strong> (${port.country_code})
                </div>
                <div style="font-size: 0.72rem; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 5px; margin-top: 5px;">
                    <i>🌐</i> ${parseFloat(port.latitude).toFixed(4)}, ${parseFloat(port.longitude).toFixed(4)}
                </div>
            </div>`;

        const marker = L.marker([parseFloat(port.latitude), parseFloat(port.longitude)], {
            icon: createPortIcon(false)
        }).addTo(portMap).bindPopup(popupContent);

        markers[port.port_name] = marker;
    });

    // =============================================
    // 3. Port List Click — Fly to Map
    // =============================================
    let activeBtn = null;
    let activePortName = null;

    function flyToPort(btn) {
        const lat = parseFloat(btn.getAttribute('data-lat'));
        const lng = parseFloat(btn.getAttribute('data-lng'));
        const name = btn.getAttribute('data-display-name');

        portMap.flyTo([lat, lng], 12, { animate: true, duration: 1.2 });

        // Reset previous active
        if (activeBtn) {
            activeBtn.style.backgroundColor = '';
            activeBtn.style.borderLeft = '';
            if (activePortName && markers[activePortName]) {
                markers[activePortName].setIcon(createPortIcon(false));
            }
        }

        // Set new active
        btn.style.backgroundColor = '#eff6ff';
        btn.style.borderLeft = '3px solid #0284c7';

        if (markers[name]) {
            markers[name].setIcon(createPortIcon(true));
            markers[name].openPopup();
        }

        activeBtn = btn;
        activePortName = name;
    }

    document.querySelectorAll('.port-item').forEach(btn => {
        btn.addEventListener('click', function () {
            flyToPort(this);
        });

        btn.addEventListener('mouseenter', function () {
            if (this !== activeBtn) this.style.backgroundColor = '#f8fafc';
        });
        btn.addEventListener('mouseleave', function () {
            if (this !== activeBtn) this.style.backgroundColor = '';
        });
    });

    // =============================================
    // 4. Search Feature — Instant Filter
    // =============================================
    const searchInput = document.getElementById('portSearch');
    const clearBtn = document.getElementById('clearSearch');
    const resultCount = document.getElementById('searchResultCount');
    const noResults = document.getElementById('noSearchResults');
    const portItems = document.querySelectorAll('.port-item');

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;
        let firstVisible = null;

        portItems.forEach(item => {
            const portName = item.getAttribute('data-port-name');
            const countryName = item.getAttribute('data-country-name');
            const countryCode = item.getAttribute('data-country-code').toLowerCase();

            const matches = portName.includes(query) || countryName.includes(query) || countryCode.includes(query);

            item.style.display = matches ? '' : 'none';
            if (matches) {
                visibleCount++;
                if (!firstVisible) firstVisible = item;
            }
        });

        // Show/hide clear button
        clearBtn.style.display = query.length > 0 ? '' : 'none';

        // Show count
        if (query.length > 0) {
            resultCount.textContent = `${visibleCount} hasil ditemukan`;
        } else {
            resultCount.textContent = '';
        }

        // No results state
        noResults.style.display = visibleCount === 0 && query.length > 0 ? '' : 'none';

        // Auto fly to first result if exact-ish match
        if (firstVisible && query.length >= 3) {
            flyToPort(firstVisible);
        }
    });

    // Clear search
    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
    });

    // Keyboard: Enter key focuses first result
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            const firstVisible = [...portItems].find(item => item.style.display !== 'none');
            if (firstVisible) flyToPort(firstVisible);
        }
    });
});
</script>
@endpush