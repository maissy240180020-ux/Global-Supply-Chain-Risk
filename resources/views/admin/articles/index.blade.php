@extends('layouts.app')

@section('title', 'Kelola Artikel - Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">📰 Kelola Artikel Analisis</h2>
            <p class="text-muted mb-0">Manajemen publikasi berita dan artikel internal.</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-warning text-dark rounded-pill fw-bold shadow-sm">
            <i class="bi bi-pencil-square me-1"></i> Tulis Artikel
        </a>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 16px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">Judul Artikel</th>
                            <th>Kategori</th>
                            <th>Penulis</th>
                            <th>Tanggal Publikasi</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles as $article)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark d-block text-truncate" style="max-width: 300px;">
                                    {{ $article->title }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $article->category }}</span>
                            </td>
                            <td class="text-muted">{{ $article->author }}</td>
                            <td class="text-muted" style="font-size: 0.85rem;">
                                {{ \Carbon\Carbon::parse($article->created_at)->format('d M Y, H:i') }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Edit</a>
                                    
                                    <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" class="delete-form m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($articles->isEmpty())
                <div class="text-center py-5">
                    <h5 class="text-muted fw-bold">Belum ada artikel yang dipublikasikan.</h5>
                </div>
            @endif
        </div>
        
        <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-center">
            {{ $articles->links() }}
        </div>
    </div>
</div>
@endsection
