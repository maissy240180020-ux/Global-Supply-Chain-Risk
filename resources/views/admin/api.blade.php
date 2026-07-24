@extends('layouts.app')

@section('title', 'API Monitoring Dashboard')

@section('content')
<style>
    .status-card { transition: transform 0.2s; border-radius: 12px; }
    .status-card:hover { transform: translateY(-3px); }
    .pulse {
        animation: pulse-animation 2s infinite;
    }
    @keyframes pulse-animation {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0,0,0, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(0,0,0, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0,0,0, 0); }
    }
    .status-dot {
        width: 12px; height: 12px; border-radius: 50%; display: inline-block;
    }
    .bg-green-pulse { background-color: #22c55e; box-shadow: 0 0 0 rgba(34, 197, 94, 0.4); animation: pulse-green 2s infinite; }
    .bg-red-pulse { background-color: #ef4444; box-shadow: 0 0 0 rgba(239, 68, 68, 0.4); animation: pulse-red 2s infinite; }
    .bg-gray { background-color: #94a3b8; }
    
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    
    .api-preview-box {
        background: #1e293b; color: #a5b4fc; font-family: monospace; font-size: 0.85rem;
        padding: 15px; border-radius: 8px; max-height: 300px; overflow-y: auto; white-space: pre-wrap;
    }
</style>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-activity text-primary me-2"></i>API Monitoring Dashboard</h2>
            <p class="text-muted mb-0">Pusat kontrol dan monitoring status detak jantung (ping) integrasi REST API sistem.</p>
        </div>
        <button class="btn btn-dark rounded-pill px-4" onclick="checkApiStatus()" id="btnRefresh">
            <i class="bi bi-arrow-repeat me-1"></i> Ping Ulang API
        </button>
    </div>

    <!-- API Status Cards -->
    <div class="row g-3 mb-5" id="apiCardsContainer">
        <!-- Cards will be rendered by JS -->
    </div>

    <!-- Live Data Preview Section -->
    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
            <h5 class="fw-bold"><i class="bi bi-terminal me-2"></i>Live Data Preview</h5>
            <p class="text-muted small">Pratinjau struktur data asli yang ditarik secara real-time dari setiap API.</p>
            
            <ul class="nav nav-tabs mt-3" id="apiTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-worldbank" type="button" role="tab" onclick="fetchSample('worldbank')">World Bank</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-restcountries" type="button" role="tab" onclick="fetchSample('restcountries')">REST Countries</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-exchangerate" type="button" role="tab" onclick="fetchSample('exchangerate')">ExchangeRate</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-openmeteo" type="button" role="tab" onclick="fetchSample('openmeteo')">Open-Meteo</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-localapi" type="button" role="tab" onclick="fetchSample('localapi')">Local API</button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="apiTabsContent">
                
                <!-- World Bank Tab -->
                <div class="tab-pane fade show active" id="tab-worldbank" role="tabpanel">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">GET</span>
                        <code class="text-dark">http://api.worldbank.org/v2/country/id/indicator/NY.GDP.MKTP.CD?format=json</code>
                    </div>
                    <div class="api-preview-box" id="preview-worldbank">Menunggu permintaan data...</div>
                </div>

                <!-- REST Countries Tab -->
                <div class="tab-pane fade" id="tab-restcountries" role="tabpanel">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">GET</span>
                        <code class="text-dark">https://restcountries.com/v3.1/alpha/id</code>
                    </div>
                    <div class="api-preview-box" id="preview-restcountries">Menunggu permintaan data...</div>
                </div>

                <!-- ExchangeRate Tab -->
                <div class="tab-pane fade" id="tab-exchangerate" role="tabpanel">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">GET</span>
                        <code class="text-dark">https://open.er-api.com/v6/latest/USD</code>
                    </div>
                    <div class="api-preview-box" id="preview-exchangerate">Menunggu permintaan data...</div>
                </div>

                <!-- Open-Meteo Tab -->
                <div class="tab-pane fade" id="tab-openmeteo" role="tabpanel">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">GET</span>
                        <code class="text-dark">https://api.open-meteo.com/v1/forecast?...</code>
                    </div>
                    <div class="api-preview-box" id="preview-openmeteo">Menunggu permintaan data...</div>
                </div>

                <!-- Local API Tab -->
                <div class="tab-pane fade" id="tab-localapi" role="tabpanel">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">GET</span>
                        <code class="text-dark">{{ url('/api/countries') }}</code>
                    </div>
                    <div class="api-preview-box" id="preview-localapi">Menunggu permintaan data...</div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const apis = [
        { id: 'world_bank', name: 'World Bank API', icon: 'bi-bank' },
        { id: 'rest_countries', name: 'REST Countries', icon: 'bi-globe' },
        { id: 'exchange_rate', name: 'ExchangeRate', icon: 'bi-currency-exchange' },
        { id: 'open_meteo', name: 'Open-Meteo', icon: 'bi-cloud-sun' },
        { id: 'gnews', name: 'GNews API', icon: 'bi-newspaper' },
        { id: 'local_api', name: 'Local REST API', icon: 'bi-server' }
    ];

    function renderInitialCards() {
        const container = document.getElementById('apiCardsContainer');
        container.innerHTML = '';
        
        apis.forEach(api => {
            container.innerHTML += `
                <div class="col-xl-4 col-md-6">
                    <div class="card border-0 shadow-sm status-card" style="border: 1px solid #e2e8f0 !important;" id="card-${api.id}">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light text-primary rounded p-2 d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                                        <i class="bi ${api.icon} fs-5"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-dark">${api.name}</h6>
                                </div>
                                <div class="status-dot bg-gray" id="dot-${api.id}"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-end mt-4">
                                <div>
                                    <small class="text-muted d-block mb-1">Status</small>
                                    <strong class="text-secondary" id="status-${api.id}">Checking...</strong>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block mb-1">Response Time</small>
                                    <strong class="text-dark fs-5" id="latency-${api.id}">--</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    function checkApiStatus() {
        const btn = document.getElementById('btnRefresh');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Pinging...';

        apis.forEach(api => {
            document.getElementById(`dot-${api.id}`).className = 'status-dot bg-gray';
            document.getElementById(`status-${api.id}`).textContent = 'Pinging...';
            document.getElementById(`status-${api.id}`).className = 'text-secondary';
            document.getElementById(`latency-${api.id}`).textContent = '--';
        });

        fetch('{{ route("admin.api.ping") }}')
            .then(response => response.json())
            .then(data => {
                for (const [id, result] of Object.entries(data)) {
                    const dot = document.getElementById(`dot-${id}`);
                    const statusText = document.getElementById(`status-${id}`);
                    const latencyText = document.getElementById(`latency-${id}`);
                    
                    if (result.success) {
                        dot.className = 'status-dot bg-green-pulse';
                        statusText.textContent = 'Connected';
                        statusText.className = 'text-success fw-bold';
                    } else {
                        dot.className = 'status-dot bg-red-pulse';
                        statusText.textContent = 'Disconnected';
                        statusText.className = 'text-danger fw-bold';
                    }
                    latencyText.textContent = result.latency;
                }
            })
            .catch(error => {
                console.error('Error fetching ping:', error);
                Swal.fire('Gagal Ping', 'Gagal menghubungi server untuk pengecekan status API.', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Ping Ulang API';
            });
    }

    function fetchSample(api) {
        const box = document.getElementById(`preview-${api}`);
        box.innerHTML = '<span class="spinner-border spinner-border-sm me-2 text-primary"></span> Fetching real-time data...';

        let url = '';
        if (api === 'worldbank') url = 'https://api.worldbank.org/v2/country/id/indicator/NY.GDP.MKTP.CD?format=json';
        if (api === 'restcountries') url = 'https://restcountries.com/v3.1/alpha/id';
        if (api === 'exchangerate') url = 'https://open.er-api.com/v6/latest/USD';
        if (api === 'openmeteo') url = 'https://api.open-meteo.com/v1/forecast?latitude=-6.2088&longitude=106.8456&current=temperature_2m,relative_humidity_2m';
        if (api === 'localapi') url = '{{ url('/api/countries') }}';

        if (url) {
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    box.innerHTML = JSON.stringify(data, null, 2);
                })
                .catch(err => {
                    box.innerHTML = `<span class="text-danger">Failed to fetch data: ${err.message}</span>`;
                });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderInitialCards();
        checkApiStatus();
        fetchSample('worldbank'); // Load first tab by default
    });
</script>
@endpush
