@extends('layouts.app')

@section('title','Dashboard Admin')

@section('content')

<div class="container-fluid">

<h2 class="fw-bold mb-4">
👨‍💼 Dashboard Admin
</h2>

<div class="row">

<div class="col-md-3">

<div class="card shadow-sm">

<div class="card-body text-center">

<h6>Total Negara</h6>

<h2>{{ \App\Models\Country::count() }}</h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card shadow-sm">

<div class="card-body text-center">

<h6>Risiko Tinggi</h6>

<h2 class="text-danger">

{{ \App\Models\Country::where('risk_level','High')->count() }}

</h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card shadow-sm">

<div class="card-body text-center">

<h6>Risiko Sedang</h6>

<h2 class="text-warning">

{{ \App\Models\Country::where('risk_level','Medium')->count() }}

</h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card shadow-sm">

<div class="card-body text-center">

<h6>Risiko Rendah</h6>

<h2 class="text-success">

{{ \App\Models\Country::where('risk_level','Low')->count() }}

</h2>

</div>

</div>

</div>

</div>

</div>

@endsection