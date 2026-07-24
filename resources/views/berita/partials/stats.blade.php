@php
    $total = $stats['positive'] + $stats['neutral'] + $stats['negative'];
    $posPct = $total ? round(($stats['positive'] / $total) * 100) : 0;
    $neuPct = $total ? round(($stats['neutral'] / $total) * 100) : 0;
    $negPct = $total ? round(($stats['negative'] / $total) * 100) : 0;
@endphp

<div class="col-md-4">
    <div class="card shadow-sm border-0" style="border-radius: 12px; border-left: 5px solid #198754 !important;">
        <div class="card-body d-flex justify-content-between align-items-center p-3">
            <div>
                <h6 class="text-muted fw-bold mb-1" style="font-size: 0.8rem; letter-spacing: 1px;">SENTIMEN POSITIF</h6>
                <h3 class="fw-bolder text-success mb-0">{{ $stats['positive'] }} <span class="fs-6 text-muted fw-normal">({{ $posPct }}%)</span></h3>
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
                <h3 class="fw-bolder text-secondary mb-0">{{ $stats['neutral'] }} <span class="fs-6 text-muted fw-normal">({{ $neuPct }}%)</span></h3>
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
                <h3 class="fw-bolder text-danger mb-0">{{ $stats['negative'] }} <span class="fs-6 text-muted fw-normal">({{ $negPct }}%)</span></h3>
            </div>
            <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="bi bi-graph-down-arrow fs-4"></i>
            </div>
        </div>
    </div>
</div>
