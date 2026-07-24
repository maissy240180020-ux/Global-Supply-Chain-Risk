@extends('layouts.app')

@section('title', 'Tambah User - Admin | SIMRPG')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.users') }}" class="btn btn-light bg-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center hover-card" style="width: 45px; height: 45px;">
            <i class="bi bi-arrow-left fs-5 text-dark"></i>
        </a>
        <div>
            <h2 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-person-plus text-primary"></i> Tambah Pengguna Baru
            </h2>
            <p class="text-muted mb-0">Buat akun baru untuk memberikan akses ke dalam sistem.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-card-text text-primary me-2"></i> Formulir Data Pengguna</h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg bg-light border-light shadow-none @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" style="border-radius: 10px;" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-lg bg-light border-light shadow-none @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Contoh: budi@email.com" style="border-radius: 10px;" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Role / Hak Akses <span class="text-danger">*</span></label>
                            <select name="role" class="form-select form-select-lg bg-light border-light shadow-none @error('role') is-invalid @enderror" style="border-radius: 10px;" required>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User Biasa</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text text-muted mt-2"><i class="bi bi-info-circle me-1"></i> Administrator memiliki akses penuh ke area panel Admin.</div>
                        </div>

                        <div class="row g-3 mb-5">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Kata Sandi <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control form-control-lg bg-light border-light shadow-none @error('password') is-invalid @enderror" style="border-radius: 10px;" placeholder="Minimal 6 karakter" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control form-control-lg bg-light border-light shadow-none" style="border-radius: 10px;" placeholder="Ulangi kata sandi" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('admin.users') }}" class="btn btn-light px-4 py-2 fw-semibold" style="border-radius: 10px;">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold d-flex align-items-center gap-2 shadow-sm" style="border-radius: 10px;">
                                <i class="bi bi-save"></i> Simpan Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5 d-none d-lg-block">
            <!-- Decorative / Helper text side -->
            <div class="card border-0 bg-primary bg-opacity-10 shadow-none h-100" style="border-radius: 16px;">
                <div class="card-body p-5 d-flex flex-column justify-content-center text-center">
                    <i class="bi bi-person-bounding-box text-primary opacity-50 mb-4" style="font-size: 5rem;"></i>
                    <h5 class="fw-bold text-primary mb-3">Keamanan Akun</h5>
                    <p class="text-muted">Pastikan Anda memberikan peran administrator hanya kepada personel yang berwenang. Semua perubahan pada sistem akan tercatat.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
