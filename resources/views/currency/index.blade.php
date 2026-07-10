@extends('layouts.app')

@section('title','Dashboard Nilai Tukar')

@section('content')

<div class="container-fluid">

<h2 class="fw-bold mb-4">
💱 Dashboard Nilai Tukar
</h2>

<div class="row">

<div class="col-md-4">

<div class="card shadow-sm border-0">

<div class="card-body text-center">

<h6 class="text-muted">USD / IDR</h6>

<h2 class="text-primary">16.250</h2>

<small class="text-success">▲ 0.52%</small>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow-sm border-0">

<div class="card-body text-center">

<h6 class="text-muted">EUR / IDR</h6>

<h2 class="text-success">18.200</h2>

<small class="text-danger">▼ 0.20%</small>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow-sm border-0">

<div class="card-body text-center">

<h6 class="text-muted">JPY / IDR</h6>

<h2 class="text-warning">112</h2>

<small class="text-success">▲ 0.10%</small>

</div>

</div>

</div>

</div>

<div class="card mt-4 shadow-sm">

<div class="card-header">

Daftar Nilai Tukar

</div>

<div class="card-body">

<table class="table table-bordered">

<thead>

<tr>

<th>Mata Uang</th>

<th>Nilai Tukar</th>

<th>Perubahan</th>

</tr>

</thead>

<tbody>

<tr>

<td>USD</td>

<td>16.250</td>

<td class="text-success">Naik</td>

</tr>

<tr>

<td>EUR</td>

<td>18.200</td>

<td class="text-danger">Turun</td>

</tr>

<tr>

<td>JPY</td>

<td>112</td>

<td class="text-success">Naik</td>

</tr>

</tbody>

</table>

</div>

</div>

</div>

@endsection