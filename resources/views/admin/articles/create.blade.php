@extends('layouts.app')

@section('title', 'Tulis Artikel - Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.articles') }}" class="btn btn-outline-secondary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0">Tulis Artikel Baru</h2>
    </div>

    <div class="row">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <form action="{{ route('admin.articles.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Artikel</label>
                            <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Masukkan judul artikel yang menarik..." required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Kategori</label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="" selected disabled>-- Pilih Kategori --</option>
                                    <option value="Supply Chain">Rantai Pasok (Supply Chain)</option>
                                    <option value="Economy">Ekonomi Makro</option>
                                    <option value="Geopolitics">Geopolitik & Konflik</option>
                                    <option value="Environment">Lingkungan & Cuaca</option>
                                    <option value="Technology">Teknologi Maritim</option>
                                </select>
                                @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sentimen Analisis</label>
                                <select name="sentiment" class="form-select">
                                    <option value="Neutral">Netral</option>
                                    <option value="Positive">Positif</option>
                                    <option value="Negative">Negatif / Berisiko</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nama Penulis</label>
                                <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" value="{{ old('author', auth()->user()->name ?? 'Admin') }}" required>
                                @error('author') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Konten Artikel</label>
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="12" placeholder="Tuliskan isi artikel Anda di sini..." required>{{ old('content') }}</textarea>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill shadow-sm px-4">
                                <i class="bi bi-send me-1"></i> Publikasikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
