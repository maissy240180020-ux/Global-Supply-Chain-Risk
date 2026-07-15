@extends('layouts.app')

@section('title','Data Negara')

@section('content')

<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        🌍 Data Negara
    </h2>

    <div class="d-flex justify-content-between align-items-center mb-4">

        <form action="{{ route('countries.index') }}"
              method="GET"
              class="d-flex w-50">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control me-2"
                placeholder="Cari nama negara...">

            <button class="btn btn-primary">

                <i class="bi bi-search"></i>

                Cari

            </button>

        </form>

    </div>

    <div class="row mb-4">

    <div class="col-md-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center">

                <h6 class="text-muted">

                    Total Negara

                </h6>

                <h2 class="fw-bold text-primary">

                    {{ $countries->total() }}

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center">

                <h6 class="text-muted">

                    Risiko Tinggi

                </h6>

                <h2 class="fw-bold text-danger">

                    {{ \App\Models\Country::where('risk_level','High')->count() }}

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center">

                <h6 class="text-muted">

                    Rata-rata Skor Risiko

                </h6>

                <h2 class="fw-bold text-warning">

                    {{ number_format(\App\Models\Country::avg('risk_score'),1) }}

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center">

                <h6 class="text-muted">

                    Total GDP

                </h6>

                <h5 class="fw-bold text-success">

                    {{ number_format(\App\Models\Country::sum('gdp'),0) }}

                </h5>

            </div>

        </div>

    </div>

</div>

    <!-- PETA LOKASI NEGARA (DROP POINTS) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom-0 pb-0 pt-3">
            <h6 class="fw-bold mb-0">🗺️ Peta Titik Pemantauan Risiko Global (Drop Points)</h6>
        </div>
        <div class="card-body">
            <div id="countriesMap" class="rounded border" style="height: 380px; z-index:1;"></div>
        </div>
    </div>

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

                <tbody>

                    @forelse($countries as $country)

                    <tr>

                        <td class="text-center">
                            <button class="btn btn-link p-0 toggle-favorite-btn" data-country-id="{{ $country->id }}" style="text-decoration: none; border: none; background: none;">
                                <i class="bi {{ $country->is_favorite ? 'bi-star-fill text-warning' : 'bi-star text-muted' }} fs-5 favorite-star-icon"></i>
                            </button>
                        </td>

                        <td>{{ $loop->iteration }}</td>

                        <td>

    @if($country->flag)

        <img src="{{ $country->flag }}"
             width="45"
             class="border rounded shadow-sm">

    @endif

</td>

                        <td>

                            <strong>

                                {{ $country->country_name }}

                            </strong>

                        </td>

                        <td>

                            @php

                                $badge='bg-success';

                                if($country->risk_level=='Medium'){
                                    $badge='bg-warning text-dark';
                                }

                                if($country->risk_level=='High'){
                                    $badge='bg-danger';
                                }

                            @endphp

                            <span class="badge {{ $badge }}">

                                {{ number_format($country->risk_score,0) }}

                            </span>

                        </td>

                        <td>

                            {{ $country->currency }}

                        </td>

                        <td>

                            <strong>

                                {{ $country->temperature }}°C

                            </strong>

                            <br>

                            <small class="text-muted">

                                {{ $country->weather }}

                            </small>

                        </td>

                        <td class="text-center">

                            <a href="{{ route('countries.show',$country->id) }}"
                               class="btn btn-info btn-sm">

                                <i class="bi bi-eye"></i>

                                Detail

                            </a>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="7" class="text-center py-5">

                            <i class="bi bi-database fs-1 text-secondary"></i>

                            <br><br>

                            Belum ada data negara.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

            <div class="d-flex justify-content-between align-items-center mt-4">

                <small class="text-muted">

                    Menampilkan

                    {{ $countries->firstItem() ?? 0 }}

                    sampai

                    {{ $countries->lastItem() ?? 0 }}

                    dari

                    {{ $countries->total() }}

                    data.

                </small>

                <div>

                    {{ $countries->links('pagination::bootstrap-5') }}

                </div>

            </div>

        </div>

</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
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

    // AJAX Watchlist Toggle
    document.querySelectorAll('.toggle-favorite-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const countryId = this.getAttribute('data-country-id');
            const starIcon = this.querySelector('.favorite-star-icon');
            
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
});
</script>
@endpush

@endsection