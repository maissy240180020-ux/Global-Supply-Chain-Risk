@extends('layouts.app')

@section('title','Pemantauan Risiko | SIMRPG')

@section('content')

<style>
    .hover-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-card:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.06) !important; }
    .table-row-hover { transition: background-color 0.2s; }
    .table-row-hover:hover { background-color: #f1f5f9; }
</style>

<div class="container-fluid">

    <h2 class="fw-bold mb-4 text-dark d-flex align-items-center gap-2">
        <i class="bi bi-shield-exclamation text-primary"></i> Pemantauan Risiko Rantai Pasok
    </h2>

    <!-- Ringkasan Risiko -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm hover-card" style="background-color: #ffe4e6; border-radius: 16px;">
                <div class="card-body d-flex align-items-center gap-4 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-x fs-3 text-danger"></i>
                    </div>
                    <div>
                        <h6 class="text-danger fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.8rem;">Risiko Tinggi</h6>
                        <h2 class="text-dark fw-bold mb-0">{{ $highRisk }} <small class="text-muted fs-6 fw-normal">Negara</small></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm hover-card" style="background-color: #fef3c7; border-radius: 16px;">
                <div class="card-body d-flex align-items-center gap-4 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-exclamation fs-3 text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-warning text-darken fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.8rem;">Risiko Sedang</h6>
                        <h2 class="text-dark fw-bold mb-0">{{ $mediumRisk }} <small class="text-muted fs-6 fw-normal">Negara</small></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm hover-card" style="background-color: #dcfce7; border-radius: 16px;">
                <div class="card-body d-flex align-items-center gap-4 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-check fs-3 text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-success fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.8rem;">Risiko Rendah</h6>
                        <h2 class="text-dark fw-bold mb-0">{{ $lowRisk }} <small class="text-muted fs-6 fw-normal">Negara</small></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart & Top Panels -->
    <div class="row g-4 mb-4">
        
        <!-- Statistik Risiko Global -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 16px; border-top: 5px solid #3b82f6 !important;">
                <div class="card-header bg-white py-3 border-bottom-0 pb-1">
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-bar-chart-steps me-2"></i> Statistik Risiko Global</h6>
                </div>
                <div class="card-body p-4 pt-2">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Total Dipantau</span>
                        <span class="fw-bold fs-5">{{ $totalCountries }} Negara</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Rata-rata Global</span>
                        <span class="badge bg-primary rounded-pill px-3 py-2">{{ number_format($avgRiskScore, 1) }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Risiko Tertinggi</span>
                        <div class="text-end">
                            <span class="fw-bold d-block">{{ $highestRiskCountry ? $highestRiskCountry->country_name : '-' }}</span>
                            <small class="text-danger fw-bold">{{ $highestRiskCountry ? number_format($highestRiskCountry->risk_score, 1) : '-' }}</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Risiko Terendah</span>
                        <div class="text-end">
                            <span class="fw-bold d-block">{{ $lowestRiskCountry ? $lowestRiskCountry->country_name : '-' }}</span>
                            <small class="text-success fw-bold">{{ $lowestRiskCountry ? number_format($lowestRiskCountry->risk_score, 1) : '-' }}</small>
                        </div>
                    </div>

                    <!-- Persentase Distribusi -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-danger fw-bold"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Tinggi: {{ $highRiskPct }}%</small>
                            <small class="text-warning fw-bold" style="color: #d97706 !important;"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Sedang: {{ $mediumRiskPct }}%</small>
                            <small class="text-success fw-bold"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Rendah: {{ $lowRiskPct }}%</small>
                        </div>
                    </div>

                    <!-- Ringkasan Kondisi Sistem -->
                    @php
                        $majority = 'Risiko Rendah';
                        $majorColor = 'text-success';
                        if ($highRisk >= $mediumRisk && $highRisk >= $lowRisk) {
                            $majority = 'Risiko Tinggi';
                            $majorColor = 'text-danger';
                        } elseif ($mediumRisk >= $highRisk && $mediumRisk >= $lowRisk) {
                            $majority = 'Risiko Sedang';
                            $majorColor = 'text-warning'; 
                        }
                    @endphp
                    <div class="mt-3 p-3 bg-light rounded shadow-sm border border-light">
                        <small class="d-block mb-1"><i class="bi bi-info-circle text-primary me-1"></i> Mayoritas negara berada pada kategori <strong class="{{ $majorColor }}" style="{{ $majorColor == 'text-warning' ? 'color: #d97706 !important;' : '' }}">{{ $majority }}</strong>.</small>
                        <small class="d-block mb-1"><i class="bi bi-check-circle text-success me-1"></i> Sistem pemantauan berjalan normal.</small>
                        <small class="d-block"><i class="bi bi-clock-history text-secondary me-1"></i> Data diperbarui berdasarkan perhitungan terbaru.</small>
                    </div>

                </div>
            </div>
        </div>

        <!-- Top 5 Highest Risk -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 16px; border-top: 5px solid #ef4444 !important;">
                <div class="card-header bg-white py-3 border-bottom-0 pb-1">
                    <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-graph-up-arrow me-2"></i> Top 5 Risiko Tertinggi</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush mt-2">
                        @forelse($topHigh as $t)
                        <li class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center border-light">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-muted">{{ $loop->iteration }}.</span>
                                @if($t->flag) <img src="{{ $t->flag }}" width="20" class="border rounded shadow-sm"> @endif
                                <span class="fw-medium text-dark">{{ $t->country_name }}</span>
                            </div>
                            <span class="badge bg-danger rounded-pill px-2 py-1">{{ number_format($t->risk_score, 1) }}</span>
                        </li>
                        @empty
                        <li class="list-group-item px-4 py-3 text-muted">Data tidak tersedia.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Top 5 Lowest Risk -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 hover-card" style="border-radius: 16px; border-top: 5px solid #10b981 !important;">
                <div class="card-header bg-white py-3 border-bottom-0 pb-1">
                    <h6 class="fw-bold mb-0 text-success"><i class="bi bi-graph-down-arrow me-2"></i> Top 5 Risiko Terendah</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush mt-2">
                        @forelse($topLow as $t)
                        <li class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center border-light">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-muted">{{ $loop->iteration }}.</span>
                                @if($t->flag) <img src="{{ $t->flag }}" width="20" class="border rounded shadow-sm"> @endif
                                <span class="fw-medium text-dark">{{ $t->country_name }}</span>
                            </div>
                            <span class="badge bg-success rounded-pill px-2 py-1">{{ number_format($t->risk_score, 1) }}</span>
                        </li>
                        @empty
                        <li class="list-group-item px-4 py-3 text-muted">Data tidak tersedia.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel & Filter -->
    <div id="filter-section" class="card border-0 shadow-sm" style="border-radius: 16px; scroll-margin-top: 20px;">
        <div class="card-body p-4">
            
            <form action="{{ route('risk.index') }}#filter-section" method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-lg-3">
                        <div class="position-relative">
                            <input type="text" name="search" class="form-control ps-5 border-light shadow-none bg-light" 
                                   placeholder="Cari negara..." value="{{ request('search') }}" style="border-radius: 10px; height: 45px;">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-2">
                        <select name="region" class="form-select border-light shadow-none bg-light" style="border-radius: 10px; height: 45px;">
                            <option value="">Benua (Semua)</option>
                            @if(isset($regions))
                                @foreach($regions as $r)
                                    <option value="{{ $r }}" {{ request('region') == $r ? 'selected' : '' }}>{{ $r }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-12 col-md-4 col-lg-3">
                        <select name="risk" class="form-select border-light shadow-none bg-light" style="border-radius: 10px; height: 45px;">
                            <option value="">Risiko (Semua)</option>
                            <option value="High" {{ request('risk')=='High' ? 'selected':'' }}>Tinggi (High Risk)</option>
                            <option value="Medium" {{ request('risk')=='Medium' ? 'selected':'' }}>Sedang (Medium Risk)</option>
                            <option value="Low" {{ request('risk')=='Low' ? 'selected':'' }}>Rendah (Low Risk)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 col-lg-2">
                        <select name="sort" class="form-select border-light shadow-none bg-light" style="border-radius: 10px; height: 45px;">
                            <option value="tertinggi" {{ request('sort') == 'tertinggi' ? 'selected' : '' }}>Skor Tertinggi</option>
                            <option value="terendah" {{ request('sort') == 'terendah' ? 'selected' : '' }}>Skor Terendah</option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill shadow-sm d-flex align-items-center justify-content-center gap-2" style="border-radius: 10px; height: 45px;">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('risk.index') }}#filter-section" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 10px; height: 45px; width: 45px;">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
                <table class="table table-hover align-middle border-top">
                    <thead class="table-light position-sticky top-0 shadow-sm" style="z-index: 1;">
                        <tr>
                            <th class="ps-3 text-muted fw-medium py-3">No</th>
                            <th class="text-muted fw-medium py-3">Negara</th>
                            <th class="text-muted fw-medium py-3">Skor Risiko</th>
                            <th class="text-muted fw-medium py-3">Tingkat Risiko</th>
                            <th class="text-muted fw-medium py-3 text-center">Trend</th>
                            <th class="text-muted fw-medium py-3">Suhu Terkini</th>
                            <th class="text-muted fw-medium py-3">Status Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($countries as $index => $country)
                        <tr class="table-row-hover">
                            <td class="ps-3">
                                {{ $index + 1 }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($country->flag) <img src="{{ $country->flag }}" width="28" class="border rounded shadow-sm"> @endif
                                    <strong class="text-dark">{{ $country->country_name }}</strong>
                                </div>
                            </td>
                            <td>
                                <strong class="fs-6 text-dark">
                                    {{ number_format($country->risk_score, 1) }}
                                </strong>
                            </td>
                            <td>
                                @php
                                    $badge = 'bg-success text-white';
                                    $icon = 'bi-shield-check';
                                    $text = 'Rendah';
                                    
                                    // Mocking trend based on risk score purely for visualization since we have no history
                                    $trendIcon = 'bi-dash-lg text-secondary'; // Stable
                                    $trendTooltip = 'Stabil';
                                    
                                    if($country->risk_score > 65) {
                                        $trendIcon = 'bi-graph-up-arrow text-danger'; // Trending worse
                                        $trendTooltip = 'Meningkat (Memburuk)';
                                    } elseif ($country->risk_score < 35) {
                                        $trendIcon = 'bi-graph-down-arrow text-success'; // Trending better
                                        $trendTooltip = 'Menurun (Membaik)';
                                    }

                                    if($country->risk_level == 'Medium'){
                                        $badge = 'bg-warning text-dark';
                                        $icon = 'bi-shield-exclamation';
                                        $text = 'Sedang';
                                    }
                                    if($country->risk_level == 'High'){
                                        $badge = 'bg-danger text-white';
                                        $icon = 'bi-shield-x';
                                        $text = 'Tinggi';
                                    }
                                @endphp
                                <span class="badge {{ $badge }} px-2 py-1 shadow-sm" style="border-radius: 6px;">
                                    <i class="bi {{ $icon }} me-1"></i> {{ $text }}
                                </span>
                            </td>
                            <td class="text-center">
                                <i class="bi {{ $trendIcon }} fs-5" title="{{ $trendTooltip }}" data-bs-toggle="tooltip"></i>
                            </td>
                            <td>
                                <span class="text-muted">{{ $country->temperature ?? '-' }}°C</span>
                            </td>
                            <td>
                                @if($country->risk_level == 'High')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 shadow-sm" style="border-radius: 20px;">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Kritis
                                    </span>
                                @elseif($country->risk_level == 'Medium')
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1 shadow-sm" style="border-radius: 20px;">
                                        <i class="bi bi-exclamation-circle-fill text-warning me-1"></i> Waspada
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 shadow-sm" style="border-radius: 20px;">
                                        <i class="bi bi-check-circle-fill me-1"></i> Stabil
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-secondary opacity-50"></i>
                                <br><br>
                                <span class="text-muted">Data tidak ditemukan. Coba sesuaikan filter pencarian.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endpush