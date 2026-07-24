@extends('layouts.app')

@section('title', 'Edit Artikel - Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.articles') }}" class="btn btn-outline-secondary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0">Edit Artikel Analisis</h2>
    </div>

    <div class="row">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <form action="{{ route('admin.articles.update', $article->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Artikel</label>
                            <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" value="{{ old('title', $article->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Kategori</label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="Supply Chain" {{ old('category', $article->category) == 'Supply Chain' ? 'selected' : '' }}>Rantai Pasok (Supply Chain)</option>
                                    <option value="Economy" {{ old('category', $article->category) == 'Economy' ? 'selected' : '' }}>Ekonomi Makro</option>
                                    <option value="Geopolitics" {{ old('category', $article->category) == 'Geopolitics' ? 'selected' : '' }}>Geopolitik & Konflik</option>
                                    <option value="Environment" {{ old('category', $article->category) == 'Environment' ? 'selected' : '' }}>Lingkungan & Cuaca</option>
                                    <option value="Technology" {{ old('category', $article->category) == 'Technology' ? 'selected' : '' }}>Teknologi Maritim</option>
                                </select>
                                @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sentimen Analisis</label>
                                <select name="sentiment" class="form-select">
                                    <option value="Neutral" {{ old('sentiment', $article->sentiment) == 'Neutral' ? 'selected' : '' }}>Netral</option>
                                    <option value="Positive" {{ old('sentiment', $article->sentiment) == 'Positive' ? 'selected' : '' }}>Positif</option>
                                    <option value="Negative" {{ old('sentiment', $article->sentiment) == 'Negative' ? 'selected' : '' }}>Negatif / Berisiko</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nama Penulis</label>
                                <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" value="{{ old('author', $article->author) }}" required>
                                @error('author') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Konten Artikel</label>
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="12" required>{{ old('content', $article->content) }}</textarea>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill shadow-sm px-4">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
