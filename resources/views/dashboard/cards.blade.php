@php
    $total = max(1, $totalCountries ?? 0);
    
    // Average Risk Card calculations
    $avgRisk = $averageRisk ?? 0;
    if ($avgRisk <= 35) {
        $avgStatus = 'RENDAH';
        $avgColor = '#10b981';
        $avgBg = 'rgba(16, 185, 129, 0.12)';
    } elseif ($avgRisk <= 70) {
        $avgStatus = 'SEDANG';
        $avgColor = '#f59e0b';
        $avgBg = 'rgba(245, 158, 11, 0.12)';
    } else {
        $avgStatus = 'TINGGI';
        $avgColor = '#ef4444';
        $avgBg = 'rgba(239, 68, 68, 0.12)';
    }

    $highPct = ($highRisk / $total) * 100;
    $mediumPct = ($mediumRisk / $total) * 100;
    $lowPct = ($lowRisk / $total) * 100;
@endphp

<div class="row g-3 mb-4">

    <!-- Skor Risiko Global -->
    <div class="col-lg col-md-6 col-sm-12">
        <div class="card dashboard-card h-100 border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase tracking-wider fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                            Skor Risiko Global
                        </span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 38px; height: 38px; background-color: {{ $avgBg }}; color: {{ $avgColor }};">
                            <i class="bi bi-globe fs-5"></i>
                        </div>
                    </div>
                    
                    <!-- Value -->
                    <div class="d-flex align-items-baseline mb-3">
                        <h2 class="fw-bold mb-0 text-dark" style="font-size: 2.2rem; line-height: 1;">
                            {{ $averageRisk ?? 0 }}
                        </h2>
                        <span class="text-muted ms-1 fw-medium" style="font-size: 0.95rem;">/100</span>
                    </div>
                </div>
                
                <!-- Footer & Indicator -->
                <div class="mt-2">
                    <div class="progress mb-2" style="height: 6px; background-color: #f1f5f9;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $avgRisk }}%; background-color: {{ $avgColor }} !important; transition: width 0.8s ease;" 
                             aria-valuenow="{{ $avgRisk }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span class="text-muted fw-medium">Rata-rata Risiko</span>
                        <span class="badge rounded-pill fw-semibold px-2 py-1" 
                              style="background-color: {{ $avgBg }}; color: {{ $avgColor }}; font-size: 0.7rem; letter-spacing: 0.02em;">
                            {{ $avgStatus }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Negara Risiko Tinggi -->
    <div class="col-lg col-md-6 col-sm-12">
        <div class="card dashboard-card h-100 border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase tracking-wider fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                            Negara Risiko Tinggi
                        </span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 38px; height: 38px; background-color: rgba(239, 68, 68, 0.12); color: #ef4444;">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        </div>
                    </div>
                    
                    <!-- Value -->
                    <div class="d-flex align-items-baseline mb-3">
                        <h2 class="fw-bold mb-0 text-dark" style="font-size: 2.2rem; line-height: 1;">
                            {{ $highRisk }}
                        </h2>
                    </div>
                </div>
                
                <!-- Footer & Indicator -->
                <div class="mt-2">
                    <div class="progress mb-2" style="height: 6px; background-color: #f1f5f9;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $highPct }}%; background-color: #ef4444 !important; transition: width 0.8s ease;" 
                             aria-valuenow="{{ $highPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span class="text-muted fw-medium">Proporsi</span>
                        <span class="badge rounded-pill fw-semibold px-2 py-1" 
                              style="background-color: rgba(239, 68, 68, 0.12); color: #ef4444; font-size: 0.7rem; letter-spacing: 0.02em;">
                            {{ number_format($highPct, 1) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Negara Risiko Sedang -->
    <div class="col-lg col-md-6 col-sm-12">
        <div class="card dashboard-card h-100 border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase tracking-wider fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                            Negara Risiko Sedang
                        </span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 38px; height: 38px; background-color: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                            <i class="bi bi-shield-fill-exclamation fs-5"></i>
                        </div>
                    </div>
                    
                    <!-- Value -->
                    <div class="d-flex align-items-baseline mb-3">
                        <h2 class="fw-bold mb-0 text-dark" style="font-size: 2.2rem; line-height: 1;">
                            {{ $mediumRisk }}
                        </h2>
                    </div>
                </div>
                
                <!-- Footer & Indicator -->
                <div class="mt-2">
                    <div class="progress mb-2" style="height: 6px; background-color: #f1f5f9;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $mediumPct }}%; background-color: #f59e0b !important; transition: width 0.8s ease;" 
                             aria-valuenow="{{ $mediumPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span class="text-muted fw-medium">Proporsi</span>
                        <span class="badge rounded-pill fw-semibold px-2 py-1" 
                              style="background-color: rgba(245, 158, 11, 0.12); color: #f59e0b; font-size: 0.7rem; letter-spacing: 0.02em;">
                            {{ number_format($mediumPct, 1) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Negara Risiko Rendah -->
    <div class="col-lg col-md-6 col-sm-12">
        <div class="card dashboard-card h-100 border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase tracking-wider fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                            Negara Risiko Rendah
                        </span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 38px; height: 38px; background-color: rgba(16, 185, 129, 0.12); color: #10b981;">
                            <i class="bi bi-shield-fill-check fs-5"></i>
                        </div>
                    </div>
                    
                    <!-- Value -->
                    <div class="d-flex align-items-baseline mb-3">
                        <h2 class="fw-bold mb-0 text-dark" style="font-size: 2.2rem; line-height: 1;">
                            {{ $lowRisk }}
                        </h2>
                    </div>
                </div>
                
                <!-- Footer & Indicator -->
                <div class="mt-2">
                    <div class="progress mb-2" style="height: 6px; background-color: #f1f5f9;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $lowPct }}%; background-color: #10b981 !important; transition: width 0.8s ease;" 
                             aria-valuenow="{{ $lowPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span class="text-muted fw-medium">Proporsi</span>
                        <span class="badge rounded-pill fw-semibold px-2 py-1" 
                              style="background-color: rgba(16, 185, 129, 0.12); color: #10b981; font-size: 0.7rem; letter-spacing: 0.02em;">
                            {{ number_format($lowPct, 1) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Negara -->
    <div class="col-lg col-md-6 col-sm-12">
        <div class="card dashboard-card h-100 border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase tracking-wider fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                            Total Negara
                        </span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 38px; height: 38px; background-color: rgba(71, 85, 105, 0.12); color: #475569;">
                            <i class="bi bi-flag-fill fs-5"></i>
                        </div>
                    </div>
                    
                    <!-- Value -->
                    <div class="d-flex align-items-baseline mb-3">
                        <h2 class="fw-bold mb-0 text-dark" style="font-size: 2.2rem; line-height: 1;">
                            {{ $totalCountries }}
                        </h2>
                    </div>
                </div>
                
                <!-- Footer & Indicator -->
                <div class="mt-2">
                    <div class="progress mb-2" style="height: 6px; background-color: #f1f5f9;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: 100%; background-color: #475569 !important; transition: width 0.8s ease;" 
                             aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span class="text-muted fw-medium">Status Monitoring</span>
                        <span class="badge rounded-pill fw-semibold px-2 py-1" 
                              style="background-color: rgba(71, 85, 105, 0.12); color: #475569; font-size: 0.7rem; letter-spacing: 0.02em;">
                            AKTIF
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>