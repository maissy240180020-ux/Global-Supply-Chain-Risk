@extends('layouts.app')

@section('title', 'News Intelligence - Analisis Berita Supply Chain')

@section('content')

<div class="container-fluid">

    <!-- Header & Country Selector -->
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0">📰 News Intelligence Center</h2>
            <p class="text-muted mb-0">Analisis Sentimen Berita Rantai Pasok Global Secara Realtime Berbasis Leksikon AI</p>
        </div>

        <div class="bg-white p-2 rounded shadow-sm border border-light d-flex align-items-center gap-2">
            <label for="country_id" class="fw-semibold text-secondary mb-0">Negara:</label>
            <form action="{{ route('berita.index') }}" method="GET" id="countryForm" class="m-0">
                <select name="country_id" id="country_id" class="form-select form-select-sm border-0 bg-light"
                        style="font-weight: 500;" onchange="this.form.submit()">
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ $selectedCountry && $selectedCountry->id == $c->id ? 'selected' : '' }}>
                            {{ $c->country_name }} ({{ $c->country_code }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Sentiment Overview Cards -->
    @php
        $totalArticles = count($berita);
        $positiveCount = collect($berita)->where('sentiment', 'Positive')->count();
        $neutralCount  = collect($berita)->where('sentiment', 'Neutral')->count();
        $negativeCount = collect($berita)->where('sentiment', 'Negative')->count();

        if ($sentiment === 'Positive') {
            $sentimentColor = '#10b981';
            $sentimentBg    = 'rgba(16, 185, 129, 0.10)';
            $sentimentIcon  = 'bi-emoji-smile-fill';
            $sentimentLabel = 'POSITIF';
        } elseif ($sentiment === 'Negative') {
            $sentimentColor = '#ef4444';
            $sentimentBg    = 'rgba(239, 68, 68, 0.10)';
            $sentimentIcon  = 'bi-emoji-frown-fill';
            $sentimentLabel = 'NEGATIF';
        } else {
            $sentimentColor = '#f59e0b';
            $sentimentBg    = 'rgba(245, 158, 11, 0.10)';
            $sentimentIcon  = 'bi-emoji-neutral-fill';
            $sentimentLabel = 'NETRAL';
        }
    @endphp

    <div class="row g-3 mb-4">

        <!-- Overall Sentiment -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                         style="width: 54px; height: 54px; background-color: {{ $sentimentBg }}; color: {{ $sentimentColor }};">
                        <i class="bi {{ $sentimentIcon }} fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">Sentimen Keseluruhan</small>
                        <h4 class="fw-bold mb-0 mt-1" style="color: {{ $sentimentColor }};">{{ $sentimentLabel }}</h4>
                        <small class="text-muted">{{ $totalArticles }} berita dianalisis</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Positive -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                         style="width: 54px; height: 54px; background-color: rgba(16, 185, 129, 0.10); color: #10b981;">
                        <i class="bi bi-arrow-up-circle-fill fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">Berita Positif</small>
                        <h4 class="fw-bold mb-0 mt-1 text-dark">{{ $positiveCount }}</h4>
                        <small class="text-muted">Kata kunci: growth, recovery, profit...</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Neutral -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                         style="width: 54px; height: 54px; background-color: rgba(245, 158, 11, 0.10); color: #f59e0b;">
                        <i class="bi bi-dash-circle-fill fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">Berita Netral</small>
                        <h4 class="fw-bold mb-0 mt-1 text-dark">{{ $neutralCount }}</h4>
                        <small class="text-muted">Tidak terdeteksi polaritas kata</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Negative -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                         style="width: 54px; height: 54px; background-color: rgba(239, 68, 68, 0.10); color: #ef4444;">
                        <i class="bi bi-arrow-down-circle-fill fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">Berita Negatif</small>
                        <h4 class="fw-bold mb-0 mt-1 text-dark">{{ $negativeCount }}</h4>
                        <small class="text-muted">Kata kunci: risk, crisis, shortage...</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- News Feed Column -->
        <div class="col-lg-8 col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-light d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-rss-fill text-primary"></i>
                        Live News Feed — {{ $selectedCountry->country_name ?? 'Global' }}
                    </h5>
                    @if($selectedCountry && $selectedCountry->flag)
                        <img src="{{ $selectedCountry->flag }}" alt="{{ $selectedCountry->country_name }}" style="height: 22px; border-radius: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.15);">
                    @endif
                </div>

                <div class="card-body p-0">
                    @if(count($berita) > 0)
                        @foreach($berita as $item)
                            @php
                                if ($item['sentiment'] === 'Positive') {
                                    $sColor = '#10b981'; $sBg = 'rgba(16, 185, 129, 0.08)'; $sBadge = 'POSITIF'; $sIcon = 'bi-arrow-up-circle-fill';
                                } elseif ($item['sentiment'] === 'Negative') {
                                    $sColor = '#ef4444'; $sBg = 'rgba(239, 68, 68, 0.08)'; $sBadge = 'NEGATIF'; $sIcon = 'bi-arrow-down-circle-fill';
                                } else {
                                    $sColor = '#f59e0b'; $sBg = 'rgba(245, 158, 11, 0.08)'; $sBadge = 'NETRAL'; $sIcon = 'bi-dash-circle-fill';
                                }
                            @endphp
                            <div class="px-4 py-3 border-bottom border-light news-item d-flex gap-3 align-items-start"
                                 style="transition: background-color 0.2s; background-color: {{ $sBg }};"
                                 onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='{{ $sBg }}'">

                                <!-- Sentiment Dot -->
                                <div class="mt-1 flex-shrink-0">
                                    <i class="bi {{ $sIcon }}" style="color: {{ $sColor }}; font-size: 1.1rem;"></i>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                                        <h6 class="fw-semibold text-dark mb-1" style="font-size: 0.9rem; line-height: 1.4;">
                                            {{ $item['judul'] }}
                                        </h6>
                                        <span class="badge rounded-pill fw-bold flex-shrink-0"
                                              style="background-color: {{ $sColor }}1A; color: {{ $sColor }}; font-size: 0.65rem; letter-spacing: 0.05em; border: 1px solid {{ $sColor }}33; white-space: nowrap;">
                                            {{ $sBadge }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 mt-1 flex-wrap">
                                        <span class="text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-clock"></i> {{ $item['tanggal'] }}
                                        </span>
                                        <span class="text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-newspaper"></i> {{ $item['source'] }}
                                        </span>
                                        <a href="{{ $item['link'] }}" target="_blank" class="d-flex align-items-center gap-1 fw-semibold"
                                           style="font-size: 0.75rem; color: #475569; text-decoration: none;">
                                            <i class="bi bi-box-arrow-up-right"></i> Baca Selengkapnya
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-5 text-center">
                            <i class="bi bi-newspaper fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="fw-bold text-secondary">Tidak Ada Berita Tersedia</h5>
                            <p class="text-muted">Tidak dapat mengambil berita dari sumber RSS saat ini. Periksa koneksi internet Anda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4 col-12 d-flex flex-column gap-4">

            <!-- Sentiment Chart -->
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-light">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-pie-chart-fill text-primary"></i> Distribusi Sentimen
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div style="height: 200px; position: relative;">
                        <canvas id="sentimentChart"></canvas>
                    </div>
                    <div class="mt-3 d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.8rem;">
                                <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background: #10b981;"></span> Positif
                            </span>
                            <span class="fw-bold" style="color: #10b981;">{{ $positiveCount }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.8rem;">
                                <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background: #f59e0b;"></span> Netral
                            </span>
                            <span class="fw-bold" style="color: #f59e0b;">{{ $neutralCount }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.8rem;">
                                <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background: #ef4444;"></span> Negatif
                            </span>
                            <span class="fw-bold" style="color: #ef4444;">{{ $negativeCount }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supply Chain Risk Implication -->
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-light d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-shield-shaded text-primary"></i> Implikasi Risiko
                    </h5>
                    <span class="badge fw-bold rounded-pill px-2 py-1"
                          style="background-color: {{ $sentimentBg }}; color: {{ $sentimentColor }}; font-size: 0.68rem; letter-spacing: 0.03em;">
                        {{ $sentimentLabel }}
                    </span>
                </div>
                <div class="card-body p-4" style="background-color: {{ $sentimentBg }}; border-top: 1px solid {{ $sentimentColor }}20;">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi {{ $sentimentIcon }} fs-2 flex-shrink-0" style="color: {{ $sentimentColor }};"></i>
                        <div>
                            @if($sentiment === 'Positive')
                                <h6 class="fw-bold mb-1" style="color: #065f46;">✅ Iklim Rantai Pasok Kondusif</h6>
                                <p class="text-dark mb-0" style="font-size: 0.82rem; line-height: 1.6;">
                                    Mayoritas berita supply chain terkini di <strong>{{ $selectedCountry->country_name ?? 'negara ini' }}</strong> bernada positif (growth, recovery, improvement). Ini merupakan sinyal awal yang baik bagi kelancaran rantai logistik. Waktu yang tepat untuk mempercepat jadwal pengiriman atau negosiasi kontrak baru.
                                </p>
                            @elseif($sentiment === 'Negative')
                                <h6 class="fw-bold mb-1 text-danger">⚠️ Waspada Gangguan Rantai Pasok</h6>
                                <p class="text-dark mb-0" style="font-size: 0.82rem; line-height: 1.6;">
                                    Berita supply chain terbaru di <strong>{{ $selectedCountry->country_name ?? 'negara ini' }}</strong> mayoritas bernada negatif (risk, crisis, shortage). Disarankan untuk meninjau rencana kontingensi pengiriman, mengaktifkan rute logistik alternatif, dan mempertimbangkan penundaan order baru sementara waktu.
                                </p>
                            @else
                                <h6 class="fw-bold mb-1" style="color: #92400e;">📊 Situasi Rantai Pasok Dalam Pemantauan</h6>
                                <p class="text-dark mb-0" style="font-size: 0.82rem; line-height: 1.6;">
                                    Berita supply chain di <strong>{{ $selectedCountry->country_name ?? 'negara ini' }}</strong> belum menunjukkan sinyal yang kuat ke arah tertentu. Pantau berita berikutnya secara berkala dan pertahankan kesiapan rencana cadangan untuk mengantisipasi perubahan kondisi.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('sentimentChart');
    if (!ctx || typeof window.Chart === 'undefined') return;

    new window.Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Positif', 'Netral', 'Negatif'],
            datasets: [{
                data: [{{ $positiveCount }}, {{ $neutralCount }}, {{ $negativeCount }}],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` ${context.label}: ${context.raw} berita`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush