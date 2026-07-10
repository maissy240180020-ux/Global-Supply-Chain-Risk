@extends('layouts.app')

@section('title','Ubah Data Negara')

@section('content')

<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        ✏️ Ubah Data Negara
    </h2>

    <div class="card dashboard-card">

        <div class="card-body">

            <form action="{{ route('countries.update',$country->id) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="row">

                    <!-- Nama Negara -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Nama Negara
                        </label>

                        <input
                            type="text"
                            name="country_name"
                            class="form-control"
                            value="{{ $country->country_name }}"
                            required>

                    </div>

                    <!-- Kode Negara -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kode Negara
                        </label>

                        <input
                            type="text"
                            name="country_code"
                            class="form-control"
                            value="{{ $country->country_code }}"
                            required>

                    </div>

                    <!-- Ibu Kota -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Ibu Kota
                        </label>

                        <input
                            type="text"
                            name="capital"
                            class="form-control"
                            value="{{ $country->capital }}"
                            required>

                    </div>

                    <!-- Mata Uang -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Mata Uang
                        </label>

                        <input
                            type="text"
                            name="currency"
                            class="form-control"
                            value="{{ $country->currency }}"
                            required>

                    </div>

                    <!-- GDP -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Produk Domestik Bruto (GDP)
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="gdp"
                            class="form-control"
                            value="{{ $country->gdp }}">

                    </div>

                    <!-- Inflasi -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Inflasi (%)
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="inflation"
                            class="form-control"
                            value="{{ $country->inflation }}">

                    </div>

                    <!-- Populasi -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Populasi
                        </label>

                        <input
                            type="number"
                            name="population"
                            class="form-control"
                            value="{{ $country->population }}">

                    </div>

                    <!-- Skor Risiko -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Skor Risiko
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="risk_score"
                            class="form-control"
                            value="{{ $country->risk_score }}"
                            required>

                    </div>

                    <!-- Tingkat Risiko -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Tingkat Risiko
                        </label>

                        <select
                            name="risk_level"
                            class="form-select">

                            <option value="Low"
                                {{ $country->risk_level=='Low'?'selected':'' }}>
                                Rendah
                            </option>

                            <option value="Medium"
                                {{ $country->risk_level=='Medium'?'selected':'' }}>
                                Sedang
                            </option>

                            <option value="High"
                                {{ $country->risk_level=='High'?'selected':'' }}>
                                Tinggi
                            </option>

                        </select>

                    </div>

                    <!-- Suhu -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Suhu (°C)
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="temperature"
                            class="form-control"
                            value="{{ $country->temperature }}">

                    </div>

                    <!-- Kondisi Cuaca -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kondisi Cuaca
                        </label>

                        <select
                            name="weather"
                            class="form-select">

                            <option value="Cerah"
                                {{ $country->weather=='Cerah'?'selected':'' }}>
                                Cerah
                            </option>

                            <option value="Berawan"
                                {{ $country->weather=='Berawan'?'selected':'' }}>
                                Berawan
                            </option>

                            <option value="Hujan"
                                {{ $country->weather=='Hujan'?'selected':'' }}>
                                Hujan
                            </option>

                            <option value="Cerah Berawan"
                                {{ $country->weather=='Cerah Berawan'?'selected':'' }}>
                                Cerah Berawan
                            </option>

                        </select>

                    </div>

                    <!-- Latitude -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Latitude
                        </label>

                        <input
                            type="number"
                            step="0.000001"
                            name="latitude"
                            class="form-control"
                            value="{{ $country->latitude }}">

                    </div>

                    <!-- Longitude -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Longitude
                        </label>

                        <input
                            type="number"
                            step="0.000001"
                            name="longitude"
                            class="form-control"
                            value="{{ $country->longitude }}">

                    </div>

                </div>

                <hr>

                <button
                    type="submit"
                    class="btn btn-warning">

                    <i class="bi bi-save"></i>

                    Simpan Perubahan

                </button>

                <a
                    href="{{ route('countries.index') }}"
                    class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i>

                    Kembali

                </a>

            </form>

        </div>

    </div>

</div>

@endsection