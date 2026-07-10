@extends('layouts.app')

@section('title','Pemantauan Risiko')

@section('content')

<div class="container-fluid">

    <h2 class="fw-bold mb-4">

        ⚠️ Pemantauan Risiko

    </h2>

    <!-- Ringkasan Risiko -->
    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body text-center">

                    <h6 class="text-muted">

                        Risiko Tinggi

                    </h6>

                    <h2 class="text-danger fw-bold">

                        {{ $highRisk }}

                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body text-center">

                    <h6 class="text-muted">

                        Risiko Sedang

                    </h6>

                    <h2 class="text-warning fw-bold">

                        {{ $mediumRisk }}

                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body text-center">

                    <h6 class="text-muted">

                        Risiko Rendah

                    </h6>

                    <h2 class="text-success fw-bold">

                        {{ $lowRisk }}

                    </h2>

                </div>

            </div>

        </div>

    </div>

    <!-- Tabel -->
    <div class="card dashboard-card">

        <div class="card-body">

            <div class="d-flex justify-content-between mb-4">

                <form
                    action="{{ route('risk.index') }}"
                    method="GET"
                    class="d-flex">

                    <select
                        name="risk"
                        class="form-select me-2">

                        <option value="">

                            Semua Tingkat Risiko

                        </option>

                        <option
                            value="High"
                            {{ request('risk')=='High' ? 'selected':'' }}>

                            Risiko Tinggi

                        </option>

                        <option
                            value="Medium"
                            {{ request('risk')=='Medium' ? 'selected':'' }}>

                            Risiko Sedang

                        </option>

                        <option
                            value="Low"
                            {{ request('risk')=='Low' ? 'selected':'' }}>

                            Risiko Rendah

                        </option>

                    </select>

                    <button class="btn btn-primary">

                        <i class="bi bi-funnel"></i>

                        Filter

                    </button>

                </form>

                <a
                    href="{{ route('risk.index') }}"
                    class="btn btn-secondary">

                    <i class="bi bi-arrow-clockwise"></i>

                    Muat Ulang

                </a>

            </div>

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th>No</th>

                        <th>Nama Negara</th>

                        <th>Skor Risiko</th>

                        <th>Tingkat Risiko</th>

                        <th>Suhu</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($countries as $country)

                    <tr>

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            {{ $country->country_name }}

                        </td>

                        <td>

                            <strong>

                                {{ number_format($country->risk_score,0) }}

                            </strong>

                        </td>

                        <td>

                            @php

                                $badge='bg-success';

                                if($country->risk_level=='Medium'){
                                    $badge='bg-warning text-dark';
                                }

                                if($country->risk_level=='High'){
                                    $badge='bg-danger';
                                }

                            @endphp

                            <span class="badge {{ $badge }}">

                                @if($country->risk_level=='High')

                                    Tinggi

                                @elseif($country->risk_level=='Medium')

                                    Sedang

                                @else

                                    Rendah

                                @endif

                            </span>

                        </td>

                        <td>

                            {{ $country->temperature }}°C

                        </td>

                        <td>

                            @if($country->risk_level=='High')

                                <span class="badge bg-danger">

                                    🔴 Waspada

                                </span>

                            @elseif($country->risk_level=='Medium')

                                <span class="badge bg-warning text-dark">

                                    🟡 Perhatian

                                </span>

                            @else

                                <span class="badge bg-success">

                                    🟢 Aman

                                </span>

                            @endif

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="6" class="text-center py-5">

                            <i class="bi bi-database fs-1 text-secondary"></i>

                            <br><br>

                            Belum ada data risiko.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection