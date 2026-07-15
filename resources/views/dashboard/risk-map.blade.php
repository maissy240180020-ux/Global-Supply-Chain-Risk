<div class="card dashboard-card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            🌍 Peta Risiko Rantai Pasok Global
        </h5>
        <span class="badge bg-success">
            Aktif
        </span>
    </div>

    <div class="card-body">
        <p class="text-muted mb-3">
            Peta di bawah menampilkan tingkat risiko rantai pasok global per negara. Klik pin untuk info cepat.
        </p>
        <div id="riskMap" style="height:450px;"></div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    var riskMapContainer = document.getElementById('riskMap');
    if (!riskMapContainer) return;

    // Koordinat pusat default: negara terpilih atau tengah dunia
    var mapLat = {{ $selectedCountry->latitude ?? 20 }};
    var mapLng = {{ $selectedCountry->longitude ?? 0 }};
    var zoomLevel = {{ $selectedCountry ? 4 : 2 }};

    var riskMap = L.map('riskMap').setView([mapLat, mapLng], zoomLevel);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(riskMap);

    // Kustomisasi Icon berdasarkan level risiko
    var greenIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    var orangeIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    var redIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    // Ambil data semua negara dari PHP
    var countriesData = @json($countries);

    countriesData.forEach(function(country) {
        if (country.latitude && country.longitude) {
            var icon = greenIcon;
            if (country.risk_level === 'Medium') {
                icon = orangeIcon;
            } else if (country.risk_level === 'High') {
                icon = redIcon;
            }

            var marker = L.marker([parseFloat(country.latitude), parseFloat(country.longitude)], { icon: icon })
                .addTo(riskMap);

            var popupContent = `
                <div style="font-family: 'Poppins', sans-serif;">
                    <h6 class="fw-bold mb-1">${country.country_name} (${country.country_code})</h6>
                    <div style="font-size:0.8rem; margin-bottom: 5px;">Ibukota: <b>${country.capital}</b></div>
                    <div style="font-size:0.8rem; margin-bottom: 5px;">Skor Risiko: <b class="text-primary">${country.risk_score}/100</b></div>
                    <div style="font-size:0.8rem; margin-bottom: 5px;">Level Risiko: <b>${country.risk_level}</b></div>
                    <a href="?country_id=${country.id}" class="btn btn-primary btn-sm text-white py-0 px-2 mt-1" style="font-size:0.75rem;">Monitor Negara Ini</a>
                </div>
            `;
            marker.bindPopup(popupContent);

            // Buka popup otomatis jika negara ini sedang terpilih
            if (country.id === {{ $selectedCountry->id ?? 'null' }}) {
                marker.openPopup();
            }
        }
    });
});
</script>
@endpush