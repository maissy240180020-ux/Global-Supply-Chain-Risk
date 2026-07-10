@extends('layouts.app')

@section('title','Daftar Pantauan')

@section('content')

<div class="container-fluid">

<h2 class="fw-bold mb-4">
⭐ Daftar Pantauan
</h2>

<div class="card shadow-sm">

<div class="card-body">

<table class="table table-hover">

<thead class="table-light">

<tr>

<th>No</th>

<th>Negara</th>

<th>Status Risiko</th>

<th>Skor Risiko</th>

</tr>

</thead>

<tbody>

<tr>

<td>1</td>

<td>Indonesia</td>

<td>
<span class="badge bg-warning">
Sedang
</span>
</td>

<td>45</td>

</tr>

<tr>

<td>2</td>

<td>China</td>

<td>
<span class="badge bg-danger">
Tinggi
</span>
</td>

<td>80</td>

</tr>

<tr>

<td>3</td>

<td>Jepang</td>

<td>
<span class="badge bg-success">
Rendah
</span>
</td>

<td>20</td>

</tr>

</tbody>

</table>

</div>

</div>

</div>

@endsection