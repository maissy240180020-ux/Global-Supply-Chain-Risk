@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="mb-4">
        <h2 class="fw-bold">Dashboard</h2>
        <p class="text-muted">
            Selamat datang di Global Supply Chain Risk Intelligence Platform
        </p>
    </div>

    @include('dashboard.cards')

    <div class="row">

        <div class="col-lg-8">

            @include('dashboard.risk-map')

            @include('dashboard.indicators')

        </div>

        <div class="col-lg-4">

            @include('dashboard.top-risk')

        </div>

    </div>

    @include('dashboard.weather')

    @include('dashboard.port-map')

</div>

@endsection