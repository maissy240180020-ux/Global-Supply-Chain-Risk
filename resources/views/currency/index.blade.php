@extends('layouts.app')

@section('title','Dashboard Nilai Tukar')

@section('content')

<div class="container-fluid">

<h2 class="fw-bold mb-4">
💱 Dashboard Nilai Tukar
</h2>

@if($kurs)

<div class="row">

<div class="col-md-4">

<div class="card shadow-sm border-0">

<div class="card-body text-center">

<h6 class="text-muted">
USD → IDR
</h6>

<h2 class="text-primary">

{{ number_format($kurs['rates']['IDR'],2) }}

</h2>

<small class="text-success">

Realtime

</small>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow-sm border-0">

<div class="card-body text-center">

<h6 class="text-muted">
USD → EUR
</h6>

<h2 class="text-success">

{{ $kurs['rates']['EUR'] }}

</h2>

<small>

Realtime

</small>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow-sm border-0">

<div class="card-body text-center">

<h6 class="text-muted">
USD → JPY
</h6>

<h2 class="text-warning">

{{ $kurs['rates']['JPY'] }}

</h2>

<small>

Realtime

</small>

</div>

</div>

</div>

</div>

<div class="card mt-4 shadow-sm">

<div class="card-header">

Nilai Tukar Hari Ini

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th>Tanggal Update</th>

<td>{{ $kurs['date'] }}</td>

</tr>

<tr>

<th>Base Currency</th>

<td>{{ $kurs['base'] }}</td>

</tr>

<tr>

<th>USD → IDR</th>

<td>{{ number_format($kurs['rates']['IDR'],2) }}</td>

</tr>

<tr>

<th>USD → EUR</th>

<td>{{ $kurs['rates']['EUR'] }}</td>

</tr>

<tr>

<th>USD → JPY</th>

<td>{{ $kurs['rates']['JPY'] }}</td>

</tr>

</table>

</div>

</div>

@else

<div class="alert alert-danger">

Gagal mengambil data dari Currency API.

</div>

@endif

</div>

@endsection