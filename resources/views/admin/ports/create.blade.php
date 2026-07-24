@extends('layouts.app')

@section('title', 'Tambah Pelabuhan - Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.ports') }}" class="btn btn-outline-secondary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0">Tambah Pelabuhan Baru</h2>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <form action="{{ route('admin.ports.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nama Pelabuhan</label>
                                <input type="text" name="port_name" class="form-control @error('port_name') is-invalid @enderror" value="{{ old('port_name') }}" placeholder="Contoh: Port of Singapore" required>
                                @error('port_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Negara</label>
                                <select name="country_code" class="form-select @error('country_code') is-invalid @enderror" required>
                                    <option value="" selected disabled>-- Pilih Negara --</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->country_code }}" {{ old('country_code') == $country->country_code ? 'selected' : '' }}>
                                            {{ $country->country_name }} ({{ $country->country_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('country_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Garis Lintang (Latitude)</label>
                                <input type="number" step="any" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude') }}" placeholder="Contoh: 1.264" required>
                                @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Garis Bujur (Longitude)</label>
                                <input type="number" step="any" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude') }}" placeholder="Contoh: 103.84" required>
                                @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                        <hr class="text-light mb-4">

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light fw-bold rounded-pill shadow-sm px-4">Reset</button>
                            <button type="submit" class="btn btn-info text-white fw-bold rounded-pill shadow-sm px-4">Simpan Pelabuhan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
