@extends('layouts.app')

@section('title', 'Kelola User - Admin | SIMRPG')

@section('content')
<style>
    .table-row-hover { transition: background-color 0.2s; }
    .table-row-hover:hover { background-color: #f8fafc; }
    .action-btn { transition: all 0.2s; border-radius: 8px; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; }
    .action-btn:hover { transform: translateY(-2px); }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-people text-primary"></i> Kelola User
            </h2>
            <p class="text-muted mb-0 ms-4 ps-2">Manajemen hak akses dan daftar pengguna sistem.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary shadow-sm px-4 py-2" style="border-radius: 10px;">
            <i class="bi bi-person-plus-fill me-2"></i> Tambah Pengguna
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center" style="border-radius: 12px;">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center" style="border-radius: 12px;">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        {{ session('error') }}
    </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-muted fw-medium py-3 border-0">Pengguna</th>
                            <th class="text-muted fw-medium py-3 border-0">Alamat Email</th>
                            <th class="text-muted fw-medium py-3 border-0">Role Akses</th>
                            <th class="text-muted fw-medium py-3 border-0">Tgl Terdaftar</th>
                            <th class="text-end pe-4 text-muted fw-medium py-3 border-0">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr class="table-row-hover border-bottom border-light">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary-subtle text-primary d-flex justify-content-center align-items-center fw-bold shadow-sm" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-bold text-dark">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 text-muted">{{ $user->email }}</td>
                            <td class="py-3">
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2" style="border-radius: 8px;"><i class="bi bi-shield-lock-fill me-1"></i> Administrator</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2" style="border-radius: 8px;"><i class="bi bi-person-fill me-1"></i> User Biasa</span>
                                @endif
                            </td>
                            <td class="py-3 text-muted small">
                                <i class="bi bi-calendar3 me-1"></i> {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="py-3 text-end pe-4">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-light text-primary border action-btn shadow-sm" title="Edit Pengguna">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light text-danger border action-btn shadow-sm" title="Hapus Pengguna">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button type="button" class="btn btn-light text-muted border action-btn shadow-sm" title="Tidak dapat menghapus diri sendiri" disabled>
                                        <i class="bi bi-shield-slash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                    <h6 class="fw-bold">Belum ada data pengguna</h6>
                                    <small>Mulai dengan menambahkan pengguna baru.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
