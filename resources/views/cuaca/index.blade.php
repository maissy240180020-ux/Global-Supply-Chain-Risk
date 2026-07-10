@extends('layouts.app')

@section('title','Pemantauan Cuaca')

@section('content')

<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        🌦️ Pemantauan Cuaca
    </h2>

    <div class="row">

        <div class="col-md-3">

            <div class="card shadow-sm border-0">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Suhu Saat Ini
                    </h6>

                    <h2 class="text-primary">
                        29°C
                    </h2>

                    <small>
                        Jakarta
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-0">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Kelembapan
                    </h6>

                    <h2 class="text-info">
                        78%
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-0">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Kecepatan Angin
                    </h6>

                    <h2 class="text-success">
                        14 Km/Jam
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-0">

                <div class="card-body text-center">

                    <h6 class="text-muted">
                        Kondisi
                    </h6>

                    <h2>
                        ☀️
                    </h2>

                    <small>
                        Cerah
                    </small>

                </div>

            </div>

        </div>

    </div>

    <div class="card mt-4 shadow-sm border-0">

        <div class="card-header">

            Prakiraan Cuaca Hari Ini

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead class="table-light">

                    <tr>

                        <th>Waktu</th>

                        <th>Suhu</th>

                        <th>Kondisi</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>Pagi</td>

                        <td>26°C</td>

                        <td>Cerah</td>

                    </tr>

                    <tr>

                        <td>Siang</td>

                        <td>31°C</td>

                        <td>Cerah Berawan</td>

                    </tr>

                    <tr>

                        <td>Sore</td>

                        <td>29°C</td>

                        <td>Berawan</td>

                    </tr>

                    <tr>

                        <td>Malam</td>

                        <td>25°C</td>

                        <td>Hujan Ringan</td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection