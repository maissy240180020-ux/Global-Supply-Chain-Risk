@extends('layouts.app')

@section('title', 'Pemantauan Cuaca Realtime')

@section('content')

<div class="container-fluid">

    <!-- Header & Dropdown -->
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0">🌦️ Weather Intelligence Center</h2>
            <p class="text-muted mb-0">Pemantauan Kondisi Cuaca Global & Analisis Risiko Logistik Rantai Pasok</p>
        </div>
        
        <!-- Pemilih Negara -->
        <div class="bg-white p-2 rounded shadow-sm border border-light d-flex align-items-center gap-2">
            <label for="country_id" class="fw-semibold text-secondary mb-0">Negara:</label>
            <form action="{{ route('cuaca.index') }}" method="GET" id="countrySelectForm" class="m-0">
                <select name="country_id" id="country_id" class="form-select form-select-sm border-0 bg-light" style="font-weight: 500;" onchange="this.form.submit()">
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ $selectedCountry && $selectedCountry->id == $c->id ? 'selected' : '' }}>
                            {{ $c->country_name }} ({{ $c->country_code }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($cuaca)

    <div class="row g-4">
        
        <!-- Main Weather Card -->
        <div class="col-lg-5 col-md-12">
            <div class="card border-0 shadow-sm h-100 overflow-hidden text-white" 
                 style="border-radius: 20px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Location Title -->
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-light text-dark fw-semibold px-2 py-1 mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                                    STASIUN PEMANTAU
                                </span>
                                <h3 class="fw-bold mb-0">{{ $selectedCountry->country_name }}</h3>
                                <p class="text-white-50 mb-0" style="font-size: 0.9rem;">
                                    <i class="bi bi-geo-alt-fill me-1"></i> Ibukota: {{ $selectedCountry->capital ?? '-' }}
                                </p>
                            </div>
                            @if($selectedCountry->flag)
                                <img src="{{ $selectedCountry->flag }}" alt="{{ $selectedCountry->country_name }}" style="height: 32px; border-radius: 4px; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">
                            @else
                                <span class="fs-2">🌍</span>
                            @endif
                        </div>

                        <!-- Weather Big display -->
                        <div class="d-flex align-items-center my-4 py-3 justify-content-between">
                            <div>
                                <h1 class="fw-bold mb-0 text-white" style="font-size: 4rem; line-height: 1;">
                                    {{ $cuaca['temperature_2m'] }}<span style="font-size: 2rem; vertical-align: top;">°C</span>
                                </h1>
                                <span class="badge rounded-pill mt-2 px-3 py-1.5 fw-semibold" 
                                      style="background-color: {{ $cuaca['color'] }}1F; color: {{ $cuaca['color'] }}; font-size: 0.85rem; border: 1px solid {{ $cuaca['color'] }}33;">
                                    ● {{ $cuaca['description'] }}
                                </span>
                            </div>
                            <div class="pe-3">
                                <i class="bi {{ $cuaca['icon'] }}" 
                                   style="font-size: 5.5rem; color: {{ $cuaca['color'] }}; filter: drop-shadow(0px 0px 15px {{ $cuaca['color'] }}4d);"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Coordinates and update status -->
                    <div class="border-top border-secondary pt-3 mt-3" style="font-size: 0.8rem;">
                        <div class="row">
                            <div class="col-6">
                                <span class="text-white-50 d-block">LATITUDE / LONGITUDE</span>
                                <span class="fw-semibold text-white">{{ number_format($selectedCountry->latitude, 4) }} / {{ number_format($selectedCountry->longitude, 4) }}</span>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-white-50 d-block">TERAKHIR DIUPDATE</span>
                                <span class="fw-semibold text-white">{{ $cuaca['formatted_time'] }}</span>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
            </div>
        </div>

        <!-- Detail Grid Cards -->
        <div class="col-lg-7 col-md-12">
            <div class="row g-3 h-100">
                
                <!-- Apparent Temp -->
                <div class="col-sm-6 col-12">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" 
                                 style="width: 50px; height: 50px; background-color: rgba(239, 68, 68, 0.12); color: #ef4444; flex-shrink: 0;">
                                <i class="bi bi-thermometer-half fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">
                                    Suhu Terasa
                                </small>
                                <h3 class="fw-bold mb-0 mt-1 text-dark">
                                    {{ $cuaca['apparent_temperature'] ?? $cuaca['temperature_2m'] }} °C
                                </h3>
                                <small class="text-muted" style="font-size: 0.75rem;">Suhu riil yang dirasakan lingkungan</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Humidity -->
                <div class="col-sm-6 col-12">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" 
                                 style="width: 50px; height: 50px; background-color: rgba(2, 132, 199, 0.12); color: #0284c7; flex-shrink: 0;">
                                <i class="bi bi-droplet-fill fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">
                                    Kelembaban Udara
                                </small>
                                <h3 class="fw-bold mb-0 mt-1 text-dark">
                                    {{ $cuaca['relative_humidity_2m'] ?? 0 }} %
                                </h3>
                                <small class="text-muted" style="font-size: 0.75rem;">Persentase uap air di atmosfer</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wind -->
                <div class="col-sm-6 col-12">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" 
                                 style="width: 50px; height: 50px; background-color: rgba(20, 184, 166, 0.12); color: #14b8a6; flex-shrink: 0;">
                                <i class="bi bi-wind fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">
                                    Kecepatan Angin
                                </small>
                                <h3 class="fw-bold mb-0 mt-1 text-dark">
                                    {{ $cuaca['wind_speed_10m'] ?? 0 }} <span style="font-size: 1rem; font-weight: normal;">km/jam</span>
                                </h3>
                                <small class="text-muted" style="font-size: 0.75rem;">Hembusan udara dari arah {{ $cuaca['wind_direction_10m'] ?? 0 }}°</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Precipitation -->
                <div class="col-sm-6 col-12">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" 
                                 style="width: 50px; height: 50px; background-color: rgba(99, 102, 241, 0.12); color: #6366f1; flex-shrink: 0;">
                                <i class="bi bi-cloud-rain-heavy-fill fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">
                                    Curah Hujan
                                </small>
                                <h3 class="fw-bold mb-0 mt-1 text-dark">
                                    {{ $cuaca['precipitation'] ?? 0.0 }} <span style="font-size: 1rem; font-weight: normal;">mm</span>
                                </h3>
                                <small class="text-muted" style="font-size: 0.75rem;">Volume presipitasi saat ini</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Supply Chain Logistics Weather Impact Assessment -->
    <div class="card border-0 shadow-sm mt-4 overflow-hidden" style="border-radius: 16px;">
        <div class="card-header bg-white py-3 border-light d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-shield-shaded text-primary"></i> Analisis Risiko Rantai Pasok Berbasis Cuaca
            </h5>
            
            @php
                $code = $cuaca['weather_code'] ?? 0;
                // High risk weather (storms, heavy snow, thunder)
                if (in_array($code, [95, 96, 99, 71, 73, 75, 77, 85, 86])) {
                    $riskLabel = 'RISIKO TINGGI';
                    $riskBadgeColor = 'bg-danger text-white';
                    $alertBg = 'rgba(239, 68, 68, 0.06)';
                    $alertBorder = '#fecaca';
                    $alertIcon = 'bi-exclamation-triangle-fill text-danger';
                }
                // Medium risk (rain, drizzle, fog)
                elseif (in_array($code, [45, 48, 51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82])) {
                    $riskLabel = 'RISIKO SEDANG';
                    $riskBadgeColor = 'bg-warning text-dark';
                    $alertBg = 'rgba(245, 158, 11, 0.06)';
                    $alertBorder = '#fef08a';
                    $alertIcon = 'bi-exclamation-circle-fill text-warning';
                }
                // Low risk (clear, partly cloudy)
                else {
                    $riskLabel = 'RISIKO RENDAH';
                    $riskBadgeColor = 'bg-success text-white';
                    $alertBg = 'rgba(16, 185, 129, 0.06)';
                    $alertBorder = '#bbf7d0';
                    $alertIcon = 'bi-check-circle-fill text-success';
                }
            @endphp
            
            <span class="badge {{ $riskBadgeColor }} fw-bold px-3 py-1.5 rounded-pill" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                {{ $riskLabel }}
            </span>
        </div>
        
        <div class="card-body p-4" style="background-color: {{ $alertBg }}; border-top: 1px solid {{ $alertBorder }};">
            <div class="d-flex gap-3 align-items-start">
                <i class="bi {{ $alertIcon }} fs-2" style="line-height: 1;"></i>
                <div>
                    @if($riskLabel === 'RISIKO TINGGI')
                        <h5 class="fw-bold text-danger">⚠️ Peringatan Keterlambatan Logistik Signifikan</h5>
                        <p class="text-dark mb-0" style="line-height: 1.6;">
                            Kondisi cuaca ekstrim (<strong>{{ $cuaca['description'] }}</strong>) sedang melanda wilayah <strong>{{ $selectedCountry->country_name }}</strong>. 
                            Ini sangat berisiko mengganggu operasional logistik pelabuhan (bongkar muat kapal cargo) serta dapat menyebabkan pembatalan penerbangan kargo udara.
                            Kami merekomendasikan untuk menunda atau mengalihkan pengiriman penting jika memungkinkan, dan mengaktifkan rencana kontingensi rute alternatif.
                        </p>
                    @elseif($riskLabel === 'RISIKO SEDANG')
                        <h5 class="fw-bold text-warning" style="color: #b45309 !important;">⚠️ Rekomendasi Pemantauan Rutin</h5>
                        <p class="text-dark mb-0" style="line-height: 1.6;">
                            Kondisi cuaca basah/berkabut (<strong>{{ $cuaca['description'] }}</strong>) dilaporkan di wilayah <strong>{{ $selectedCountry->country_name }}</strong>. 
                            Operasional logistik jalan darat dan aktivitas pelabuhan mungkin mengalami sedikit perlambatan akibat penurunan visibilitas atau genangan air setempat. 
                            Harap pantau estimasi waktu kedatangan kontainer secara berkala dan hubungi pihak otoritas pelabuhan setempat jika ada penundaan jadwal.
                        </p>
                    @else
                        <h5 class="fw-bold text-success">✅ Kondisi Operasional Sangat Kondusif</h5>
                        <p class="text-dark mb-0" style="line-height: 1.6;">
                            Cuaca <strong>{{ $cuaca['description'] }}</strong> di wilayah <strong>{{ $selectedCountry->country_name }}</strong>. 
                            Tidak ada ancaman cuaca signifikan yang dilaporkan. Operasional pelabuhan laut, jalur logistik darat, dan penerbangan kargo udara berjalan dengan aman dan lancar. 
                            Ini adalah waktu yang optimal untuk mempercepat pemrosesan pengiriman barang Anda.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @else

    <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center gap-3 p-4" style="border-radius: 16px;">
        <i class="bi bi-x-circle-fill fs-3"></i>
        <div>
            <h5 class="fw-bold mb-1">Gagal Mengambil Data Cuaca</h5>
            <p class="mb-0">Tidak dapat terhubung ke API stasiun cuaca. Harap periksa jaringan Anda atau coba lagi beberapa saat lagi.</p>
        </div>
    </div>

    @endif

</div>

@endsection