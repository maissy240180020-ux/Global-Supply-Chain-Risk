@extends('layouts.app')

@section('title','Countries')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">

            🌍 Countries

        </h2>

        <button class="btn btn-primary">

            + Add Country

        </button>

    </div>

    <div class="card dashboard-card">

        <div class="card-body">

            <div class="row mb-3">

                <div class="col-md-4">

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Cari Negara...">

                </div>

            </div>

            <table class="table table-hover">

                <thead>

                <tr>

                    <th>No</th>

                    <th>Flag</th>

                    <th>Country</th>

                    <th>Risk</th>

                    <th>Currency</th>

                    <th>Weather</th>

                    <th>Action</th>

                </tr>

                </thead>

                <tbody>

                <tr>

                    <td>1</td>

                    <td>🇮🇩</td>

                    <td>Indonesia</td>

                    <td>
                        <span class="badge bg-warning">
                            45
                        </span>
                    </td>

                    <td>IDR</td>

                    <td>30°C</td>

                    <td>

                        <button class="btn btn-sm btn-primary">

                            Edit

                        </button>

                        <button class="btn btn-sm btn-danger">

                            Delete

                        </button>

                    </td>

                </tr>

                <tr>

                    <td>2</td>

                    <td>🇨🇳</td>

                    <td>China</td>

                    <td>

                        <span class="badge bg-danger">

                            76

                        </span>

                    </td>

                    <td>CNY</td>

                    <td>27°C</td>

                    <td>

                        <button class="btn btn-sm btn-primary">

                            Edit

                        </button>

                        <button class="btn btn-sm btn-danger">

                            Delete

                        </button>

                    </td>

                </tr>

                <tr>

                    <td>3</td>

                    <td>🇯🇵</td>

                    <td>Japan</td>

                    <td>

                        <span class="badge bg-success">

                            22

                        </span>

                    </td>

                    <td>JPY</td>

                    <td>24°C</td>

                    <td>

                        <button class="btn btn-sm btn-primary">

                            Edit

                        </button>

                        <button class="btn btn-sm btn-danger">

                            Delete

                        </button>

                    </td>

                </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection