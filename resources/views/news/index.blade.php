@extends('layouts.app')

@section('title','Berita Supply Chain')

@section('content')

<div class="container-fluid">

<h2 class="fw-bold mb-4">

📰 Berita Supply Chain

</h2>

@if(count($berita))

@foreach($berita as $item)

<div class="card shadow-sm mb-3">

<div class="card-body">

<h5>

{{ $item['judul'] }}

</h5>

<small class="text-muted">

{{ $item['tanggal'] }}

</small>

<br><br>

<a href="{{ $item['link'] }}" target="_blank"
class="btn btn-primary btn-sm">

Baca Berita

</a>

</div>

</div>

@endforeach

@else

<div class="alert alert-danger">

Berita tidak dapat dimuat.

</div>

@endif

</div>

@endsection