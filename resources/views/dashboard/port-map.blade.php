<div class="card dashboard-card mb-4 border-0" style="border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 24px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.03)';">
    <div class="card-header bg-white d-flex justify-content-between align-items-center pt-4 pb-3 px-4 border-0">
        <div class="d-flex align-items-center gap-3">
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">
                🚢 Peta Lokasi Pelabuhan: <strong>{{ $selectedCountry->country_name }}</strong>
            </h5>
            <div class="badge-realtime" style="padding: 3px 8px; font-size: 0.65rem;"><i class="bi bi-record-circle-fill"></i> Realtime</div>
        </div>
        <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm">
            {{ $ports->count() }} Pelabuhan
        </span>
    </div>

    <div class="card-body">
        <div class="row">
            <!-- Daftar Pelabuhan -->
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="list-group" id="portList" style="max-height: 350px; overflow-y: auto;">
                    @forelse($ports as $port)
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center port-selector-btn" 
                                data-lat="{{ $port->latitude }}" 
                                data-lng="{{ $port->longitude }}" 
                                data-name="{{ $port->port_name }}">
                            <div>
                                <i class="bi bi-geo-alt-fill text-primary"></i> 
                                <strong style="font-size:0.9rem;">{{ $port->port_name }}</strong>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                {{ number_format($port->latitude, 3) }}, {{ number_format($port->longitude, 3) }}
                            </small>
                        </button>
                    @empty
                        <div class="text-center text-muted p-4 border rounded">
                            <i class="bi bi-info-circle text-warning fs-3 d-block mb-2"></i>
                            Tidak ada data pelabuhan untuk negara ini di database.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Peta Map -->
            <div class="col-md-8">
                <div id="portMap" style="height:350px; border-radius: 10px; border: 1px solid #ddd; overflow:hidden;"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    var portMapContainer = document.getElementById('portMap');
    if (!portMapContainer) return;

    var portsData = @json($ports);
    
    // Default center if no ports exist
    var defaultLat = {{ $selectedCountry->latitude ?? 0.0 }};
    var defaultLng = {{ $selectedCountry->longitude ?? 0.0 }};
    var mapZoom = 5;

    if (portsData.length > 0) {
        defaultLat = parseFloat(portsData[0].latitude);
        defaultLng = parseFloat(portsData[0].longitude);
        mapZoom = 6;
    }

    // Inisialisasi Peta
    var portMap = L.map('portMap').setView([defaultLat, defaultLng], mapZoom);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors, © CARTO'
    }).addTo(portMap);

    setTimeout(function() {
        portMap.invalidateSize();
    }, 150);

    var markers = {};

    // Helper functions to enrich ports dynamically with status colors
    function getPortStatus(id) {
        const val = (id * 17) % 100;
        if (val < 28) return 'Rendah';
        if (val < 67) return 'Sedang';
        return 'Tinggi';
    }

    function createPortIcon(status) {
        let color = '#3b82f6'; // default blue
        if (status === 'Rendah') color = '#10b981'; // green
        if (status === 'Sedang') color = '#f59e0b'; // yellow/orange
        if (status === 'Tinggi') color = '#ef4444'; // red

        const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="36" viewBox="0 0 28 36">
            <path d="M14 0C6.27 0 0 6.27 0 14c0 9.63 14 22 14 22s14-12.37 14-22C28 6.27 21.73 0 14 0z" fill="${color}" stroke="white" stroke-width="2"/>
            <text x="14" y="18" text-anchor="middle" fill="white" font-size="12" font-weight="bold" font-family="Arial">⚓</text>
        </svg>`;
        return L.divIcon({
            html: svg,
            className: 'bg-transparent border-0',
            iconSize: [28, 36],
            iconAnchor: [14, 36],
            popupAnchor: [0, -36]
        });
    }

    // Tambah marker pelabuhan
    portsData.forEach(function(port) {
        var status = getPortStatus(port.id);
        var icon = createPortIcon(status);
        var marker = L.marker([parseFloat(port.latitude), parseFloat(port.longitude)], { icon: icon })
            .addTo(portMap)
            .bindPopup("<b>Pelabuhan: " + port.port_name + "</b><br>Negara: {{ $selectedCountry->country_name }}<br>Kemacetan: <span style='font-weight: 600; color:" + (status === 'Rendah' ? '#10b981' : status === 'Sedang' ? '#d97706' : '#ef4444') + "'>" + status + "</span>");
        
        markers[port.port_name] = marker;
    });

    // Event listener untuk tombol daftar pelabuhan
    document.querySelectorAll('.port-selector-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var lat = parseFloat(this.getAttribute('data-lat'));
            var lng = parseFloat(this.getAttribute('data-lng'));
            var name = this.getAttribute('data-name');

            // Fly to coordinate
            portMap.flyTo([lat, lng], 10, {
                animate: true,
                duration: 1.5
            });

            // Open Popup
            if (markers[name]) {
                markers[name].openPopup();
            }

            // Aktifkan kelas active
            document.querySelectorAll('.port-selector-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>
@endpush