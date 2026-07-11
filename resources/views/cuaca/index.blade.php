@extends('layouts.app')

@section('title','Pemantauan Cuaca')

@section('content')

<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        🌦️ Pemantauan Cuaca (Realtime)
    </h2>

    @if($cuaca)

    <div class="row">

        <div class="col-md-4">

            <div class="card shadow-sm border-0">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Suhu Saat Ini
                    </h6>

                    <h2 class="text-primary">
                        {{ $cuaca['temperature_2m'] }} °C
                    </h2>

                    <small>
                        Jakarta
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm border-0">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Kecepatan Angin
                    </h6>

                    <h2 class="text-success">
                        {{ $cuaca['wind_speed_10m'] }} km/jam
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm border-0">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Arah Angin
                    </h6>

                    <h2 class="text-info">
                        {{ $cuaca['wind_direction_10m'] }}°
                    </h2>

                </div>

            </div>

        </div>

    </div>

    <div class="card mt-4 shadow-sm border-0">

        <div class="card-header">
            Informasi Cuaca Realtime
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th>Kota</th>
                    <td>Jakarta</td>
                </tr>

                <tr>
                    <th>Suhu</th>
                    <td>{{ $cuaca['temperature_2m'] }} °C</td>
                </tr>

                <tr>
                    <th>Kecepatan Angin</th>
                    <td>{{ $cuaca['wind_speed_10m'] }} km/jam</td>
                </tr>

                <tr>
                    <th>Arah Angin</th>
                    <td>{{ $cuaca['wind_direction_10m'] }}°</td>
                </tr>

                <tr>
                    <th>Update</th>
                    <td>{{ $cuaca['time'] }}</td>
                </tr>

            </table>

        </div>

    </div>

    @else

    <div class="alert alert-danger">

        Gagal mengambil data cuaca dari API.

    </div>

    @endif

</div>

@endsection