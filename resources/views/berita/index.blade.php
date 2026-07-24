@extends('layouts.app')

@section('title', 'Analisis Berita Real-Time')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0 text-dark">
                <i class="bi bi-newspaper text-primary me-2"></i>Analisis Berita & Sentimen
            </h2>
            <p class="text-muted mb-0 mt-1" style="font-size: 0.95rem;">Pantau berita rantai pasok global secara real-time dari GNews API.</p>
        </div>
        <div class="d-flex gap-2">
            <button id="refreshBtn" class="btn btn-primary rounded-pill fw-bold shadow-sm px-4 d-flex align-items-center gap-2" onclick="refreshNews()" style="transition: all 0.3s;">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Filter Bar & Categories -->
    <div class="card shadow-sm border-0 mb-4 bg-white" style="border-radius: 16px;">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <!-- Category Pills -->
                <div class="d-flex flex-wrap gap-2" id="categoryPills">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-semibold category-btn {{ empty($category) ? 'active' : '' }}" data-category="">Semua Kategori</button>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-semibold category-btn {{ $category == 'Logistics' ? 'active' : '' }}" data-category="Logistics">Logistics</button>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-semibold category-btn {{ $category == 'Trade' ? 'active' : '' }}" data-category="Trade">Trade</button>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-semibold category-btn {{ $category == 'Shipping' ? 'active' : '' }}" data-category="Shipping">Shipping</button>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-semibold category-btn {{ $category == 'Economy' ? 'active' : '' }}" data-category="Economy">Economy</button>
                </div>
                
                <!-- Country Filter -->
                <div class="d-flex align-items-center gap-2">
                    <label class="fw-semibold text-muted small mb-0 text-nowrap"><i class="bi bi-funnel-fill text-primary me-1"></i> Negara:</label>
                    <select id="country" class="form-select bg-light border-0 fw-semibold" style="width: auto; min-width: 220px;" onchange="refreshNews()">
                        <option value="world">🌍 Seluruh Dunia (Global)</option>
                        <option value="USA" {{ $country == 'USA' ? 'selected' : '' }}>🇺🇸 Amerika Serikat</option>
                        <option value="China" {{ $country == 'China' ? 'selected' : '' }}>🇨🇳 China</option>
                        <option value="Indonesia" {{ $country == 'Indonesia' ? 'selected' : '' }}>🇮🇩 Indonesia</option>
                        <option value="Europe" {{ $country == 'Europe' ? 'selected' : '' }}>🇪🇺 Eropa</option>
                        <option value="Japan" {{ $country == 'Japan' ? 'selected' : '' }}>🇯🇵 Jepang</option>
                    </select>
                </div>
            </div>
            <input type="hidden" id="category" value="{{ $category }}">
        </div>
    </div>

    <!-- Sentiment Stats -->
    <div class="row g-4 mb-4" id="statsContainer">
        <!-- Stats will be populated here -->
        @include('berita.partials.stats', ['stats' => $stats])
    </div>

    <!-- Error Banner -->
    <div id="errorBanner" class="alert alert-danger shadow-sm border-0 rounded-4 mb-4 d-none">
        <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan</h6>
        <p class="mb-0" id="errorMessage"></p>
    </div>

    <!-- News Grid -->
    <div class="row g-4" id="newsGrid">
        @if($error)
            <div class="col-12">
                <div class="alert alert-danger shadow-sm border-0 rounded-4 p-4">
                    <h6 class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-2"></i>Gagal Mengambil Data</h6>
                    <p class="mb-0">{{ $error }}</p>
                </div>
            </div>
        @else
            @include('berita.partials.articles', ['articles' => $articles, 'country' => $country])
        @endif
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="d-none justify-content-center align-items-center py-5">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
        <span class="ms-3 fw-bold text-muted">Mengambil data Real-Time dari GNews API...</span>
    </div>

</div>
@endsection

@push('scripts')
<script>
function refreshNews() {
    const btn = document.getElementById('refreshBtn');
    const grid = document.getElementById('newsGrid');
    const statsContainer = document.getElementById('statsContainer');
    const loading = document.getElementById('loadingOverlay');
    const errorBanner = document.getElementById('errorBanner');
    const errorMessage = document.getElementById('errorMessage');

    // Ambil nilai filter
    const category = document.getElementById('category').value;
    const country = document.getElementById('country').value;

    // UI Loading State
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Memuat...';
    btn.disabled = true;
    grid.classList.add('d-none');
    statsContainer.classList.add('opacity-50');
    loading.classList.remove('d-none');
    loading.classList.add('d-flex');
    errorBanner.classList.add('d-none');

    // Buat URL API Request
    const url = new URL(window.location.href);
    url.searchParams.delete('keyword'); // Pastikan keyword dibersihkan dari URL
    url.searchParams.set('category', category);
    url.searchParams.set('country', country);
    
    // Update URL bar tanpa reload
    window.history.pushState({}, '', url);

    // Fetch API ke Controller
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            showError(data.error);
        } else {
            renderNews(data.articles);
            renderStats(data.stats);
        }
    })
    .catch(error => {
        showError("Terjadi kesalahan jaringan atau koneksi ke server.");
    })
    .finally(() => {
        // Pulihkan UI
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refresh';
        btn.disabled = false;
        loading.classList.remove('d-flex');
        loading.classList.add('d-none');
        grid.classList.remove('d-none');
        statsContainer.classList.remove('opacity-50');
    });
}

// Event Listeners for Category Pills
document.querySelectorAll('.category-btn').forEach(button => {
    button.addEventListener('click', function() {
        // Hapus kelas active dari semua tombol
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
        // Tambahkan ke tombol yang diklik
        this.classList.add('active');
        // Set hidden input value
        document.getElementById('category').value = this.getAttribute('data-category');
        // Panggil refresh
        refreshNews();
    });
});

function showError(msg) {
    document.getElementById('errorBanner').classList.remove('d-none');
    document.getElementById('errorMessage').textContent = msg;
    document.getElementById('newsGrid').innerHTML = '';
}

function renderStats(stats) {
    const total = stats.positive + stats.neutral + stats.negative;
    const posPct = total ? Math.round((stats.positive/total)*100) : 0;
    const negPct = total ? Math.round((stats.negative/total)*100) : 0;
    const neuPct = total ? Math.round((stats.neutral/total)*100) : 0;

    document.getElementById('statsContainer').innerHTML = `
        <div class="col-md-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px; border-left: 5px solid #198754 !important;">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <h6 class="text-muted fw-bold mb-1" style="font-size: 0.8rem; letter-spacing: 1px;">SENTIMEN POSITIF</h6>
                        <h3 class="fw-bolder text-success mb-0">${stats.positive} <span class="fs-6 text-muted fw-normal">(${posPct}%)</span></h3>
                    </div>
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px; border-left: 5px solid #6c757d !important;">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <h6 class="text-muted fw-bold mb-1" style="font-size: 0.8rem; letter-spacing: 1px;">SENTIMEN NETRAL</h6>
                        <h3 class="fw-bolder text-secondary mb-0">${stats.neutral} <span class="fs-6 text-muted fw-normal">(${neuPct}%)</span></h3>
                    </div>
                    <div class="bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-dash-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px; border-left: 5px solid #dc3545 !important;">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <h6 class="text-muted fw-bold mb-1" style="font-size: 0.8rem; letter-spacing: 1px;">SENTIMEN NEGATIF</h6>
                        <h3 class="fw-bolder text-danger mb-0">${stats.negative} <span class="fs-6 text-muted fw-normal">(${negPct}%)</span></h3>
                    </div>
                    <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-graph-down-arrow fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderNews(articles) {
    const grid = document.getElementById('newsGrid');
    if (!articles || articles.length === 0) {
        grid.innerHTML = `
            <div class="col-12 text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="No Data" style="width: 120px; opacity: 0.5;" class="mb-3">
                <h5 class="text-muted fw-bold">Tidak ada berita ditemukan</h5>
                <p class="text-muted small">Coba gunakan kata kunci atau kategori yang berbeda.</p>
            </div>
        `;
        return;
    }

    let html = '';
    articles.forEach(article => {
        
        let sentimentBadge = '';
        if (article.sentiment === 'Positif') {
            sentimentBadge = '<span class="badge bg-success shadow-sm rounded-pill px-3 py-2"><i class="bi bi-emoji-smile me-1"></i> Positif</span>';
        } else if (article.sentiment === 'Negatif') {
            sentimentBadge = '<span class="badge bg-danger shadow-sm rounded-pill px-3 py-2"><i class="bi bi-emoji-frown me-1"></i> Negatif</span>';
        } else {
            sentimentBadge = '<span class="badge bg-secondary shadow-sm rounded-pill px-3 py-2"><i class="bi bi-emoji-neutral me-1"></i> Netral</span>';
        }

        const imageStr = article.image || 'https://via.placeholder.com/600x300?text=No+Image';
        const srcName = article.source.name || 'Sumber Tidak Diketahui';
        
        // Ambil negara dari dropdown
        const countrySelect = document.getElementById('country');
        const countryText = countrySelect.options[countrySelect.selectedIndex].text;
        const displayCountry = countrySelect.value === 'world' ? 'Global' : countryText;

        html += `
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; overflow: hidden; transition: transform 0.2s;">
                <div class="position-relative">
                    <img src="${imageStr}" class="card-img-top" alt="News Image" style="height: 200px; object-fit: cover;">
                    <div class="position-absolute top-0 end-0 m-3">
                        ${sentimentBadge}
                    </div>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="badge bg-light text-primary border fw-semibold"><i class="bi bi-globe me-1"></i> ${srcName}</span>
                            <span class="badge bg-light text-secondary border fw-semibold ms-1"><i class="bi bi-geo-alt me-1"></i> ${displayCountry}</span>
                        </div>
                        <small class="text-muted fw-semibold" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i> ${article.published_formatted}</small>
                    </div>
                    <h5 class="fw-bold text-dark mb-3" style="line-height: 1.4;">${article.title}</h5>
                    <p class="text-muted small mb-4" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                        ${article.description}
                    </p>
                    <a href="${article.url}" target="_blank" class="btn btn-outline-primary mt-auto rounded-pill fw-bold w-100 shadow-sm" style="transition: all 0.3s;">
                        Baca Selengkapnya <i class="bi bi-box-arrow-up-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        `;
    });

    grid.innerHTML = html;
}
</script>
@endpush
