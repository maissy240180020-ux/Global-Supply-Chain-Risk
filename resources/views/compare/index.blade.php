@extends('layouts.app')

@section('title','Compare Countries')

@section('content')

<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        Perbandingan Negara
    </h2>

    <div class="card dashboard-card">

        <div class="card-body">

            <form action="{{ route('compare.compare') }}" method="POST">

                @csrf

                <div class="row align-items-end">

                    {{-- Country 1 --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Negara Pertama
                        </label>

                        <select
                            id="country1"
                            name="country1"
                            class="form-select">

                            @foreach($countries as $country)

                                <option
                                    value="{{ $country->id }}"
                                    @isset($countryA)
                                        {{ $countryA->id == $country->id ? 'selected' : '' }}
                                    @endisset>

                                    {{ $country->country_name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- Country 2 --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Negara Kedua
                        </label>

                        <select
                            id="country2"
                            name="country2"
                            class="form-select">

                            @foreach($countries as $country)

                                <option
                                    value="{{ $country->id }}"
                                    @isset($countryB)
                                        {{ $countryB->id == $country->id ? 'selected' : '' }}
                                    @endisset>

                                    {{ $country->country_name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- Button --}}
                    <div class="col-md-4">

                        <label class="form-label">&nbsp;</label>

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary flex-fill">

                                <i class="bi bi-bar-chart-fill"></i>
                                Bandingkan

                            </button>

                            <button
                                type="button"
                                id="swapBtn"
                                class="btn btn-warning">

                                <i class="bi bi-arrow-left-right"></i>
                                Tukar

                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

    @if(isset($countryA) && isset($countryB))

    <div class="card dashboard-card mt-4">

        <div class="card-body">

            <h4 class="fw-bold mb-4">
                📊 Hasil Perbandingan
            </h4>

            {{-- Winner --}}
            @if($countryA->risk_score > $countryB->risk_score)

                <div class="alert alert-danger">

                    🏆
                    <strong>{{ $countryA->country_name }}</strong>

                    memiliki Risk Score lebih tinggi
                    ({{ number_format($countryA->risk_score,2) }})

                </div>

            @elseif($countryB->risk_score > $countryA->risk_score)

                <div class="alert alert-danger">

                    🏆
                    <strong>{{ $countryB->country_name }}</strong>

                    memiliki Risk Score lebih tinggi
                    ({{ number_format($countryB->risk_score,2) }})

                </div>

            @else

                <div class="alert alert-success">

                    ⚖️ Kedua negara memiliki Risk Score yang sama.

                </div>

            @endif

            <table class="table table-bordered align-middle">

                <thead class="table-primary">

                    <tr>

                        <th width="25%">Information</th>

                        <th>{{ $countryA->country_name }}</th>

                        <th>{{ $countryB->country_name }}</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>Capital</td>

                        <td>{{ $countryA->capital }}</td>

                        <td>{{ $countryB->capital }}</td>

                    </tr>

                    <tr>

                        <td>Currency</td>

                        <td>{{ $countryA->currency }}</td>

                        <td>{{ $countryB->currency }}</td>

                    </tr>

                    <tr>

                        <td>Risk Score</td>

                        <td>

                            <strong class="text-warning">

                                {{ number_format($countryA->risk_score,2) }}

                            </strong>

                            <div class="progress mt-2">

                                <div
                                    class="progress-bar bg-warning"
                                    style="width: {{ $countryA->risk_score }}%">

                                </div>

                            </div>

                        </td>

                        <td>

                            <strong class="text-danger">

                                {{ number_format($countryB->risk_score,2) }}

                            </strong>

                            <div class="progress mt-2">

                                <div
                                    class="progress-bar bg-danger"
                                    style="width: {{ $countryB->risk_score }}%">

                                </div>

                            </div>

                        </td>

                    </tr>

                    <tr>

                        <td>Risk Level</td>

                        <td>

                            @if($countryA->risk_level=="High")

                                <span class="badge bg-danger">High</span>

                            @elseif($countryA->risk_level=="Medium")

                                <span class="badge bg-warning text-dark">Medium</span>

                            @else

                                <span class="badge bg-success">Low</span>

                            @endif

                        </td>

                        <td>

                            @if($countryB->risk_level=="High")

                                <span class="badge bg-danger">High</span>

                            @elseif($countryB->risk_level=="Medium")

                                <span class="badge bg-warning text-dark">Medium</span>

                            @else

                                <span class="badge bg-success">Low</span>

                            @endif

                        </td>

                    </tr>

                    <tr>

                        <td>Temperature</td>

                        <td>{{ $countryA->temperature }} °C</td>

                        <td>{{ $countryB->temperature }} °C</td>

                    </tr>

                    <tr>

                        <td>Weather</td>

                        <td>{{ $countryA->weather }}</td>

                        <td>{{ $countryB->weather }}</td>

                    </tr>

                </tbody>

            </table>

            @php

                $difference = abs($countryA->risk_score - $countryB->risk_score);

                $tempDifference = abs($countryA->temperature - $countryB->temperature);

            @endphp

            <div class="card mt-4 border-0 shadow-sm">

                <div class="card-header bg-primary text-white">

                    🤖 AI Risk Analysis

                </div>

                <div class="card-body">

                    <ul class="list-group">

                        <li class="list-group-item d-flex justify-content-between">

                            <span>📈 Selisih Risk Score</span>

                            <strong>{{ $difference }}</strong>

                        </li>

                        <li class="list-group-item d-flex justify-content-between">

                            <span>🌡 Selisih Suhu</span>

                            <strong>{{ $tempDifference }} °C</strong>

                        </li>

                        <li class="list-group-item d-flex justify-content-between">

                            <span>💰 Mata Uang</span>

                            <strong>{{ $countryA->currency }} vs {{ $countryB->currency }}</strong>

                        </li>

                        <li class="list-group-item d-flex justify-content-between">

                            <span>🌤 Cuaca</span>

                            <strong>{{ $countryA->weather }} vs {{ $countryB->weather }}</strong>

                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

    @endif

</div>

@push('scripts')

<script>

document.getElementById('swapBtn').addEventListener('click', function(){

    let a = document.getElementById('country1');

    let b = document.getElementById('country2');

    let temp = a.value;

    a.value = b.value;

    b.value = temp;

});

</script>

@endpush

@endsection