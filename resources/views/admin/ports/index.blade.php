@extends('layouts.app')

@section('title', 'Kelola Pelabuhan - Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0 text-dark">
                <i class="bi bi-geo-alt-fill text-info me-2"></i>Dataset Pelabuhan
            </h2>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">Kelola titik kordinat pelabuhan utama di seluruh dunia.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.ports.create') }}" class="btn btn-info text-white rounded-pill fw-bold shadow-sm px-4 d-flex align-items-center gap-2" style="transition: all 0.3s;">
                <i class="bi bi-plus-circle-fill"></i> Tambah Pelabuhan
            </a>
        </div>
    </div>

    <!-- Peta Sebaran Pelabuhan (Halaman Ini) -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-map text-primary me-2"></i> Peta Lokasi Pelabuhan (Halaman {{ $ports->currentPage() }})</h6>
        </div>
        <div class="card-body p-0">
            <div id="portsMap" style="height: 300px; width: 100%; z-index: 1;"></div>
        </div>
    </div>

    <!-- Tabel Data Pelabuhan -->
    <div class="card shadow-sm border-0" id="ports-table" style="border-radius: 16px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3" style="width: 5%;">No</th>
                            <th style="width: 35%;">Nama Pelabuhan & Negara</th>
                            <th style="width: 25%;">Koordinat Geografis</th>
                            <th style="width: 15%;">Ditambahkan Pada</th>
                            <th class="text-end pe-4" style="width: 20%;">Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ports as $index => $port)
                        <tr style="transition: all 0.2s ease;">
                            <td class="ps-4 text-muted fw-bold">{{ $ports->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="bi bi-water fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $port->port_name }}</h6>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill border fw-semibold px-2 py-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                            <i class="bi bi-flag-fill me-1"></i>{{ $port->country_code }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.85rem; font-family: monospace;">
                                        <span class="badge bg-light text-dark border w-100 text-start py-2">
                                            <span class="text-primary fw-bold">Lat:</span> {{ number_format($port->latitude, 5) }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.85rem; font-family: monospace;">
                                        <span class="badge bg-light text-dark border w-100 text-start py-2">
                                            <span class="text-success fw-bold">Lng:</span> {{ number_format($port->longitude, 5) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-muted" style="font-size: 0.85rem;">
                                    <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($port->created_at)->format('d M Y') }}
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.ports.edit', $port->id) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.ports.destroy', $port->id) }}" method="POST" class="delete-form m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Hapus Data">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($ports->isEmpty())
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="No Data" style="width: 120px; opacity: 0.5;" class="mb-3">
                    <h5 class="text-muted fw-bold mb-1">Data Pelabuhan Kosong</h5>
                    <p class="text-muted small">Belum ada titik pelabuhan yang dimasukkan ke dalam database.</p>
                </div>
            @endif
        </div>
        
        <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted fw-semibold">Menampilkan {{ $ports->firstItem() ?? 0 }} - {{ $ports->lastItem() ?? 0 }} dari total {{ $ports->total() }} pelabuhan</small>
            <div>
                {{ $ports->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if($ports->isNotEmpty())
            // Inisialisasi Peta
            var map = L.map('portsMap').setView([20, 0], 2);
            
            // Tambahkan Tile Layer (Dark Mode Theme)
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://carto.com/">Carto</a>'
            }).addTo(map);

            // Kustom Ikon Marker
            var portIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#0dcaf0; width:15px; height:15px; border-radius:50%; border:2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);'></div>",
                iconSize: [15, 15],
                iconAnchor: [7.5, 7.5]
            });

            // Tambahkan Marker untuk setiap pelabuhan di halaman ini
            var bounds = [];
            @foreach($ports as $p)
                @if($p->latitude && $p->longitude)
                    L.marker([{{ $p->latitude }}, {{ $p->longitude }}], {icon: portIcon})
                     .addTo(map)
                     .bindPopup("<b>{{ $p->port_name }}</b><br>Negara: {{ $p->country_code }}");
                    bounds.push([{{ $p->latitude }}, {{ $p->longitude }}]);
                @endif
            @endforeach

            // Auto zoom agar semua marker terlihat
            if (bounds.length > 0) {
                map.fitBounds(bounds, {padding: [30, 30], maxZoom: 5});
            }
        @endif
    });
</script>
@endpush
