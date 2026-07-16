@extends('layouts.app')

@section('title', 'Daftar Pantauan Favorit')

@section('content')

<div class="container-fluid">

    <!-- Header & Search -->
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0">⭐ Daftar Pantauan Favorit</h2>
            <p class="text-muted mb-0">Pemantauan Prioritas Risiko Rantai Pasok Global untuk Negara Terpilih</p>
        </div>
        
        @if(count($watchlist) > 0)
        <!-- Search Input -->
        <div class="position-relative" style="width: 250px;">
            <input type="text" id="watchlistSearch" class="form-control ps-5 border-light shadow-none" 
                   placeholder="Cari negara terpantau..."
                   style="font-size: 0.85rem; border-radius: 10px; background-color: #f8fafc; height: 38px;">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.9rem;"></i>
        </div>
        @endif
    </div>

    @if(count($watchlist) > 0)

    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="watchlistTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 80px;">No</th>
                            <th style="width: 100px;">Bendera</th>
                            <th>Nama Negara</th>
                            <th>Level Risiko</th>
                            <th>Skor Risiko</th>
                            <th>Mata Uang</th>
                            <th>Cuaca</th>
                            <th class="text-center" style="width: 240px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($watchlist as $index => $country)
                            <tr id="watchlist-row-{{ $country->id }}">
                                <td class="ps-4 fw-semibold text-secondary">{{ $index + 1 }}</td>
                                <td>
                                    @if($country->flag)
                                        <img src="{{ $country->flag }}" alt="{{ $country->country_name }}" 
                                             width="45" class="border rounded shadow-sm">
                                    @else
                                        <span class="fs-4">🌍</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-dark">{{ $country->country_name }}</strong>
                                    <span class="text-muted d-block" style="font-size: 0.78rem;">Ibukota: {{ $country->capital ?? '-' }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = 'bg-success-subtle text-success';
                                        if ($country->risk_level == 'Medium') {
                                            $badgeClass = 'bg-warning-subtle text-warning';
                                        } elseif ($country->risk_level == 'High') {
                                            $badgeClass = 'bg-danger-subtle text-danger';
                                        }
                                    @endphp
                                    <span class="badge rounded-pill fw-bold px-2.5 py-1" style="font-size: 0.72rem; letter-spacing: 0.02em;">
                                        {{ strtoupper($country->risk_level) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold fs-5 text-dark">{{ round($country->risk_score) }}</span>
                                        <span class="text-muted" style="font-size: 0.8rem;">/100</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary">{{ $country->currency }}</span>
                                </td>
                                <td>
                                    <strong>{{ $country->temperature ?? '-' }}°C</strong>
                                    <span class="text-muted d-block" style="font-size: 0.78rem;">{{ $country->weather ?? '-' }}</span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="/dashboard?country_id={{ $country->id }}" class="btn btn-dark btn-sm rounded-pill px-3 py-1.5 d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-speedometer2"></i> Monitor
                                        </a>
                                        <button class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1.5 d-flex align-items-center gap-1 remove-favorite-btn" data-country-id="{{ $country->id }}" style="font-size: 0.75rem;">
                                            <i class="bi bi-star-fill"></i> Lepas
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @else

    <!-- Empty State -->
    <div class="card border-0 shadow-sm text-center py-5 px-4" style="border-radius: 16px; background-color: #fff;">
        <div class="card-body py-5 d-flex flex-column align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center rounded-circle mb-4" 
                 style="width: 80px; height: 80px; background-color: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                <i class="bi bi-star fs-1"></i>
            </div>
            <h4 class="fw-bold text-dark">Daftar Pantauan Kosong</h4>
            <p class="text-muted mx-auto mb-4" style="max-width: 480px; line-height: 1.6;">
                Anda belum menandai negara mana pun sebagai favorit. Tandai negara-negara prioritas Anda dengan mengklik ikon bintang (⭐) pada halaman daftar negara global untuk memantau risiko rantai pasok mereka secara berkala.
            </p>
            <a href="{{ route('countries.index') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
                <i class="bi bi-globe2"></i> Cari Negara Global
            </a>
        </div>
    </div>

    @endif

</div>

@endsection

@push('scripts')
@if(count($watchlist) > 0)
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Aksi Hapus dari Daftar Pantauan (Lepas)
    document.querySelectorAll('.remove-favorite-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const countryId = this.getAttribute('data-country-id');
            const row = document.getElementById(`watchlist-row-${countryId}`);
            
            Swal.fire({
                title: 'Lepas Negara?',
                text: "Negara ini akan dihapus dari daftar pantauan prioritas Anda.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lepas!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/watchlist/toggle/${countryId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && !data.is_favorite) {
                            // Efek transisi menghapus baris
                            row.style.transition = 'all 0.4s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(-20px)';
                            
                            setTimeout(() => {
                                row.remove();
                                
                                // Cek jika baris sudah habis, refresh halaman untuk menunjukkan empty state
                                const remainingRows = document.querySelectorAll('#watchlistTable tbody tr');
                                if (remainingRows.length === 0) {
                                    location.reload();
                                } else {
                                    // Perbarui nomor urut baris yang tersisa
                                    remainingRows.forEach((r, idx) => {
                                        r.cells[0].textContent = idx + 1;
                                    });
                                }
                            }, 400);

                            Swal.fire({
                                title: 'Dilepas!',
                                text: `${data.country_name} berhasil dihapus dari daftar pantauan.`,
                                icon: 'success',
                                timer: 1200,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error removing from watchlist:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Gagal memperbarui daftar pantauan.',
                            icon: 'error'
                        });
                    });
                }
            });
        });
    });

    // 2. Filter Pencarian Tabel
    const watchlistSearch = document.getElementById('watchlistSearch');
    const tableRows = document.querySelectorAll('#watchlistTable tbody tr');

    watchlistSearch.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        
        tableRows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            if (cells.length >= 3) {
                const countryName = cells[2].textContent.toLowerCase();
                const capitalName = cells[2].querySelector('span').textContent.toLowerCase();
                const currency = cells[5].textContent.toLowerCase();
                
                if (countryName.includes(query) || capitalName.includes(query) || currency.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
});
</script>
@endif
@endpush