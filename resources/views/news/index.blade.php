@extends('layouts.app')

@section('title','Analisis Berita')

@section('content')

<div class="container-fluid">

<h2 class="fw-bold mb-4">
📰 Analisis Berita
</h2>

<div class="row">

<div class="col-md-4">

<div class="card shadow-sm">

<div class="card-body">

<h5>Kenaikan Biaya Pengiriman</h5>

<p>

Biaya logistik global mengalami kenaikan sebesar 5%.

</p>

<span class="badge bg-danger">

Risiko Tinggi

</span>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow-sm">

<div class="card-body">

<h5>Ekspor Indonesia Meningkat</h5>

<p>

Nilai ekspor meningkat dibanding bulan sebelumnya.

</p>

<span class="badge bg-success">

Positif

</span>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow-sm">

<div class="card-body">

<h5>Cuaca Ekstrem</h5>

<p>

Cuaca ekstrem mempengaruhi jalur distribusi barang.

</p>

<span class="badge bg-warning text-dark">

Perhatian

</span>

</div>

</div>

</div>

</div>

</div>

@endsection