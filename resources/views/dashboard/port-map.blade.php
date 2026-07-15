<div class="card dashboard-card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            🚢 Peta Lokasi Pelabuhan Internasional: <strong>{{ $selectedCountry->country_name }}</strong>
        </h5>
        <span class="badge bg-primary">
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

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(portMap);

    var markers = {};

    // Tambah marker pelabuhan
    portsData.forEach(function(port) {
        var marker = L.marker([parseFloat(port.latitude), parseFloat(port.longitude)])
            .addTo(portMap)
            .bindPopup("<b>Pelabuhan: " + port.port_name + "</b><br>Negara: {{ $selectedCountry->country_name }}");
        
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