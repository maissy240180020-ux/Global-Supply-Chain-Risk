@extends('layouts.app')

@section('title','REST Countries API')

@section('content')

<div class="card shadow-sm">

    <div class="card-header">

        <h4>REST Countries API</h4>

    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>No</th>

                    <th>Bendera</th>

                    <th>Negara</th>

                    <th>Ibukota</th>

                    <th>Region</th>

                    <th>Populasi</th>

                </tr>

            </thead>

            <tbody>

                @foreach($countries as $country)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>

    @if(isset($country['flags']) && isset($country['flags']['png']))
        <img src="{{ $country['flags']['png'] }}" width="50">
    @else
        -
    @endif

</td>

                    <td>{{ $country['name']['common'] }}</td>

                    <td>{{ $country['capital'][0] ?? '-' }}</td>

                    <td>{{ $country['region'] }}</td>

                    <td>{{ number_format($country['population']) }}</td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection