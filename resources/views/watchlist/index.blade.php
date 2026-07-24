@extends('layouts.app')

@section('title', 'Daftar Pantauan Favorit')

@section('content')
<style>
    .hover-row { transition: all 0.2s ease; }
    .hover-row:hover { background-color: #f8fafc; transform: scale(1.002); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); z-index: 10; position: relative; }
    .stat-card { transition: transform 0.25s ease, box-shadow 0.25s ease; border-radius: 14px; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.08); }
    
    .loading-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.8); z-index: 50;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        border-radius: 16px;
    }
    
    .progress-bar-animated {
        transition: width 1s ease-in-out;
    }
</style>

<div class="container-fluid py-2">

    <!-- Header & Search -->
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.85rem;">⭐ Daftar Pantauan Favorit</h2>
            <p class="text-secondary mb-0" style="font-size: 0.95rem;">Pemantauan Prioritas Risiko Rantai Pasok Global Khusus Untuk Anda</p>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light shadow-sm text-primary fw-bold" id="refreshBtn" onclick="loadWatchlistData()">
                <i class="bi bi-arrow-clockwise me-1"></i> Perbarui Data
            </button>
            <div class="position-relative" style="width: 250px;">
                <input type="text" id="watchlistSearch" class="form-control ps-5 border-light shadow-sm" 
                       placeholder="Cari negara terpantau..."
                       style="font-size: 0.85rem; border-radius: 10px; background-color: #fff; height: 38px;">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.9rem;"></i>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4" id="statsContainer">
        <!-- Total Favorit -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 stat-card" style="background-color: #f0f9ff; border: 1px solid #bae6fd !important;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm" style="width: 50px; height: 50px; color: #0284c7;">
                        <i class="bi bi-star-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="fw-bold d-block text-uppercase" style="color: #0369a1; font-size: 0.65rem;">Total Dipantau</small>
                        <h3 class="fw-bold mb-0 mt-1 text-dark" id="statTotal">-</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- High Risk -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 stat-card" style="background-color: #fef2f2; border: 1px solid #fecaca !important;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm" style="width: 50px; height: 50px; color: #ef4444;">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="fw-bold d-block text-uppercase" style="color: #b91c1c; font-size: 0.65rem;">Risiko Tinggi</small>
                        <h3 class="fw-bold mb-0 mt-1 text-dark" id="statHigh">-</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- Medium Risk -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 stat-card" style="background-color: #fffbeb; border: 1px solid #fde68a !important;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm" style="width: 50px; height: 50px; color: #f59e0b;">
                        <i class="bi bi-exclamation-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="fw-bold d-block text-uppercase" style="color: #b45309; font-size: 0.65rem;">Risiko Sedang</small>
                        <h3 class="fw-bold mb-0 mt-1 text-dark" id="statMedium">-</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- Low Risk -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 stat-card" style="background-color: #f0fdf4; border: 1px solid #bbf7d0 !important;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm" style="width: 50px; height: 50px; color: #10b981;">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="fw-bold d-block text-uppercase" style="color: #047857; font-size: 0.65rem;">Risiko Rendah</small>
                        <h3 class="fw-bold mb-0 mt-1 text-dark" id="statLow">-</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="card border-0 shadow-sm position-relative" style="border-radius: 16px; min-height: 400px;">
        
        <!-- Loading Indicator -->
        <div id="loadingOverlay" class="loading-overlay">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
            <h5 class="fw-bold text-dark">Mengambil Data Real-Time...</h5>
            <p class="text-muted small">Mensinkronisasi skor risiko, cuaca, dan mata uang</p>
        </div>

        <div class="card-body p-0">
            <!-- Table Container -->
            <div class="table-responsive d-none" id="tableContainer">
                <table class="table align-middle mb-0" id="watchlistTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 border-bottom-0" style="width: 70px;">No</th>
                            <th class="border-bottom-0" style="width: 90px;">Bendera</th>
                            <th class="border-bottom-0">Negara</th>
                            <th class="border-bottom-0" style="width: 140px;">Level Risiko</th>
                            <th class="border-bottom-0" style="width: 200px;">Skor Risiko</th>
                            <th class="border-bottom-0" style="width: 150px;">Cuaca Live</th>
                            <th class="border-bottom-0" style="width: 120px;">Mata Uang</th>
                            <th class="text-center border-bottom-0" style="width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="watchlistTbody">
                        <!-- Data rows will be injected here -->
                    </tbody>
                </table>
            </div>

            <!-- Empty State Container -->
            <div class="text-center py-5 px-4 d-none" id="emptyContainer">
                <div class="py-4 d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex align-items-center justify-content-center rounded-circle mb-4 shadow-sm" 
                         style="width: 90px; height: 90px; background-color: #f1f5f9; color: #94a3b8;">
                        <i class="bi bi-star fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Belum Ada Negara Favorit</h4>
                    <p class="text-muted mx-auto mb-4" style="max-width: 500px; line-height: 1.6;">
                        Tambahkan negara ke daftar pantauan favorit Anda melalui halaman Dashboard Negara atau Detail Negara agar Anda dapat memantau indikator real-time secara khusus di sini.
                    </p>
                    <a href="{{ route('countries.index') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-globe2 me-2"></i> Jelajahi Negara Global
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableContainer = document.getElementById('tableContainer');
    const emptyContainer = document.getElementById('emptyContainer');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const watchlistTbody = document.getElementById('watchlistTbody');
    const searchInput = document.getElementById('watchlistSearch');
    const refreshBtn = document.getElementById('refreshBtn');

    // Load Live Data
    window.loadWatchlistData = function() {
        loadingOverlay.classList.remove('d-none');
        tableContainer.classList.add('d-none');
        emptyContainer.classList.add('d-none');
        refreshBtn.disabled = true;

        fetch('{{ route('watchlist.live-data') }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) throw new Error('Failed to fetch');

            // Update Stats
            document.getElementById('statTotal').textContent = data.stats.total;
            document.getElementById('statHigh').textContent = data.stats.high_risk;
            document.getElementById('statMedium').textContent = data.stats.medium_risk;
            document.getElementById('statLow').textContent = data.stats.low_risk;

            if (data.data.length === 0) {
                emptyContainer.classList.remove('d-none');
                searchInput.disabled = true;
            } else {
                renderTable(data.data);
                tableContainer.classList.remove('d-none');
                searchInput.disabled = false;
                
                // Re-apply search filter if there's any text
                if (searchInput.value) searchInput.dispatchEvent(new Event('input'));
            }
        })
        .catch(err => {
            console.error('Watchlist Error:', err);
            Swal.fire('Gagal Memuat Data', 'Terjadi kesalahan saat memuat data live dari API.', 'error');
        })
        .finally(() => {
            loadingOverlay.classList.add('d-none');
            refreshBtn.disabled = false;
        });
    };

    function renderTable(countries) {
        watchlistTbody.innerHTML = '';
        countries.forEach((country, index) => {
            
            // Risk Level Badge Class
            let badgeClass = 'bg-success-subtle text-success border-success';
            let barClass = 'bg-success';
            if (country.risk_level === 'Medium') {
                badgeClass = 'bg-warning-subtle text-warning border-warning';
                barClass = 'bg-warning';
            } else if (country.risk_level === 'High') {
                badgeClass = 'bg-danger-subtle text-danger border-danger';
                barClass = 'bg-danger';
            }

            const tr = document.createElement('tr');
            tr.className = 'hover-row border-bottom border-light';
            tr.id = `watchlist-row-${country.id}`;
            tr.innerHTML = `
                <td class="ps-4 fw-semibold text-secondary">${index + 1}</td>
                <td>
                    ${country.flag ? `<img src="${country.flag}" alt="${country.name}" width="42" class="border border-light rounded shadow-sm" style="object-fit:cover; height:28px;">` : `<span class="fs-4">🌍</span>`}
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <strong class="text-dark fs-6" style="line-height:1.2;">${country.name}</strong>
                        <span class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-bank me-1"></i>${country.capital}</span>
                    </div>
                </td>
                <td>
                    <span class="badge rounded-pill fw-bold px-3 py-2 border" style="font-size: 0.75rem; letter-spacing: 0.03em; ${badgeClass.includes('border') ? '' : ''}" class="${badgeClass}">
                        ${country.risk_level.toUpperCase()}
                    </span>
                </td>
                <td>
                    <div class="d-flex flex-column justify-content-center pe-3">
                        <div class="d-flex justify-content-between align-items-end mb-1">
                            <span class="fw-bold text-dark" style="font-size:0.9rem;">${country.risk_score}</span>
                            <span class="text-muted" style="font-size:0.7rem;">/100</span>
                        </div>
                        <div class="progress shadow-sm" style="height: 6px; border-radius: 4px; background-color: #f1f5f9;">
                            <div class="progress-bar progress-bar-animated ${barClass}" role="progressbar" style="width: ${country.risk_score}%"></div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width:36px; height:36px; background-color:#f8fafc;">
                            <i class="bi ${country.weather_icon} ${country.weather_color} fs-5"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <strong class="text-dark" style="font-size:0.9rem;">${country.temperature}°C</strong>
                            <span class="text-muted text-truncate" style="font-size:0.7rem; max-width:80px;" title="${country.weather_condition}">${country.weather_condition}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-light text-secondary border px-2 py-1">${country.currency}</span>
                </td>
                <td class="text-center pe-4">
                    <div class="d-flex justify-content-center gap-2">
                        <a href="/dashboard?country_id=${country.id}" class="btn btn-dark btn-sm rounded-pill px-3 py-1.5 d-flex align-items-center gap-1 shadow-sm" style="font-size: 0.75rem; transition:all 0.2s;">
                            <i class="bi bi-speedometer2"></i> Monitor
                        </a>
                        <button class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1.5 d-flex align-items-center gap-1 shadow-sm remove-btn" data-id="${country.id}" data-name="${country.name}" style="font-size: 0.75rem; transition:all 0.2s;">
                            <i class="bi bi-star-fill"></i> Lepas
                        </button>
                    </div>
                </td>
            `;
            
            // Re-apply the badge classes properly since template literal class="" logic above was slightly separated
            tr.querySelector('td:nth-child(4) span').className = `badge rounded-pill fw-bold px-3 py-1.5 border ${badgeClass}`;

            watchlistTbody.appendChild(tr);
        });

        attachRemoveEvents();
    }

    function attachRemoveEvents() {
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const countryId = this.getAttribute('data-id');
                const countryName = this.getAttribute('data-name');
                const row = document.getElementById(`watchlist-row-${countryId}`);
                
                Swal.fire({
                    title: 'Lepas Favorit?',
                    text: `${countryName} akan dihapus dari daftar pantauan prioritas Anda.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Lepas!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
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
                            if (data.success && !data.is_favorite) {
                                // Update Stats locally
                                let statTotal = parseInt(document.getElementById('statTotal').textContent);
                                document.getElementById('statTotal').textContent = Math.max(0, statTotal - 1);
                                
                                row.style.opacity = '0';
                                row.style.transform = 'translateX(-20px)';
                                
                                setTimeout(() => {
                                    row.remove();
                                    const remainingRows = document.querySelectorAll('#watchlistTable tbody tr');
                                    if (remainingRows.length === 0) {
                                        tableContainer.classList.add('d-none');
                                        emptyContainer.classList.remove('d-none');
                                    } else {
                                        remainingRows.forEach((r, idx) => r.cells[0].textContent = idx + 1);
                                    }
                                }, 300);

                                Swal.fire({
                                    title: 'Dilepas!',
                                    text: `${countryName} dihapus dari daftar pantauan.`,
                                    icon: 'success',
                                    timer: 1200,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Gagal memperbarui daftar pantauan.', 'error');
                        });
                    }
                });
            });
        });
    }

    // Search Logic
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#watchlistTable tbody tr');
        
        rows.forEach(row => {
            const countryName = row.querySelector('td:nth-child(3) strong').textContent.toLowerCase();
            const capital = row.querySelector('td:nth-child(3) span').textContent.toLowerCase();
            
            if (countryName.includes(query) || capital.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Initial Load
    loadWatchlistData();
});
</script>
@endpush