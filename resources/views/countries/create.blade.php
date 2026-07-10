@extends('layouts.app')

@section('title','Tambah Data Negara')

@section('content')

<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        🌍 Tambah Data Negara
    </h2>

    <div class="card dashboard-card">

        <div class="card-body">

            <form action="{{ route('countries.store') }}" method="POST">

                @csrf

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
                            class="form-control">

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
                            class="form-control">

                    </div>

                    <!-- Populasi -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Populasi
                        </label>

                        <input
                            type="number"
                            name="population"
                            class="form-control">

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

                            <option value="Low">
                                Rendah
                            </option>

                            <option value="Medium">
                                Sedang
                            </option>

                            <option value="High">
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
                            class="form-control">

                    </div>

                    <!-- Cuaca -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kondisi Cuaca
                        </label>

                        <select
                            name="weather"
                            class="form-select">

                            <option>Cerah</option>
                            <option>Berawan</option>
                            <option>Hujan</option>
                            <option>Cerah Berawan</option>

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
                            class="form-control">

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
                            class="form-control">

                    </div>

                </div>

                <button class="btn btn-primary">

                    💾 Simpan Data

                </button>

                <a
                    href="{{ route('countries.index') }}"
                    class="btn btn-secondary">

                    Batal

                </a>

            </form>

        </div>

    </div>

</div>

@endsection