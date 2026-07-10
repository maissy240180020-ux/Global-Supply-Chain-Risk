@extends('layouts.app')

@section('title','Dashboard Visualisasi')

@section('content')

<div class="container-fluid">

<h2 class="fw-bold mb-4">
📊 Dashboard Visualisasi
</h2>

<div class="card shadow-sm">

<div class="card-body">

<canvas id="grafikRisiko" height="120"></canvas>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

new Chart(document.getElementById('grafikRisiko'),{

type:'bar',

data:{

labels:['Risiko Tinggi','Risiko Sedang','Risiko Rendah'],

datasets:[{

label:'Jumlah Negara',

data:[

{{ \App\Models\Country::where('risk_level','High')->count() }},

{{ \App\Models\Country::where('risk_level','Medium')->count() }},

{{ \App\Models\Country::where('risk_level','Low')->count() }}

]

}]

}

});

</script>

@endsection