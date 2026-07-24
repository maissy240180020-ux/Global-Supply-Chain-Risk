@extends('layouts.app')

@section('title', 'Dashboard Admin | SIMRPG')

@section('content')
<style>
    .hover-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-card:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.06) !important; }
    .table-row-hover { transition: background-color 0.2s; }
    .table-row-hover:hover { background-color: #f8fafc; }
</style>

<div class="container-fluid">
    <h2 class="fw-bold mb-4 text-dark d-flex align-items-center gap-2">
        <i class="bi bi-person-workspace text-primary"></i> Pusat Kontrol Admin
    </h2>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Negara -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm hover-card" style="background-color: #eff6ff; border-radius: 16px;">
                <div class="card-body d-flex align-items-center gap-4 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 55px; height: 55px;">
                        <i class="bi bi-globe2 fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-primary fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Total Negara</h6>
                        <h3 class="text-dark fw-bold mb-0">{{ number_format($stats['total_countries']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Negara Risiko Tinggi -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm hover-card" style="background-color: #fef2f2; border-radius: 16px;">
                <div class="card-body d-flex align-items-center gap-4 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 55px; height: 55px;">
                        <i class="bi bi-shield-x fs-4 text-danger"></i>
                    </div>
                    <div>
                        <h6 class="text-danger fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Risiko Kritis</h6>
                        <h3 class="text-dark fw-bold mb-0">{{ number_format($stats['high_risk']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm hover-card" style="background-color: #ecfdf5; border-radius: 16px;">
                <div class="card-body d-flex align-items-center gap-4 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 55px; height: 55px;">
                        <i class="bi bi-people fs-4 text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-success fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Total Pengguna</h6>
                        <h3 class="text-dark fw-bold mb-0">{{ number_format($stats['total_users']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pelabuhan -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm hover-card" style="background-color: #faf5ff; border-radius: 16px;">
                <div class="card-body d-flex align-items-center gap-4 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 55px; height: 55px;">
                        <i class="bi bi-geo-alt fs-4 text-purple" style="color: #9333ea;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem; color: #9333ea;">Titik Pelabuhan</h6>
                        <h3 class="text-dark fw-bold mb-0">{{ number_format($stats['total_ports']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Pendaftar Terbaru -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-person-plus text-primary me-2"></i> Pendaftar Terbaru</h6>
                    <a href="{{ route('admin.users') }}" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 text-muted fw-medium py-3 border-0">Pengguna</th>
                                    <th class="text-muted fw-medium py-3 border-0">Email</th>
                                    <th class="text-muted fw-medium py-3 border-0">Akses</th>
                                    <th class="pe-4 text-muted fw-medium py-3 border-0 text-end">Terdaftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers as $user)
                                <tr class="table-row-hover">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex justify-content-center align-items-center fw-bold shadow-sm" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span class="fw-bold text-dark">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-muted">{{ $user->email }}</td>
                                    <td class="py-3">
                                        @if($user->role === 'admin')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1" style="border-radius: 6px;"><i class="bi bi-shield-lock me-1"></i> Admin</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1" style="border-radius: 6px;"><i class="bi bi-person me-1"></i> User</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 py-3 text-end text-muted small">
                                        {{ $user->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada pengguna terdaftar.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Akses Cepat Kelola -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-lightning-charge text-warning me-2"></i> Akses Cepat</h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-primary text-start d-flex align-items-center p-3 hover-card" style="border-radius: 12px; border-color: #e2e8f0;">
                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="bi bi-people-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Kelola User</h6>
                                <small class="text-muted">Hak akses & akun</small>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </a>

                        <a href="{{ route('admin.ports') }}" class="btn btn-outline-info text-start d-flex align-items-center p-3 hover-card" style="border-radius: 12px; border-color: #e2e8f0;">
                            <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="bi bi-geo-alt-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Kelola Pelabuhan</h6>
                                <small class="text-muted">Dataset koordinat</small>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </a>

                        <a href="{{ route('admin.articles') }}" class="btn btn-outline-warning text-start d-flex align-items-center p-3 hover-card" style="border-radius: 12px; border-color: #e2e8f0;">
                            <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="bi bi-journal-text fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Kelola Artikel</h6>
                                <small class="text-muted">Publikasi analisis</small>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </a>
                        
                        <a href="{{ route('admin.api.index') }}" class="btn btn-outline-dark text-start d-flex align-items-center p-3 hover-card" style="border-radius: 12px; border-color: #e2e8f0;">
                            <div class="bg-dark bg-opacity-10 text-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="bi bi-cloud-arrow-down-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">API Monitoring</h6>
                                <small class="text-muted">Status koneksi peladen</small>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection