@extends('layouts.app')

@section('title','Data Negara | SIMRPG')

@section('content')

<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        🌍 Data Negara
    </h2>

    <!-- Search & Filters Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <!-- Modern Search Input with integrated icon and Debounced AJAX -->
            <div class="position-relative" style="width: 320px;">
                <input type="text" id="countrySearch" class="form-control ps-5 border-light shadow-none" 
                       placeholder="Cari nama negara, ibukota, atau mata uang..."
                       value="{{ request('search') }}"
                       style="font-size: 0.85rem; border-radius: 10px; background-color: #f8fafc; height: 40px;">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.9rem;"></i>
            </div>
            
            <select id="filterRegion" class="form-select border-light shadow-none" style="width: 180px; font-size: 0.85rem; border-radius: 10px; background-color: #f8fafc; height: 40px;">
                <option value="">Semua Wilayah</option>
                @foreach($regions as $r)
                    <option value="{{ $r }}">{{ $r }}</option>
                @endforeach
            </select>

            <select id="filterRisk" class="form-select border-light shadow-none" style="width: 180px; font-size: 0.85rem; border-radius: 10px; background-color: #f8fafc; height: 40px;">
                <option value="">Semua Risiko</option>
                <option value="High">Tinggi (High Risk)</option>
                <option value="Medium">Sedang (Medium Risk)</option>
                <option value="Low">Rendah (Low Risk)</option>
            </select>
        </div>
    </div>

    <!-- Country KPI Cards -->
    <style>
        .kpi-card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .kpi-card-hover:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.06) !important; }
        .table-row-hover { transition: background-color 0.2s ease; }
        .table-row-hover:hover { background-color: #f1f5f9 !important; cursor: pointer; }
        .highlight-row { animation: highlightFlash 3s ease; }
        @keyframes highlightFlash { 0% { background-color: #fef08a; } 100% { background-color: transparent; } }
    </style>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm kpi-card-hover" style="background-color: #e0f2fe; border-radius: 16px;">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Negara</h6>
                    <h2 class="fw-bold text-primary mb-0" id="cardTotalCount">
                        {{ $countries->total() }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm kpi-card-hover" style="background-color: #ffe4e6; border-radius: 16px;">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Risiko Tinggi</h6>
                    <h2 class="fw-bold text-danger mb-0">
                        {{ $riskCounts['High'] }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm kpi-card-hover" style="background-color: #fef3c7; border-radius: 16px;">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Rata-rata Skor Risiko</h6>
                    <h2 class="fw-bold text-warning mb-0">
                        {{ number_format(\App\Models\Country::avg('risk_score'), 1) }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm kpi-card-hover" style="background-color: #dcfce7; border-radius: 16px;">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total GDP</h6>
                    <h5 class="fw-bold text-success mb-0" style="line-height: 1.8;">
                        {{ number_format(\App\Models\Country::sum('gdp') / 1e12, 2) }} Triliun USD
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom-0 pb-0 pt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0">🗺️ Peta Interaktif Risiko Negara Global</h6>
            <div class="d-flex gap-2">
                <input type="text" id="mapSearch" list="mapCountryList" class="form-control form-control-sm" placeholder="Cari negara di peta..." style="width: 220px; font-size:0.8rem;">
                <datalist id="mapCountryList"></datalist>
                <button id="resetViewBtn" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" title="Reset View"><i class="bi bi-arrows-fullscreen"></i> Reset</button>
            </div>
        </div>
        <div class="card-body">
            <div id="countriesMap" class="rounded border" style="height: 550px; z-index:1;"></div>
        </div>
    </div>

    <!-- Country Table -->
    <div class="card dashboard-card">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;"><i class="bi bi-star-fill text-warning"></i></th>
                        <th>No</th>
                        <th>Bendera</th>
                        <th>Nama Negara</th>
                        <th>Skor Risiko</th>
                        <th>Mata Uang</th>
                        <th>Cuaca</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="countriesTableBody">
                    @include('countries.partials.rows', ['countries' => $countries])
                </tbody>
            </table>

            <!-- Scroll Loading Spinner & End Status -->
            <div class="mt-4 pb-2 border-top border-light pt-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small id="paginationInfo" class="text-muted">
                        Menampilkan 1 sampai <span id="currentCount">{{ $countries->lastItem() ?? 0 }}</span> dari <span id="totalCount">{{ $countries->total() }}</span> data.
                    </small>
                    
                    <div id="scrollStatusContainer">
                        <!-- Loader Spinner -->
                        <div id="scrollLoader" class="d-none">
                            <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="text-muted ms-2" style="font-size: 0.82rem;">Memuat data...</span>
                        </div>
                        
                        <!-- End Indicator -->
                        <div id="scrollEnd" class="text-muted" style="font-size: 0.82rem; font-weight: 500;">
                            @if($countries->hasMorePages())
                                Scroll kebawah untuk memuat negara lainnya.
                            @else
                                ✨ Menampilkan seluruh data negara.
                            @endif
                        </div>
                    </div>
                </div>
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
    // 1. Initialize Map
    var map = L.map('countriesMap').setView([20, 0], 2);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    var dbCountries = @json($mapCountries);
    
    // Create a dictionary for quick lookup by Country Name
    var countryDict = {};
    dbCountries.forEach(function(c) {
        countryDict[c.country_name.toLowerCase()] = c;
        if(c.country_code) countryDict[c.country_code.toLowerCase()] = c; // just in case
    });

    var nameAliases = {
        'the bahamas': 'bahamas',
        'united states of america': 'united states',
        'united republic of tanzania': 'tanzania',
        'republic of serbia': 'serbia',
        'russian federation': 'russia',
        'democratic republic of the congo': 'congo',
        'republic of the congo': 'congo',
        'ivory coast': 'côte d\'ivoire',
        'côte d\'ivoire': 'ivory coast',
        'south korea': 'korea, south',
        'north korea': 'korea, north',
        'vietnam': 'viet nam'
    };

    // Populate Datalist
    var datalist = document.getElementById('mapCountryList');
    dbCountries.forEach(function(c) {
        var opt = document.createElement('option');
        opt.value = c.country_name;
        datalist.appendChild(opt);
    });

    var geojsonLayer;
    
    // Legend Control
    var legend = L.control({position: 'bottomleft'});
    var riskCounts = @json($riskCounts);
    legend.onAdd = function (map) {
        var div = L.DomUtil.create('div', 'info legend rounded shadow-sm bg-white p-2');
        div.innerHTML = `
            <h6 class="fw-bold mb-2" style="font-size:12px; margin-bottom:10px !important;">Skor Risiko</h6>
            <div style="font-size:11px; margin-bottom:5px;" class="d-flex justify-content-between align-items-center">
                <span><i style="background:#ef4444; width:14px; height:14px; display:inline-block; border-radius:3px; margin-right:6px; vertical-align:middle;"></i> High Risk</span>
                <span class="fw-bold text-muted ms-3">${riskCounts.High}</span>
            </div>
            <div style="font-size:11px; margin-bottom:5px;" class="d-flex justify-content-between align-items-center">
                <span><i style="background:#f59e0b; width:14px; height:14px; display:inline-block; border-radius:3px; margin-right:6px; vertical-align:middle;"></i> Medium Risk</span>
                <span class="fw-bold text-muted ms-3">${riskCounts.Medium}</span>
            </div>
            <div style="font-size:11px; margin-bottom:5px;" class="d-flex justify-content-between align-items-center">
                <span><i style="background:#10b981; width:14px; height:14px; display:inline-block; border-radius:3px; margin-right:6px; vertical-align:middle;"></i> Low Risk</span>
                <span class="fw-bold text-muted ms-3">${riskCounts.Low}</span>
            </div>
        `;
        return div;
    };
    legend.addTo(map);

    function style(feature) {
        var name = feature.properties.name ? feature.properties.name.toLowerCase() : '';
        if (nameAliases[name]) name = nameAliases[name];
        
        var countryInfo = countryDict[name] || countryDict[feature.properties.sovereignt?.toLowerCase()];
        
        var fillColor = '#cbd5e1'; // default gray for no data
        if (countryInfo) {
            if (countryInfo.risk_level === 'High') fillColor = '#ef4444';
            else if (countryInfo.risk_level === 'Medium') fillColor = '#f59e0b';
            else if (countryInfo.risk_level === 'Low') fillColor = '#10b981';
        }

        return {
            fillColor: fillColor,
            weight: 1.5,
            opacity: 1,
            color: '#ffffff',
            dashArray: '',
            fillOpacity: 0.7
        };
    }

    function highlightFeature(e) {
        var layer = e.target;
        layer.setStyle({
            weight: 2,
            color: '#1e293b',
            dashArray: '',
            fillOpacity: 0.85
        });
        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
            layer.bringToFront();
        }
    }

    function resetHighlight(e) {
        if (geojsonLayer) {
            geojsonLayer.resetStyle(e.target);
        }
    }

    function zoomToFeature(e) {
        map.fitBounds(e.target.getBounds(), {padding: [50, 50]});
    }

    function onEachFeature(feature, layer) {
        var name = feature.properties.name ? feature.properties.name.toLowerCase() : '';
        if (nameAliases[name]) name = nameAliases[name];
        
        var countryInfo = countryDict[name] || countryDict[feature.properties.sovereignt?.toLowerCase()];

        // Tooltip (Hover)
        if (countryInfo) {
            var color = '#10b981';
            var idStatus = 'Rendah';
            if (countryInfo.risk_level === 'High') { color = '#ef4444'; idStatus = 'Tinggi'; }
            else if (countryInfo.risk_level === 'Medium') { color = '#f59e0b'; idStatus = 'Sedang'; }

            var gdp = countryInfo.gdp ? (countryInfo.gdp / 1e12).toFixed(2) + ' Triliun USD' : '-';
            var weather = countryInfo.weather ? countryInfo.weather : '-';
            
            var tooltipContent = `
                <div style="font-family: inherit; font-size: 13px; line-height: 1.5; min-width: 220px;">
                    <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                        <img src="${countryInfo.flag}" width="26" style="border-radius: 2px; border: 1px solid #ddd;">
                        <strong style="font-size:14px; margin-bottom:0;">${countryInfo.country_name}</strong>
                    </div>
                    <table style="width: 100%; margin-bottom: 8px; font-size: 11px;">
                        <tr><td style="color:#666; padding-bottom:3px;">Skor Risiko</td><td class="text-end fw-bold" style="color: ${color};">${Math.round(countryInfo.risk_score)} (${idStatus})</td></tr>
                        <tr><td style="color:#666; padding-bottom:3px;">Total GDP</td><td class="text-end fw-bold">${gdp}</td></tr>
                        <tr><td style="color:#666; padding-bottom:3px;">Cuaca</td><td class="text-end fw-bold">${weather}</td></tr>
                    </table>
                    <div class="text-center mt-2">
                        <a href="/countries/${countryInfo.id}" class="btn btn-sm btn-primary text-white w-100" style="font-size: 11px; border-radius:6px;">Lihat Detail</a>
                    </div>
                </div>
            `;
            layer.bindTooltip(tooltipContent, {direction: 'top', sticky: true, interactive: true, className: 'shadow-sm border-0 p-2 bg-white rounded-3'});
        } else {
             layer.bindTooltip(`<div class="p-2 text-center text-muted" style="font-size:12px;">Data untuk <strong>${feature.properties.name}</strong> belum tersedia.</div>`, {direction: 'top', sticky: true});
        }

        layer.on({
            mouseover: highlightFeature,
            mouseout: resetHighlight,
            click: function(e) {
                zoomToFeature(e);
                if (countryInfo) {
                    var row = document.getElementById('country-row-' + countryInfo.id);
                    if (row) {
                        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        row.classList.remove('highlight-row');
                        void row.offsetWidth; // trigger reflow
                        row.classList.add('highlight-row');
                    }
                }
            }
        });
    }

    // Load GeoJSON
    map.spin && map.spin(true); // if spin plugin exists
    fetch('/geojson/world.geojson')
        .then(res => res.json())
        .then(data => {
            geojsonLayer = L.geoJSON(data, {
                style: style,
                onEachFeature: onEachFeature
            }).addTo(map);
            if(map.spin) map.spin(false);
        })
        .catch(err => {
            console.error("Error loading GeoJSON:", err);
            if(map.spin) map.spin(false);
            Swal.fire('Error', 'Gagal memuat peta batas wilayah negara.', 'error');
        });

    // Reset View Button
    document.getElementById('resetViewBtn').addEventListener('click', function(e) {
        e.preventDefault();
        map.setView([20, 0], 2);
    });

    // Map Search
    var mapSearch = document.getElementById('mapSearch');
    mapSearch.addEventListener('input', function(e) {
        var query = this.value.toLowerCase().trim();
        if(!query) return;
        
        var found = false;
        
        if (geojsonLayer) {
            geojsonLayer.eachLayer(function(layer) {
                var name = layer.feature.properties.name ? layer.feature.properties.name.toLowerCase() : '';
                if (nameAliases[name]) name = nameAliases[name];

                if (name === query || (countryDict[name] && countryDict[name].country_name.toLowerCase() === query)) {
                    if (!found) {
                        map.fitBounds(layer.getBounds(), {padding: [50, 50]});
                        layer.openPopup();
                        found = true;
                    }
                }
            });
        }
    });

    mapSearch.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var query = this.value.toLowerCase().trim();
            if(!query) return;
            
            var found = false;
            
            if (geojsonLayer) {
                geojsonLayer.eachLayer(function(layer) {
                    var name = layer.feature.properties.name ? layer.feature.properties.name.toLowerCase() : '';
                    if (nameAliases[name]) name = nameAliases[name];

                    if (name.includes(query) || (countryDict[name] && countryDict[name].country_name.toLowerCase().includes(query))) {
                        if (!found) {
                            map.fitBounds(layer.getBounds(), {padding: [50, 50]});
                            layer.openPopup();
                            found = true;
                        }
                    }
                });
            }
            if (!found) {
                Swal.fire({icon: 'info', title: 'Pencarian Peta', text: 'Negara tidak ditemukan di peta.', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false});
            }
        }
    });

    // 2. Infinite Scroll Logic
    let nextPageUrl = '{{ $countries->nextPageUrl() }}';
    let isLoading = false;
    
    const tableBody = document.getElementById('countriesTableBody');
    const scrollLoader = document.getElementById('scrollLoader');
    const scrollEnd = document.getElementById('scrollEnd');
    const searchInput = document.getElementById('countrySearch');
    const currentCountSpan = document.getElementById('currentCount');
    const totalCountSpan = document.getElementById('totalCount');
    const cardTotalCount = document.getElementById('cardTotalCount');

    window.addEventListener('scroll', function() {
        if (!nextPageUrl || isLoading) return;

        // Trigger load when scrolled near bottom (120px threshold)
        if ((window.innerHeight + window.scrollY) >= document.documentElement.offsetHeight - 120) {
            loadMoreCountries();
        }
    });

    function loadMoreCountries() {
        isLoading = true;
        scrollLoader.classList.remove('d-none');
        scrollEnd.classList.add('d-none');
        
        let urlObj = new URL(nextPageUrl, window.location.origin);
        urlObj.searchParams.set('region', document.getElementById('filterRegion').value);
        urlObj.searchParams.set('risk_level', document.getElementById('filterRisk').value);

        fetch(urlObj.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            isLoading = false;
            scrollLoader.classList.add('d-none');

            if (data.html) {
                tableBody.insertAdjacentHTML('beforeend', data.html);
            }

            nextPageUrl = data.next_page;
            
            // Update UI count
            currentCountSpan.textContent = data.current_count;
            totalCountSpan.textContent = data.total;

            if (nextPageUrl) {
                scrollEnd.textContent = "Scroll kebawah untuk memuat negara lainnya.";
                scrollEnd.classList.remove('d-none');
            } else {
                scrollEnd.textContent = "✨ Menampilkan seluruh data negara.";
                scrollEnd.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error loading more countries:', error);
            isLoading = false;
            scrollLoader.classList.add('d-none');
        });
    }

    // 3. Instant Debounced AJAX Search
    let searchTimeout;
    const filterEls = [searchInput, document.getElementById('filterRegion'), document.getElementById('filterRisk')];
    
    filterEls.forEach(el => {
        el.addEventListener(el === searchInput ? 'input' : 'change', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(searchInput.value);
            }, 300);
        });
    });

    function performSearch(query) {
        isLoading = true;
        scrollLoader.classList.remove('d-none');
        scrollEnd.classList.add('d-none');

        const region = document.getElementById('filterRegion').value;
        const risk = document.getElementById('filterRisk').value;
        const searchUrl = `/countries?search=${encodeURIComponent(query)}&region=${encodeURIComponent(region)}&risk_level=${encodeURIComponent(risk)}`;

        fetch(searchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            isLoading = false;
            scrollLoader.classList.add('d-none');

            tableBody.innerHTML = data.html;
            nextPageUrl = data.next_page;

            // Update UI statistics
            currentCountSpan.textContent = data.current_count;
            totalCountSpan.textContent = data.total;
            if (cardTotalCount) cardTotalCount.textContent = data.total;

            if (nextPageUrl) {
                scrollEnd.textContent = "Scroll kebawah untuk memuat negara lainnya.";
                scrollEnd.classList.remove('d-none');
            } else {
                scrollEnd.textContent = data.total > 0 ? "✨ Menampilkan seluruh hasil pencarian." : "";
                scrollEnd.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error searching countries:', error);
            isLoading = false;
            scrollLoader.classList.add('d-none');
        });
    }

    // 4. AJAX Watchlist Toggle using Event Delegation (Supports Infinite Scroll Appended Rows)
    tableBody.addEventListener('click', function(e) {
        const btn = e.target.closest('.toggle-favorite-btn');
        if (!btn) return;

        e.preventDefault();
        const countryId = btn.getAttribute('data-country-id');
        const starIcon = btn.querySelector('.favorite-star-icon');
        
        fetch(`/watchlist/toggle/${countryId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.is_favorite) {
                    starIcon.classList.remove('bi-star', 'text-muted');
                    starIcon.classList.add('bi-star-fill', 'text-warning');
                    
                    Swal.fire({
                        title: 'Favorit!',
                        text: `${data.country_name} ditambahkan ke daftar pantauan.`,
                        icon: 'success',
                        timer: 1200,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    starIcon.classList.remove('bi-star-fill', 'text-warning');
                    starIcon.classList.add('bi-star', 'text-muted');
                    
                    Swal.fire({
                        title: 'Dihapus!',
                        text: `${data.country_name} dihapus dari daftar pantauan.`,
                        icon: 'info',
                        timer: 1200,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error toggling watchlist:', error);
            Swal.fire({
                title: 'Error',
                text: 'Gagal memperbarui status favorit.',
                icon: 'error'
            });
        });
    });
});
</script>
@endpush