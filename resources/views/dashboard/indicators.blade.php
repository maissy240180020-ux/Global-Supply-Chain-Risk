<div class="card dashboard-card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            📊 Global Country Dashboard & Risk Score: <strong>{{ $selectedCountry->country_name }}</strong>
        </h5>
        @if($selectedCountry->flag)
            <img src="{{ $selectedCountry->flag }}" alt="{{ $selectedCountry->country_name }}" style="height:25px; border-radius:3px; box-shadow:0 1px 3px rgba(0,0,0,0.15);">
        @endif
    </div>

    <div class="card-body">
        <!-- Grid Indikator Ekonomi Utama (Fitur 1) -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="indicator-box p-3 border rounded text-center">
                    <small class="text-muted d-block mb-1">GDP Negara</small>
                    <h4 class="fw-bold mb-0">
                        @if($selectedCountry->gdp >= 1e12)
                            ${{ number_format($selectedCountry->gdp / 1e12, 2) }} T
                        @else
                            ${{ number_format($selectedCountry->gdp / 1e9, 2) }} B
                        @endif
                    </h4>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="indicator-box p-3 border rounded text-center">
                    <small class="text-muted d-block mb-1">Laju Inflasi</small>
                    <h4 class="fw-bold mb-0 {{ $selectedCountry->inflation > 5 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($selectedCountry->inflation, 1) }}%
                    </h4>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="indicator-box p-3 border rounded text-center">
                    <small class="text-muted d-block mb-1">Total Populasi</small>
                    <h4 class="fw-bold mb-0">
                        @if($selectedCountry->population >= 1e6)
                            {{ number_format($selectedCountry->population / 1e6, 1) }} M
                        @else
                            {{ number_format($selectedCountry->population) }}
                        @endif
                    </h4>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="indicator-box p-3 border rounded text-center">
                    <small class="text-muted d-block mb-1">Mata Uang & Kurs</small>
                    <h4 class="fw-bold mb-0" style="font-size: 1.15rem;">
                        {{ $selectedCountry->currency }} 
                        <span class="text-muted" style="font-size:0.8rem; font-weight: normal;">
                            (1 USD = {{ number_format($kursRate, 2) }} {{ $selectedCountry->currency }})
                        </span>
                    </h4>
                </div>
            </div>
        </div>

        <!-- Pemecahan Skor Risiko (Fitur 2: Risk Scoring Engine) -->
        <h6 class="fw-bold border-bottom pb-2 mb-3">
            🎯 Risk Scoring Engine (Model Pembobotan)
        </h6>
        
        <div class="row align-items-center mb-3">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-center p-3 rounded bg-light border" style="min-width: 100px;">
                        <span class="text-muted d-block" style="font-size: 0.75rem;">Total Score</span>
                        <h2 class="fw-bold mb-0 text-primary">{{ $calculatedRiskScore }}<small style="font-size: 0.5em;">/100</small></h2>
                    </div>
                    <div>
                        <span class="text-muted d-block" style="font-size: 0.85rem;">Tingkat Risiko</span>
                        @php
                            $badgeColor = 'bg-success';
                            if ($calculatedRiskLevel === 'Medium') $badgeColor = 'bg-warning text-dark';
                            if ($calculatedRiskLevel === 'High') $badgeColor = 'bg-danger';
                        @endphp
                        <span class="badge {{ $badgeColor }} fs-6 fw-bold px-3 py-1 mt-1">{{ $calculatedRiskLevel }} Risk</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <!-- Bar kemajuan kontribusi risiko -->
                <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                    <span>Komponen Penyusun Risiko</span>
                    <span class="fw-bold">{{ $calculatedRiskScore }}% Total</span>
                </div>
                <div class="progress" style="height: 12px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $weatherRisk }}%;" title="Weather: {{ $weatherRisk }}%"></div>
                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $inflationRisk }}%;" title="Inflation: {{ $inflationRisk }}%"></div>
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $currencyRisk }}%;" title="Currency: {{ $currencyRisk }}%"></div>
                    <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $newsSentimentRisk }}%;" title="News Sentiment: {{ $newsSentimentRisk }}%"></div>
                </div>
                <small class="text-muted d-flex flex-wrap gap-2 mt-2" style="font-size: 0.7rem;">
                    <span><i class="bi bi-circle-fill text-info"></i> Weather ({{ $weatherRisk }})</span>
                    <span><i class="bi bi-circle-fill text-danger"></i> Inflation ({{ $inflationRisk }})</span>
                    <span><i class="bi bi-circle-fill text-warning"></i> Currency ({{ $currencyRisk }})</span>
                    <span><i class="bi bi-circle-fill text-secondary"></i> News ({{ $newsSentimentRisk }})</span>
                </small>
            </div>
        </div>

        <div class="alert alert-light border mb-0 text-muted" style="font-size: 0.8rem; background-color: #fcfcfc;">
            <i class="bi bi-info-circle-fill text-primary"></i> 
            <strong>Rumus Perhitungan:</strong> Skor Risiko dihitung secara realtime dari 
            <strong>API Cuaca ({{ $weatherRisk }})</strong> + 
            <strong>Inflasi Database ({{ $inflationRisk }})</strong> + 
            <strong>API Kurs ({{ $currencyRisk }})</strong> + 
            <strong>AI Sentimen Berita ({{ $newsSentimentRisk }})</strong>.
        </div>
    </div>
</div>