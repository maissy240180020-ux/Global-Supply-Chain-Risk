@extends('layouts.app')

@section('title', 'Pemantauan Berita Global')

@section('content')

<div class="container-fluid py-2">

    <!-- Title & Description Section -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark d-flex align-items-center gap-2 mb-1" style="font-size: 1.85rem;">
            📰 Pemantauan Berita Global
        </h2>
        <p class="text-secondary mb-0" style="font-size: 0.95rem;">
            Berita terkini mengenai Economy, Logistics, dan Geopolitics dunia — didukung oleh <strong>GNews API</strong>.
        </p>
    </div>

    <!-- Filter Card Section -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <!-- Form pencarian kategori berita -->
            <form action="" method="GET" id="newsFilterForm" class="row g-3 align-items-center">
                <!-- Dropdown Select Country -->
                <div class="col-md-5 col-12">
                    <select name="country" class="form-select text-dark" style="border-radius: 8px; border: 1px solid #cbd5e1; height: 42px; font-weight: 500;">
                        <option value="world" {{ $country === 'world' ? 'selected' : '' }}>🌍 Seluruh Dunia (Global)</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->country_name }}" {{ $country === $c->country_name ? 'selected' : '' }}>
                                {{ $c->country_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Dropdown Select Category -->
                <div class="col-md-5 col-12">
                    <select name="category" class="form-select text-dark" style="border-radius: 8px; border: 1px solid #cbd5e1; height: 42px; font-weight: 500;">
                        <option value="economy" {{ in_array($category, ['economy', 'ekonomi']) ? 'selected' : '' }}>🏦 Economy (Ekonomi)</option>
                        <option value="logistics" {{ in_array($category, ['logistics', 'logistik']) ? 'selected' : '' }}>🚚 Logistics (Logistik)</option>
                        <option value="geopolitics" {{ in_array($category, ['geopolitics', 'geopolitik']) ? 'selected' : '' }}>🌐 Geopolitics (Geopolitik)</option>
                    </select>
                </div>
                <!-- Search Button -->
                <div class="col-md-2 col-12">
                    <button type="submit" class="btn text-white w-100 fw-semibold d-flex align-items-center justify-content-center" style="background-color: #0b4635; border: none; height: 42px; border-radius: 8px; font-size: 0.95rem; transition: background-color 0.2s;">
                        Cari Berita
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- News Grid Section -->
    @if(count($berita) > 0)
        <div class="row g-4">
            @foreach($berita as $item)
                @php
                    // Normalisasi data agar kompatibel dengan BeritaController & NewsController
                    $title = $item['title'] ?? $item['judul'] ?? '';
                    $description = $item['description'] ?? $item['deskripsi'] ?? '';
                    $link = $item['url'] ?? $item['link'] ?? '#';
                    $publishedAt = $item['published_at'] ?? $item['tanggal'] ?? '';
                    $sourceName = $item['source_name'] ?? $item['source'] ?? 'Unknown';
                    $sentiment = $item['sentiment'] ?? 'Neutral';
                    $image = $item['image'] ?? '';

                    // Klasifikasi Badge Sentimen: Positif (hijau/success), Negatif (merah/danger), Netral (abu-abu/secondary)
                    if ($sentiment === 'Positive') {
                        $sBadgeText = 'Positive';
                        $sBadgeClass = 'bg-success text-white';
                    } elseif ($sentiment === 'Negative') {
                        $sBadgeText = 'Negative';
                        $sBadgeClass = 'bg-danger text-white';
                    } else {
                        $sBadgeText = 'Neutral';
                        $sBadgeClass = 'bg-secondary text-white';
                    }

                    // Normalisasi display nama kategori
                    $cat = strtolower($category);
                    if (in_array($cat, ['logistics', 'logistik'])) {
                        $displayCategory = '🚚 Logistics';
                    } elseif (in_array($cat, ['geopolitics', 'geopolitik'])) {
                        $displayCategory = '🌐 Geopolitics';
                    } else {
                        $displayCategory = '🏦 Economy';
                    }

                    // Batasi potongan deskripsi berita maksimal 150 karakter
                    $truncatedDesc = strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
                @endphp

                <div class="col-lg-6 col-12">
                    <div class="card h-100 border-0 shadow-sm news-grid-card d-flex flex-column" style="border-radius: 16px; overflow: hidden; background: #ffffff; transition: transform 0.2s, box-shadow 0.2s;">
                        
                        <!-- Thumbnail Image -->
                        <div style="height: 200px; width: 100%; overflow: hidden; background: #f1f5f9;">
                            @if(!empty($image))
                                <img src="{{ $image }}" alt="{{ $title }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                    <i class="bi bi-image fs-1"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            
                            <!-- Badges Row -->
                            <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
                                <!-- Source Badge -->
                                <span class="badge text-white px-2.5 py-1.5 font-weight-semibold" style="background-color: #212529; font-size: 0.72rem; border-radius: 6px;">
                                    {{ $sourceName }}
                                </span>
                                <!-- Category Badge -->
                                <span class="badge text-white px-2.5 py-1.5 font-weight-semibold" style="background-color: #0d6efd; font-size: 0.72rem; border-radius: 6px;">
                                    {{ $displayCategory }}
                                </span>
                                <!-- Sentiment Badge -->
                                <span class="badge px-2.5 py-1.5 font-weight-semibold {{ $sBadgeClass }}" style="font-size: 0.72rem; border-radius: 6px;">
                                    {{ $sBadgeText }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h5 class="fw-bold text-dark mb-2" style="font-size: 1.05rem; line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $title }}
                            </h5>

                            <!-- Description -->
                            <p class="text-secondary mb-3 flex-grow-1" style="font-size: 0.82rem; line-height: 1.55;">
                                {{ $truncatedDesc }}
                            </p>

                            <!-- Card Footer -->
                            <div class="mt-auto pt-3 border-top border-light d-flex justify-content-between align-items-center" style="font-size: 0.78rem;">
                                <span class="text-muted d-flex align-items-center gap-1">
                                    <i class="bi bi-clock"></i> {{ $publishedAt }}
                                </span>
                                <a href="{{ $link }}" target="_blank" class="btn btn-sm text-white fw-semibold d-flex align-items-center gap-1" style="background-color: #0b4635; border: none; border-radius: 6px; padding: 6px 12px; font-size: 0.78rem;">
                                    Baca Selengkapnya <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 16px;">
            <div class="card-body">
                <i class="bi bi-newspaper fs-1 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold text-secondary">Tidak Ada Berita Tersedia</h5>
                <p class="text-muted mb-0">Tidak dapat mengambil berita saat ini. Silakan periksa koneksi internet Anda atau coba lagi beberapa saat lagi.</p>
            </div>
        </div>
    @endif

</div>

<!-- Styles for Hover animations & Custom Styling -->
<style>
    .news-grid-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Memuat gambar asli artikel secara asinkron setelah halaman dimuat untuk Google News RSS
    document.querySelectorAll('.news-grid-card').forEach(function (card) {
        const link = card.querySelector('a.btn')?.getAttribute('href');
        const img = card.querySelector('img');
        
        if (img && link && link !== '#' && !link.includes('google.com/search')) {
            fetch(`/berita/fetch-image?url=${encodeURIComponent(link)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.image) {
                        img.src = data.image;
                    }
                })
                .catch(error => {
                    console.warn('Gagal memuat gambar asli:', error);
                });
        }
    });
});
</script>
@endpush