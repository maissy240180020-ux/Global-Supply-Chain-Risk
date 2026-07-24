@extends('layouts.app')

@section('title', 'Edit User - Admin | SIMRPG')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.users') }}" class="btn btn-light bg-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center hover-card" style="width: 45px; height: 45px;">
            <i class="bi bi-arrow-left fs-5 text-dark"></i>
        </a>
        <div>
            <h2 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square text-primary"></i> Edit Data Pengguna
            </h2>
            <p class="text-muted mb-0">Perbarui profil atau hak akses dari pengguna.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-card-text text-primary me-2"></i> Formulir Edit Data</h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg bg-light border-light shadow-none @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" style="border-radius: 10px;" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-lg bg-light border-light shadow-none @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" style="border-radius: 10px;" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold text-dark">Role / Hak Akses <span class="text-danger">*</span></label>
                            <select name="role" class="form-select form-select-lg bg-light border-light shadow-none @error('role') is-invalid @enderror" style="border-radius: 10px;" required>
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User Biasa</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Password Section inside a sub-card -->
                        <div class="card bg-light border-light shadow-none mb-5" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark"><i class="bi bi-shield-lock me-2 text-primary"></i> Perbarui Kata Sandi <small class="fw-normal text-muted ms-1">(Opsional)</small></h6>
                                <p class="text-muted small mb-4">Kosongkan kolom ini jika Anda tidak ingin mereset/mengubah kata sandi pengguna ini.</p>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">Kata Sandi Baru</label>
                                        <input type="password" name="password" class="form-control bg-white shadow-none @error('password') is-invalid @enderror" style="border-radius: 8px;">
                                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">Konfirmasi Sandi Baru</label>
                                        <input type="password" name="password_confirmation" class="form-control bg-white shadow-none" style="border-radius: 8px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('admin.users') }}" class="btn btn-light px-4 py-2 fw-semibold" style="border-radius: 10px;">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold d-flex align-items-center gap-2 shadow-sm" style="border-radius: 10px;">
                                <i class="bi bi-save"></i> Perbarui Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5 d-none d-lg-block">
            <!-- Decorative / Profile Card -->
            <div class="card border-0 shadow-sm text-center h-100 d-flex flex-column justify-content-center" style="border-radius: 16px;">
                <div class="card-body p-5">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex justify-content-center align-items-center fw-bold mb-4 shadow-sm" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    @if($user->role === 'admin')
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill fs-6"><i class="bi bi-shield-lock-fill me-1"></i> Administrator</span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2 rounded-pill fs-6"><i class="bi bi-person-fill me-1"></i> User Biasa</span>
                    @endif

                    <hr class="my-4 text-muted">
                    <div class="d-flex justify-content-center gap-4 text-muted small">
                        <div>
                            <span class="d-block fw-bold text-dark">Terdaftar Pada</span>
                            {{ $user->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
