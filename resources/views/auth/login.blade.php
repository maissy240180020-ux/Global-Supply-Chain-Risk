<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login SIMRPG</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>

        body{

            margin:0;

            padding:0;

            background:linear-gradient(135deg,#0d6efd,#5fa8ff);

            font-family:'Segoe UI',sans-serif;

            height:100vh;

            display:flex;

            justify-content:center;

            align-items:center;

        }

        .login-card{

            width:420px;

            background:white;

            border-radius:18px;

            box-shadow:0 15px 35px rgba(0,0,0,.2);

        }

        .logo{

            width:85px;

            height:85px;

            background:#0d6efd;

            color:white;

            border-radius:50%;

            display:flex;

            justify-content:center;

            align-items:center;

            font-size:40px;

            margin:auto;

        }

        .judul{

            font-weight:bold;

            margin-top:15px;

            color:#0d6efd;

        }

        .subjudul{

            color:#6c757d;

            font-size:14px;

        }

        .form-control{

            border-radius:10px;

            height:45px;

        }

        .input-group-text{

            border-radius:10px 0 0 10px;

        }

        .btn-login{

            border-radius:10px;

            height:45px;

            font-weight:bold;

        }

        .footer{

            color:#6c757d;

            font-size:13px;

        }

    </style>

</head>

<body>

<div class="card login-card">

    <div class="card-body p-4">

        <div class="text-center">

            <div class="logo">

                <i class="bi bi-globe2"></i>

            </div>

            <h3 class="judul">

                SIMRPG

            </h3>

            <p class="subjudul">

                Sistem Monitoring Risiko
                <br>
                Rantai Pasok Global

            </p>

        </div>

        @if(session('error'))

            <div class="alert alert-danger">

                {{ session('error') }}

            </div>

        @endif

        <form action="{{ route('login.proses') }}" method="POST">

            @csrf

            <div class="mb-3">

                <label class="form-label">

                    Email

                </label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-envelope-fill"></i>

                    </span>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan email"
                        required>

                </div>

            </div>

            <div class="mb-3">

                <label class="form-label">

                    Password

                </label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-lock-fill"></i>

                    </span>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        required>

                </div>

            </div>

            <div class="form-check mb-3">

                <input
                    class="form-check-input"
                    type="checkbox"
                    id="ingat">

                <label
                    class="form-check-label"
                    for="ingat">

                    Ingat Saya

                </label>

            </div>

            <button
                class="btn btn-primary btn-login w-100">

                <i class="bi bi-box-arrow-in-right"></i>

                Masuk ke Sistem

            </button>

        </form>

        <hr>

        <div class="text-center footer">

            © 2026

            <br>

            Global Supply Chain Risk Monitoring

        </div>

    </div>

</div>

</body>

</html>