<div class="row">

    <!-- 🌤 Fitur 3: Ringkasan Cuaca Realtime (Open-Meteo API) -->
    <div class="col-lg-4 mb-4">
        <div class="card dashboard-card h-100 shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">🌤️ Cuaca Realtime (API)</h6>
                    <span class="badge bg-info">Realtime</span>
                </div>

                @if($cuaca)
                    <div class="text-center my-3">
                        <h1 class="fw-bold text-primary mb-1">{{ number_format($cuaca['temperature_2m'], 1) }}°C</h1>
                        <span class="text-secondary fw-semibold">{{ $selectedCountry->weather ?? 'Cerah' }}</span>
                        <div class="text-muted" style="font-size: 0.8rem;">{{ $selectedCountry->capital }}</div>
                    </div>
                    
                    <hr class="my-3">
                    
                    <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem;">
                        <span class="text-muted">Kecepatan Angin:</span>
                        <strong class="text-dark">{{ $cuaca['wind_speed_10m'] }} km/h</strong>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size: 0.85rem;">
                        <span class="text-muted">Koordinat:</span>
                        <strong class="text-dark">{{ number_format($selectedCountry->latitude, 3) }}, {{ number_format($selectedCountry->longitude, 3) }}</strong>
                    </div>
                @else
                    <div class="alert alert-warning py-2 text-center" style="font-size:0.8rem;">
                        <i class="bi bi-exclamation-triangle"></i> Cuaca tidak dapat dimuat.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 💱 Fitur 4: Nilai Tukar Realtime (Frankfurter API) -->
    <div class="col-lg-4 mb-4">
        <div class="card dashboard-card h-100 shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">💱 Nilai Tukar Realtime (API)</h6>
                    <span class="badge bg-warning text-dark">USD Base</span>
                </div>

                <div class="text-center my-3">
                    <h2 class="fw-bold text-success mb-1">
                        1 USD = 
                    </h2>
                    <h3 class="fw-bold text-dark">
                        {{ number_format($kursRate, 2) }} {{ $selectedCountry->currency }}
                    </h3>
                    <span class="text-muted" style="font-size: 0.8rem;">Mata Uang Lokal: <strong>{{ $selectedCountry->currency }}</strong></span>
                </div>
                
                <hr class="my-3">
                
                <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem;">
                    <span class="text-muted">Status Volatilitas:</span>
                    @php
                        $volatilityColor = 'text-success';
                        $volatilityText = 'Rendah (Stabil)';
                        if (in_array($selectedCountry->currency, ['IDR', 'CNY', 'INR', 'BRL'])) {
                            $volatilityColor = 'text-warning';
                            $volatilityText = 'Sedang';
                        } elseif ($selectedCountry->currency === 'RUB') {
                            $volatilityColor = 'text-danger';
                            $volatilityText = 'Tinggi';
                        }
                    @endphp
                    <strong class="{{ $volatilityColor }}">{{ $volatilityText }}</strong>
                </div>
                <div class="d-flex justify-content-between" style="font-size: 0.85rem;">
                    <span class="text-muted">Sumber API:</span>
                    <strong class="text-dark">Frankfurter API</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- 📰 Fitur 5 & AI: News Intelligence & Sentiment Analysis (RSS Parsing) -->
    <div class="col-lg-4 mb-4">
        <div class="card dashboard-card h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">📰 Sentimen Berita Rantai Pasok</h6>
                    @php
                        $sentColor = 'bg-secondary';
                        if ($sentiment === 'Positive') $sentColor = 'bg-success';
                        if ($sentiment === 'Negative') $sentColor = 'bg-danger';
                    @endphp
                    <span class="badge {{ $sentColor }}">{{ $sentiment }}</span>
                </div>

                <div class="flex-grow-1" style="font-size: 0.8rem; max-height: 180px; overflow-y: auto;">
                    @forelse($berita as $n)
                        <div class="border-bottom pb-2 mb-2">
                            <a href="{{ $n['link'] }}" target="_blank" class="text-decoration-none text-dark fw-semibold d-block text-truncate" title="{{ $n['judul'] }}">
                                {{ $n['judul'] }}
                            </a>
                            <div class="d-flex justify-content-between align-items-center mt-1" style="font-size: 0.7rem;">
                                <span class="text-muted">{{ $n['tanggal'] }}</span>
                                @php
                                    $itemSentColor = 'text-secondary';
                                    if ($n['sentiment'] === 'Positive') $itemSentColor = 'text-success';
                                    if ($n['sentiment'] === 'Negative') $itemSentColor = 'text-danger';
                                @endphp
                                <span class="fw-bold {{ $itemSentColor }}">{{ $n['sentiment'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted my-4">
                            Tidak ada berita rantai pasok khusus ditemukan untuk {{ $selectedCountry->country_name }}.
                        </div>
                    @endforelse
                </div>

                <div class="mt-2 text-center" style="font-size: 0.75rem;">
                    <span class="text-muted">Lexicon Stats: </span>
                    <span class="text-success fw-bold"><i class="bi bi-plus-circle"></i> {{ $posCount }} Pos</span> / 
                    <span class="text-danger fw-bold"><i class="bi bi-dash-circle"></i> {{ $negCount }} Neg</span>
                </div>
            </div>
        </div>
    </div>

</div>