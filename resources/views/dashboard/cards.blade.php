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

<style>
    .kpi-card {
        border-radius: 16px !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03) !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important;
    }
    
    .kpi-icon-box {
        width: 48px; 
        height: 48px;
        transition: transform 0.3s ease;
    }
    
    .kpi-card:hover .kpi-icon-box {
        transform: scale(1.1) rotate(5deg);
    }
    
    .kpi-number {
        font-size: 2.5rem; 
        line-height: 1;
        letter-spacing: -1px;
    }
</style>

<div class="row g-3 mb-4">

    <!-- Skor Risiko Global -->
    <div class="col-lg col-md-6 col-sm-12">
        <div class="card kpi-card h-100" style="background-color: #f8fafc;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase tracking-wider fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                            Skor Risiko Global
                        </span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle kpi-icon-box" 
                             style="background-color: {{ $avgBg }}; color: {{ $avgColor }};">
                            <i class="bi bi-globe2 fs-4"></i>
                        </div>
                    </div>
                    
                    <!-- Value -->
                    <div class="d-flex align-items-baseline mb-3">
                        <h2 class="fw-bold mb-0 text-dark kpi-number count-up" data-target="{{ $averageRisk ?? 0 }}">
                            0
                        </h2>
                        <span class="text-muted ms-1 fw-medium" style="font-size: 0.95rem;">/100</span>
                    </div>
                </div>
                
                <!-- Footer & Indicator -->
                <div class="mt-2">
                    <div class="progress mb-2 rounded-pill" style="height: 6px; background-color: rgba(0,0,0,0.05);">
                        <div class="progress-bar rounded-pill" role="progressbar" 
                             style="width: {{ $avgRisk }}%; background-color: {{ $avgColor }} !important; transition: width 1s ease-in-out;" 
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
        <div class="card kpi-card h-100" style="background-color: #fef2f2;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase tracking-wider fw-bold" style="color: #b91c1c; font-size: 0.72rem; letter-spacing: 0.05em;">
                            Negara Risiko Tinggi
                        </span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle kpi-icon-box" 
                             style="background-color: rgba(239, 68, 68, 0.15); color: #ef4444;">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        </div>
                    </div>
                    
                    <!-- Value -->
                    <div class="d-flex align-items-baseline mb-3">
                        <h2 class="fw-bold mb-0 kpi-number count-up" style="color: #7f1d1d;" data-target="{{ $highRisk }}">
                            0
                        </h2>
                    </div>
                </div>
                
                <!-- Footer & Indicator -->
                <div class="mt-2">
                    <div class="progress mb-2 rounded-pill" style="height: 6px; background-color: rgba(239,68,68,0.15);">
                        <div class="progress-bar rounded-pill" role="progressbar" 
                             style="width: {{ $highPct }}%; background-color: #ef4444 !important; transition: width 1s ease-in-out;" 
                             aria-valuenow="{{ $highPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span class="fw-medium" style="color: #991b1b;">Proporsi</span>
                        <span class="badge rounded-pill fw-semibold px-2 py-1" 
                              style="background-color: rgba(239, 68, 68, 0.15); color: #ef4444; font-size: 0.7rem; letter-spacing: 0.02em;">
                            {{ number_format($highPct, 1) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Negara Risiko Sedang -->
    <div class="col-lg col-md-6 col-sm-12">
        <div class="card kpi-card h-100" style="background-color: #fefce8;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase tracking-wider fw-bold" style="color: #b45309; font-size: 0.72rem; letter-spacing: 0.05em;">
                            Negara Risiko Sedang
                        </span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle kpi-icon-box" 
                             style="background-color: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                            <i class="bi bi-shield-fill-exclamation fs-4"></i>
                        </div>
                    </div>
                    
                    <!-- Value -->
                    <div class="d-flex align-items-baseline mb-3">
                        <h2 class="fw-bold mb-0 kpi-number count-up" style="color: #78350f;" data-target="{{ $mediumRisk }}">
                            0
                        </h2>
                    </div>
                </div>
                
                <!-- Footer & Indicator -->
                <div class="mt-2">
                    <div class="progress mb-2 rounded-pill" style="height: 6px; background-color: rgba(245,158,11,0.15);">
                        <div class="progress-bar rounded-pill" role="progressbar" 
                             style="width: {{ $mediumPct }}%; background-color: #f59e0b !important; transition: width 1s ease-in-out;" 
                             aria-valuenow="{{ $mediumPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span class="fw-medium" style="color: #92400e;">Proporsi</span>
                        <span class="badge rounded-pill fw-semibold px-2 py-1" 
                              style="background-color: rgba(245, 158, 11, 0.15); color: #f59e0b; font-size: 0.7rem; letter-spacing: 0.02em;">
                            {{ number_format($mediumPct, 1) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Negara Risiko Rendah -->
    <div class="col-lg col-md-6 col-sm-12">
        <div class="card kpi-card h-100" style="background-color: #f0fdf4;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase tracking-wider fw-bold" style="color: #047857; font-size: 0.72rem; letter-spacing: 0.05em;">
                            Negara Risiko Rendah
                        </span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle kpi-icon-box" 
                             style="background-color: rgba(16, 185, 129, 0.15); color: #10b981;">
                            <i class="bi bi-shield-fill-check fs-4"></i>
                        </div>
                    </div>
                    
                    <!-- Value -->
                    <div class="d-flex align-items-baseline mb-3">
                        <h2 class="fw-bold mb-0 kpi-number count-up" style="color: #064e3b;" data-target="{{ $lowRisk }}">
                            0
                        </h2>
                    </div>
                </div>
                
                <!-- Footer & Indicator -->
                <div class="mt-2">
                    <div class="progress mb-2 rounded-pill" style="height: 6px; background-color: rgba(16,185,129,0.15);">
                        <div class="progress-bar rounded-pill" role="progressbar" 
                             style="width: {{ $lowPct }}%; background-color: #10b981 !important; transition: width 1s ease-in-out;" 
                             aria-valuenow="{{ $lowPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span class="fw-medium" style="color: #065f46;">Proporsi</span>
                        <span class="badge rounded-pill fw-semibold px-2 py-1" 
                              style="background-color: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 0.7rem; letter-spacing: 0.02em;">
                            {{ number_format($lowPct, 1) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Negara -->
    <div class="col-lg col-md-6 col-sm-12">
        <div class="card kpi-card h-100" style="background-color: #eff6ff;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-uppercase tracking-wider fw-bold" style="color: #1d4ed8; font-size: 0.72rem; letter-spacing: 0.05em;">
                            Total Negara
                        </span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle kpi-icon-box" 
                             style="background-color: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                            <i class="bi bi-flag-fill fs-4"></i>
                        </div>
                    </div>
                    
                    <!-- Value -->
                    <div class="d-flex align-items-baseline mb-3">
                        <h2 class="fw-bold mb-0 kpi-number count-up" style="color: #1e3a8a;" data-target="{{ $totalCountries }}">
                            0
                        </h2>
                    </div>
                </div>
                
                <!-- Footer & Indicator -->
                <div class="mt-2">
                    <div class="progress mb-2 rounded-pill" style="height: 6px; background-color: rgba(59,130,246,0.15);">
                        <div class="progress-bar rounded-pill" role="progressbar" 
                             style="width: 100%; background-color: #3b82f6 !important; transition: width 1s ease-in-out;" 
                             aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span class="fw-medium" style="color: #1e40af;">Status Monitoring</span>
                        <span class="badge rounded-pill fw-semibold px-2 py-1" 
                              style="background-color: rgba(59, 130, 246, 0.15); color: #3b82f6; font-size: 0.7rem; letter-spacing: 0.02em;">
                            AKTIF
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>