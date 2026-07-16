@extends('layouts.app')

@section('title', 'Perbandingan Negara | SIMRPG')

@section('content')

<div class="container-fluid">

    <!-- Header Section -->
    <div class="mb-4">
        <h2 class="fw-bold mb-0">⚖️ Perbandingan Risiko Negara (VS Mode)</h2>
        <p class="text-muted mb-0">Analisis Komparatif Kerentanan Rantai Pasok Global Antara Dua Negara</p>
    </div>

    <!-- Country Selector Form -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4 bg-white" style="border-radius: 16px;">
            <form action="{{ route('compare.compare') }}" method="POST">
                @csrf
                <div class="row g-3 align-items-end">
                    
                    <!-- Country 1 -->
                    <div class="col-lg-4 col-md-5 col-12">
                        <label for="country1" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Negara Pertama</label>
                        <select id="country1" name="country1" class="form-select border-light shadow-none" 
                                style="font-weight: 500; font-size: 0.88rem; background-color: #f8fafc; height: 42px; border-radius: 10px;">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                    @isset($countryA)
                                        {{ $countryA->id == $country->id ? 'selected' : '' }}
                                    @endisset>
                                    {{ $country->country_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Country 2 -->
                    <div class="col-lg-4 col-md-5 col-12">
                        <label for="country2" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">Negara Kedua</label>
                        <select id="country2" name="country2" class="form-select border-light shadow-none"
                                style="font-weight: 500; font-size: 0.88rem; background-color: #f8fafc; height: 42px; border-radius: 10px;">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                    @isset($countryB)
                                        {{ $countryB->id == $country->id ? 'selected' : '' }}
                                    @endisset>
                                    {{ $country->country_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-lg-4 col-md-2 col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn text-white flex-fill d-flex align-items-center justify-content-center gap-2" 
                                    style="background: linear-gradient(135deg, #475569 0%, #334155 100%); border: none; height: 42px; border-radius: 10px; font-weight: 600; font-size: 0.88rem; transition: all 0.2s;">
                                <i class="bi bi-bar-chart-fill"></i> Bandingkan
                            </button>
                            <button type="button" id="swapBtn" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" 
                                    style="width: 42px; height: 42px; border-radius: 10px; border-color: #cbd5e1; color: #475569;">
                                <i class="bi bi-arrow-left-right"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @if(isset($countryA) && isset($countryB))

        <!-- VS Visual Section -->
        <div class="row g-4 mb-4 align-items-stretch">
            
            <!-- Country A Profile -->
            <div class="col-md-5 col-12">
                <div class="card border-0 shadow-sm h-100 p-4 text-center bg-white" style="border-radius: 16px;">
                    <div class="d-flex flex-column align-items-center">
                        <img src="{{ $countryA->flag ?? 'https://flagcdn.com/w320/un.png' }}" alt="{{ $countryA->country_name }}"
                             style="width: 100px; height: 65px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.06);">
                        <h4 class="fw-bold text-dark mb-1">{{ $countryA->country_name }}</h4>
                        <span class="badge bg-secondary mb-3 px-2.5 py-1" style="font-size: 0.72rem; background-color: #64748b !important;">
                            {{ $countryA->country_code }}
                        </span>

                        <!-- Risk Dial -->
                        <div class="my-3 position-relative d-flex align-items-center justify-content-center rounded-circle"
                             style="width: 110px; height: 110px; border: 8px solid #f1f5f9; background-color: #fff; box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);">
                            @php
                                $aColor = '#10b981'; // Low
                                if ($countryA->risk_level == 'Medium') $aColor = '#f59e0b';
                                elseif ($countryA->risk_level == 'High') $aColor = '#ef4444';
                            @endphp
                            <!-- Border color status -->
                            <div class="position-absolute w-100 h-100 rounded-circle" style="border: 8px solid {{ $aColor }}; margin: -8px; opacity: 0.15;"></div>
                            <div class="text-center">
                                <span class="fw-bold text-dark" style="font-size: 1.8rem; line-height: 1;">{{ round($countryA->risk_score) }}</span>
                                <small class="text-muted d-block" style="font-size: 0.65rem; font-weight:600; text-transform: uppercase;">RISK</small>
                            </div>
                        </div>

                        <!-- Mini Stats -->
                        <div class="w-100 row g-2 mt-3 text-center border-top border-light pt-3">
                            <div class="col-4 border-end border-light">
                                <small class="text-muted d-block" style="font-size: 0.65rem;">Ibu Kota</small>
                                <span class="fw-semibold text-dark" style="font-size: 0.78rem;">{{ $countryA->capital ?? '-' }}</span>
                            </div>
                            <div class="col-4 border-end border-light">
                                <small class="text-muted d-block" style="font-size: 0.65rem;">Mata Uang</small>
                                <span class="fw-semibold text-dark" style="font-size: 0.78rem;">{{ $countryA->currency }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block" style="font-size: 0.65rem;">Pelabuhan</small>
                                <span class="fw-bold text-dark" style="font-size: 0.78rem;">{{ $countryA->port_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VS Circle Badge -->
            <div class="col-md-2 col-12 d-flex align-items-center justify-content-center my-md-0 my-3">
                <div class="rounded-circle shadow d-flex align-items-center justify-content-center text-white fw-bold"
                     style="width: 64px; height: 64px; background: linear-gradient(135deg, #475569, #1e293b); font-size: 1.4rem; border: 4px solid #fff;">
                    VS
                </div>
            </div>

            <!-- Country B Profile -->
            <div class="col-md-5 col-12">
                <div class="card border-0 shadow-sm h-100 p-4 text-center bg-white" style="border-radius: 16px;">
                    <div class="d-flex flex-column align-items-center">
                        <img src="{{ $countryB->flag ?? 'https://flagcdn.com/w320/un.png' }}" alt="{{ $countryB->country_name }}"
                             style="width: 100px; height: 65px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.06);">
                        <h4 class="fw-bold text-dark mb-1">{{ $countryB->country_name }}</h4>
                        <span class="badge bg-secondary mb-3 px-2.5 py-1" style="font-size: 0.72rem; background-color: #64748b !important;">
                            {{ $countryB->country_code }}
                        </span>

                        <!-- Risk Dial -->
                        <div class="my-3 position-relative d-flex align-items-center justify-content-center rounded-circle"
                             style="width: 110px; height: 110px; border: 8px solid #f1f5f9; background-color: #fff; box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);">
                            @php
                                $bColor = '#10b981'; // Low
                                if ($countryB->risk_level == 'Medium') $bColor = '#f59e0b';
                                elseif ($countryB->risk_level == 'High') $bColor = '#ef4444';
                            @endphp
                            <div class="position-absolute w-100 h-100 rounded-circle" style="border: 8px solid {{ $bColor }}; margin: -8px; opacity: 0.15;"></div>
                            <div class="text-center">
                                <span class="fw-bold text-dark" style="font-size: 1.8rem; line-height: 1;">{{ round($countryB->risk_score) }}</span>
                                <small class="text-muted d-block" style="font-size: 0.65rem; font-weight:600; text-transform: uppercase;">RISK</small>
                            </div>
                        </div>

                        <!-- Mini Stats -->
                        <div class="w-100 row g-2 mt-3 text-center border-top border-light pt-3">
                            <div class="col-4 border-end border-light">
                                <small class="text-muted d-block" style="font-size: 0.65rem;">Ibu Kota</small>
                                <span class="fw-semibold text-dark" style="font-size: 0.78rem;">{{ $countryB->capital ?? '-' }}</span>
                            </div>
                            <div class="col-4 border-end border-light">
                                <small class="text-muted d-block" style="font-size: 0.65rem;">Mata Uang</small>
                                <span class="fw-semibold text-dark" style="font-size: 0.78rem;">{{ $countryB->currency }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block" style="font-size: 0.65rem;">Pelabuhan</small>
                                <span class="fw-bold text-dark" style="font-size: 0.78rem;">{{ $countryB->port_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Comparative Metrics Table & Chart -->
        <div class="row g-4 mb-4">
            
            <!-- Table Card -->
            <div class="col-xl-7 col-12">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-header bg-white py-3 border-light">
                        <h6 class="fw-bold mb-0 text-dark">📋 Perbandingan Parameter Metrik</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="table-light text-secondary">
                                    <tr>
                                        <th class="ps-4" width="30%">Parameter</th>
                                        <th width="35%">{{ $countryA->country_name }}</th>
                                        <th width="35%">{{ $countryB->country_name }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">Ibu Kota</td>
                                        <td class="text-dark fw-semibold">{{ $countryA->capital ?? '-' }}</td>
                                        <td class="text-dark fw-semibold">{{ $countryB->capital ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">Mata Uang</td>
                                        <td class="text-dark fw-bold">{{ $countryA->currency }}</td>
                                        <td class="text-dark fw-bold">{{ $countryB->currency }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">Skor Risiko</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <strong style="color: {{ $aColor }}; font-size: 0.95rem;">{{ number_format($countryA->risk_score, 1) }}</strong>
                                                <span class="text-muted" style="font-size:0.72rem;">/100</span>
                                            </div>
                                            <div class="progress" style="height: 6px; border-radius: 10px;">
                                                <div class="progress-bar" style="background-color: {{ $aColor }}; width: {{ $countryA->risk_score }}%;"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <strong style="color: {{ $bColor }}; font-size: 0.95rem;">{{ number_format($countryB->risk_score, 1) }}</strong>
                                                <span class="text-muted" style="font-size:0.72rem;">/100</span>
                                            </div>
                                            <div class="progress" style="height: 6px; border-radius: 10px;">
                                                <div class="progress-bar" style="background-color: {{ $bColor }}; width: {{ $countryB->risk_score }}%;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">Level Risiko</td>
                                        <td>
                                            @php
                                                $badgeA = $countryA->risk_level == 'High' ? 'bg-danger text-white' : ($countryA->risk_level == 'Medium' ? 'bg-warning text-dark' : 'bg-success text-white');
                                                $badgeB = $countryB->risk_level == 'High' ? 'bg-danger text-white' : ($countryB->risk_level == 'Medium' ? 'bg-warning text-dark' : 'bg-success text-white');
                                            @endphp
                                            <span class="badge {{ $badgeA }} px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">{{ $countryA->risk_level }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $badgeB }} px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">{{ $countryB->risk_level }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">Temperatur</td>
                                        <td class="text-dark fw-semibold">{{ $countryA->temperature ?? '-' }} °C</td>
                                        <td class="text-dark fw-semibold">{{ $countryB->temperature ?? '-' }} °C</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">Kondisi Cuaca</td>
                                        <td class="text-secondary fw-semibold">{{ $countryA->weather ?? '-' }}</td>
                                        <td class="text-secondary fw-semibold">{{ $countryB->weather ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">Total Pelabuhan</td>
                                        <td class="text-dark fw-bold">{{ $countryA->port_count ?? 0 }}</td>
                                        <td class="text-dark fw-bold">{{ $countryB->port_count ?? 0 }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grouped Bar Chart -->
            <div class="col-xl-5 col-12">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                    <div class="card-header bg-white py-3 border-light">
                        <h6 class="fw-bold mb-0 text-dark">📊 Grafik Komparatif Side-by-Side</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                        <div style="width: 100%; max-height: 260px;">
                            <canvas id="groupedCompareChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @php
            $riskDiff = abs($countryA->risk_score - $countryB->risk_score);
            $tempDiff = abs($countryA->temperature - $countryB->temperature);
            $portDiff = abs(($countryA->port_count ?? 0) - ($countryB->port_count ?? 0));
            
            // Determine lower risk country as the logistical recommendation
            $recommendedCountry = $countryA;
            if ($countryB->risk_score < $countryA->risk_score) {
                $recommendedCountry = $countryB;
            }
        @endphp

        <!-- AI Supply Chain Recommendation Box (Slate Theme) -->
        <div class="card border-0 shadow-sm text-white mb-4" style="border-radius: 16px; background-color: #0f172a;">
            <div class="card-header bg-transparent border-bottom py-3" style="border-color: rgba(255,255,255,0.08) !important;">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    🤖 AI Supply Chain Intelligence Insight
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-lg-6 col-12">
                        <h6 class="text-secondary fw-semibold text-uppercase mb-2" style="font-size: 0.68rem; letter-spacing: 0.05em; color: #94a3b8 !important;">Analisis Perbedaan Risiko</h6>
                        <p class="mb-3" style="font-size: 0.85rem; line-height: 1.6; color: #cbd5e1;">
                            Negara <strong>{{ $countryA->country_name }}</strong> menunjukkan tingkat risiko rantai pasok sebesar <strong>{{ number_format($countryA->risk_score, 1) }}%</strong>, sementara <strong>{{ $countryB->country_name }}</strong> berada pada level risiko <strong>{{ $countryB->risk_level }}</strong> dengan skor <strong>{{ number_format($countryB->risk_score, 1) }}%</strong>. Terdapat selisih tingkat kerentanan sebesar <strong>{{ $riskDiff }}%</strong> di antara kedua wilayah ini.
                        </p>
                        <div class="d-flex flex-wrap gap-2 text-center" style="font-size: 0.72rem;">
                            <span class="badge px-3 py-1.5" style="background-color: rgba(255,255,255,0.08); border-radius: 6px;">📈 Selisih Risiko: {{ $riskDiff }}%</span>
                            <span class="badge px-3 py-1.5" style="background-color: rgba(255,255,255,0.08); border-radius: 6px;">🌡 Selisih Suhu: {{ $tempDiff }} °C</span>
                            <span class="badge px-3 py-1.5" style="background-color: rgba(255,255,255,0.08); border-radius: 6px;">⚓ Selisih Pelabuhan: {{ $portDiff }}</span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12 border-start-lg border-light" style="border-color: rgba(255,255,255,0.08) !important;">
                        <h6 class="text-secondary fw-semibold text-uppercase mb-2" style="font-size: 0.68rem; letter-spacing: 0.05em; color: #10b981 !important;">Rekomendasi Jalur Logistik</h6>
                        <div class="p-3 mb-0" style="border-radius: 12px; background-color: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.15);">
                            <p class="mb-0 text-white" style="font-size: 0.85rem; line-height: 1.6;">
                                Berdasarkan data realtime, 🏆 <strong>{{ $recommendedCountry->country_name }}</strong> direkomendasikan sebagai prioritas jalur rantai pasok atau operasional perdagangan karena memiliki skor risiko yang lebih rendah (<strong>{{ number_format($recommendedCountry->risk_score, 1) }}%</strong>). Hal ini mengindikasikan tingkat stabilitas logistik, cuaca, dan iklim ekonomi yang lebih kondusif dibandingkan dengan negara pembanding.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Swap Button Logic
    document.getElementById('swapBtn').addEventListener('click', function(){
        let a = document.getElementById('country1');
        let b = document.getElementById('country2');
        let temp = a.value;
        a.value = b.value;
        b.value = temp;
    });

    @if(isset($countryA) && isset($countryB))
        // 2. Chart.js Grouped Bar Chart Setup
        const nameA = '{{ $countryA->country_name }}';
        const nameB = '{{ $countryB->country_name }}';
        
        new Chart(document.getElementById('groupedCompareChart'), {
            type: 'bar',
            data: {
                labels: ['Skor Risiko', 'Temperatur (°C)', 'Pelabuhan'],
                datasets: [
                    {
                        label: nameA,
                        data: [
                            {{ $countryA->risk_score }},
                            {{ $countryA->temperature ?? 25 }},
                            {{ $countryA->port_count ?? 0 }}
                        ],
                        backgroundColor: 'rgba(71, 85, 105, 0.8)', // Slate-Grey A
                        borderColor: '#475569',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: nameB,
                        data: [
                            {{ $countryB->risk_score }},
                            {{ $countryB->temperature ?? 25 }},
                            {{ $countryB->port_count ?? 0 }}
                        ],
                        backgroundColor: 'rgba(30, 41, 59, 0.85)', // Dark Slate-Grey B
                        borderColor: '#1e293b',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Poppins', size: 10 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { family: 'Poppins', size: 10 } }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { family: 'Poppins', size: 10 }
                        }
                    }
                }
            }
        });
    @endif
});
</script>
@endpush