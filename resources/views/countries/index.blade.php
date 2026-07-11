@extends('layouts.app')

@section('title','Data Negara')

@section('content')

<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        🌍 Data Negara
    </h2>

    <div class="d-flex justify-content-between align-items-center mb-4">

        <form action="{{ route('countries.index') }}"
              method="GET"
              class="d-flex w-50">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control me-2"
                placeholder="Cari nama negara...">

            <button class="btn btn-primary">

                <i class="bi bi-search"></i>

                Cari

            </button>

        </form>

        <a href="{{ route('countries.sync') }}" class="btn btn-success">
    Import Data API
</a>

        <a href="{{ route('countries.create') }}"
           class="btn btn-success">

            <i class="bi bi-plus-circle"></i>

            Tambah Negara

        </a>

    </div>

    <div class="row mb-4">

    <div class="col-md-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center">

                <h6 class="text-muted">

                    Total Negara

                </h6>

                <h2 class="fw-bold text-primary">

                    {{ $countries->total() }}

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center">

                <h6 class="text-muted">

                    Risiko Tinggi

                </h6>

                <h2 class="fw-bold text-danger">

                    {{ \App\Models\Country::where('risk_level','High')->count() }}

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center">

                <h6 class="text-muted">

                    Rata-rata Skor Risiko

                </h6>

                <h2 class="fw-bold text-warning">

                    {{ number_format(\App\Models\Country::avg('risk_score'),1) }}

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center">

                <h6 class="text-muted">

                    Total GDP

                </h6>

                <h5 class="fw-bold text-success">

                    {{ number_format(\App\Models\Country::sum('gdp'),0) }}

                </h5>

            </div>

        </div>

    </div>

</div>

    <div class="card dashboard-card">

        <div class="card-body">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th>No</th>

                        <th>Bendera</th>

                        <th>Nama Negara</th>

                        <th>Skor Risiko</th>

                        <th>Mata Uang</th>

                        <th>Cuaca</th>

                        <th class="text-center">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($countries as $country)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td style="font-size:22px;">🌍</td>

                        <td>

                            <strong>

                                {{ $country->country_name }}

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

                                {{ number_format($country->risk_score,0) }}

                            </span>

                        </td>

                        <td>

                            {{ $country->currency }}

                        </td>

                        <td>

                            <strong>

                                {{ $country->temperature }}°C

                            </strong>

                            <br>

                            <small class="text-muted">

                                {{ $country->weather }}

                            </small>

                        </td>

                        <td class="text-center">

                            <a href="{{ route('countries.show',$country->id) }}"
                               class="btn btn-info btn-sm">

                                <i class="bi bi-eye"></i>

                                Detail

                            </a>

                            <a href="{{ route('countries.edit',$country->id) }}"
                               class="btn btn-warning btn-sm">

                                <i class="bi bi-pencil-square"></i>

                                Ubah

                            </a>

                            <form action="{{ route('countries.destroy',$country->id) }}"
                                  method="POST"
                                  class="delete-form d-inline">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-danger btn-sm">

                                    <i class="bi bi-trash"></i>

                                    Hapus

                                </button>

                            </form>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="7" class="text-center py-5">

                            <i class="bi bi-database fs-1 text-secondary"></i>

                            <br><br>

                            Belum ada data negara.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

            <div class="d-flex justify-content-between align-items-center mt-4">

                <small class="text-muted">

                    Menampilkan

                    {{ $countries->firstItem() ?? 0 }}

                    sampai

                    {{ $countries->lastItem() ?? 0 }}

                    dari

                    {{ $countries->total() }}

                    data.

                </small>

                <div>

                    {{ $countries->links('pagination::bootstrap-5') }}

                </div>

            </div>

        </div>

    </div>

</div>

@endsection