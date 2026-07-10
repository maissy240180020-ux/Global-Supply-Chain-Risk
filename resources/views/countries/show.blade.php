@extends('layouts.app')

@section('title','Detail Negara')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">
            🌍 Detail Negara
        </h2>

        <a href="{{ route('countries.index') }}"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left"></i>

            Kembali

        </a>

    </div>

    <div class="card dashboard-card">

        <div class="card-body">

            <div class="row">

                <!-- Nama Negara -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Nama Negara
                    </label>

                    <h4>{{ $country->country_name }}</h4>

                </div>

                <!-- Kode Negara -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Kode Negara
                    </label>

                    <h4>{{ $country->country_code }}</h4>

                </div>

                <!-- Ibu Kota -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Ibu Kota
                    </label>

                    <h4>{{ $country->capital }}</h4>

                </div>

                <!-- Mata Uang -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Mata Uang
                    </label>

                    <h4>{{ $country->currency }}</h4>

                </div>

                <!-- GDP -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Produk Domestik Bruto (GDP)
                    </label>

                    <h4>
                        {{ number_format($country->gdp ?? 0,2) }}
                    </h4>

                </div>

                <!-- Inflasi -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Inflasi
                    </label>

                    <h4>
                        {{ $country->inflation ?? 0 }} %
                    </h4>

                </div>

                <!-- Populasi -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Populasi
                    </label>

                    <h4>
                        {{ number_format($country->population ?? 0) }}
                    </h4>

                </div>

                <!-- Skor Risiko -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Skor Risiko
                    </label>

                    <h4>

                        {{ number_format($country->risk_score,0) }}/100

                    </h4>

                    <div class="progress mt-2" style="height:10px;">

                        <div
                            class="progress-bar bg-warning"
                            style="width: {{ $country->risk_score }}%;">

                        </div>

                    </div>

                </div>

                <!-- Tingkat Risiko -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Tingkat Risiko
                    </label>

                    <br>

                    @php

                        $badge='bg-success';

                        if($country->risk_level=='Medium'){
                            $badge='bg-warning text-dark';
                        }

                        if($country->risk_level=='High'){
                            $badge='bg-danger';
                        }

                    @endphp

                    <span class="badge {{ $badge }} fs-6">

                        @if($country->risk_level=='High')

                            Tinggi

                        @elseif($country->risk_level=='Medium')

                            Sedang

                        @else

                            Rendah

                        @endif

                    </span>

                </div>

                <!-- Suhu -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Suhu
                    </label>

                    <h4>

                        {{ $country->temperature }} °C

                    </h4>

                </div>

                <!-- Cuaca -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Kondisi Cuaca
                    </label>

                    <h4>

                        {{ $country->weather }}

                    </h4>

                </div>

                <!-- Latitude -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Latitude
                    </label>

                    <h4>

                        {{ $country->latitude }}

                    </h4>

                </div>

                <!-- Longitude -->
                <div class="col-md-6 mb-4">

                    <label class="fw-bold text-muted">
                        Longitude
                    </label>

                    <h4>

                        {{ $country->longitude }}

                    </h4>

                </div>

            </div>

            <hr>

            <div class="alert alert-info">

                <h5 class="fw-bold">

                    📊 Ringkasan Informasi

                </h5>

                <p>

                    <strong>{{ $country->country_name }}</strong>
                    memiliki skor risiko
                    <strong>{{ number_format($country->risk_score,0) }}/100</strong>
                    dengan kategori
                    <strong>{{ $country->risk_level }}</strong>.

                </p>

                <p>

                    Negara ini memiliki Produk Domestik Bruto (GDP)
                    sebesar
                    <strong>{{ number_format($country->gdp ?? 0,2) }}</strong>,
                    tingkat inflasi
                    <strong>{{ $country->inflation ?? 0 }}%</strong>,
                    dan jumlah penduduk
                    <strong>{{ number_format($country->population ?? 0) }}</strong>.

                </p>

                <p>

                    Ibu kota
                    <strong>{{ $country->capital }}</strong>,
                    menggunakan mata uang
                    <strong>{{ $country->currency }}</strong>,
                    dengan suhu rata-rata
                    <strong>{{ $country->temperature }} °C</strong>
                    serta kondisi cuaca
                    <strong>{{ $country->weather }}</strong>.

                </p>

            </div>

        </div>

    </div>

</div>

@endsection