@extends('layouts.app')

@section('title','Data Negara | SIMRPG')

@section('content')

<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        🌍 Data Negara
    </h2>

    <!-- Search Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <!-- Modern Search Input with integrated icon and Debounced AJAX -->
        <div class="position-relative" style="width: 320px;">
            <input type="text" id="countrySearch" class="form-control ps-5 border-light shadow-none" 
                   placeholder="Cari nama negara, ibukota, atau mata uang..."
                   value="{{ request('search') }}"
                   style="font-size: 0.85rem; border-radius: 10px; background-color: #f8fafc; height: 40px;">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.9rem;"></i>
        </div>
    </div>

    <!-- Country KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Negara</h6>
                    <h2 class="fw-bold text-primary" id="cardTotalCount">
                        {{ $countries->total() }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Risiko Tinggi</h6>
                    <h2 class="fw-bold text-danger">
                        {{ \App\Models\Country::where('risk_level','High')->count() }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Rata-rata Skor Risiko</h6>
                    <h2 class="fw-bold text-warning">
                        {{ number_format(\App\Models\Country::avg('risk_score'), 1) }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total GDP</h6>
                    <h5 class="fw-bold text-success" style="line-height: 1.8;">
                        {{ number_format(\App\Models\Country::sum('gdp'), 0) }}
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom-0 pb-0 pt-3">
            <h6 class="fw-bold mb-0">🗺️ Peta Titik Pemantauan Risiko Global (Drop Points)</h6>
        </div>
        <div class="card-body">
            <div id="countriesMap" class="rounded border" style="height: 380px; z-index:1;"></div>
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

    var countries = @json($mapCountries);
    
    countries.forEach(function(country) {
        if (country.latitude && country.longitude) {
            var color = '#10b981'; // Green
            if (country.risk_level === 'High') {
                color = '#ef4444'; // Red
            } else if (country.risk_level === 'Medium') {
                color = '#f59e0b'; // Amber
            }

            var markerHtml = `
                <div style="
                    background-color: ${color}; 
                    width: 14px; 
                    height: 14px; 
                    border-radius: 50%; 
                    border: 2px solid white; 
                    box-shadow: 0 0 4px rgba(0,0,0,0.4);
                "></div>
            `;

            var customIcon = L.divIcon({
                html: markerHtml,
                className: 'custom-div-icon',
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });

            var popupContent = `
                <div style="font-family: inherit; font-size: 13px; line-height: 1.4; min-width: 160px;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="${country.flag}" width="22" style="border-radius: 2px; border: 1px solid #ddd;">
                        <strong>${country.country_name}</strong>
                    </div>
                    <div style="font-size: 11px; margin-bottom: 2px;"><strong>Ibu Kota:</strong> ${country.capital}</div>
                    <div style="font-size: 11px; margin-bottom: 5px;"><strong>Skor Risiko:</strong> <span class="badge text-white" style="background-color: ${color}">${country.risk_level} (${Math.round(country.risk_score)})</span></div>
                    <div class="text-center">
                        <a href="/dashboard?country_id=${country.id}" class="btn btn-xs btn-dark text-white text-center d-block py-1" style="font-size: 10px;">Monitor Negara</a>
                    </div>
                </div>
            `;

            L.marker([parseFloat(country.latitude), parseFloat(country.longitude)], { icon: customIcon })
                .addTo(map)
                .bindPopup(popupContent);
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

        fetch(nextPageUrl, {
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
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(this.value);
        }, 300);
    });

    function performSearch(query) {
        isLoading = true;
        scrollLoader.classList.remove('d-none');
        scrollEnd.classList.add('d-none');

        const searchUrl = `/countries?search=${encodeURIComponent(query)}`;

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