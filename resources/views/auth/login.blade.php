<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SIMRPG</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        /* Ambient Glowing Background Orbs (Slate / Grey theme) */
        .glow-orb-1 {
            position: absolute;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(71, 85, 105, 0.15) 0%, rgba(71, 85, 105, 0) 70%);
            top: -50px;
            left: -50px;
            z-index: 1;
            pointer-events: none;
        }

        .glow-orb-2 {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(51, 65, 85, 0.15) 0%, rgba(51, 65, 85, 0) 70%);
            bottom: -100px;
            right: -100px;
            z-index: 1;
            pointer-events: none;
        }

        .login-container {
            position: relative;
            z-index: 2;
        }

        .login-card {
            width: 380px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
            transition: transform 0.3s ease;
        }

        .logo-container {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #475569, #1e293b);
            color: white;
            border-radius: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 26px;
            margin: 0 auto 12px auto;
            box-shadow: 0 8px 20px rgba(71, 85, 105, 0.25);
            transition: transform 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05) rotate(5deg);
        }

        .judul {
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.5px;
            font-size: 1.4rem;
            margin-bottom: 2px;
        }

        .subjudul {
            color: #64748b;
            font-size: 0.78rem;
            line-height: 1.4;
            margin-bottom: 20px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.95rem;
            transition: color 0.2s;
            pointer-events: none;
        }

        .form-control-custom {
            padding-left: 44px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            height: 44px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #1e293b;
            background-color: #f8fafc;
            transition: all 0.2s ease;
            width: 100%;
            box-sizing: border-box;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #475569;
            box-shadow: 0 0 0 3px rgba(71, 85, 105, 0.12);
            background-color: #ffffff;
        }

        .form-control-custom:focus + i {
            color: #475569;
        }

        .form-label-custom {
            font-weight: 600;
            color: #334155;
            font-size: 0.78rem;
            margin-bottom: 4px;
            display: block;
        }

        .btn-login {
            background: linear-gradient(135deg, #475569 0%, #334155 100%);
            border: none;
            border-radius: 10px;
            height: 44px;
            font-weight: 600;
            font-size: 0.88rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(71, 85, 105, 0.15);
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(71, 85, 105, 0.25);
            filter: brightness(1.05);
        }

        .btn-login:active {
            transform: translateY(0.5px);
        }

        .form-check-label {
            font-size: 0.78rem;
            color: #475569;
            font-weight: 500;
            user-select: none;
        }

        .form-check-input:checked {
            background-color: #475569;
            border-color: #475569;
        }

        .footer {
            color: #94a3b8;
            font-size: 0.7rem;
            margin-top: 20px;
            font-weight: 500;
        }

        .alert-custom {
            border-radius: 10px;
            font-size: 0.78rem;
            font-weight: 500;
            padding: 10px 14px;
            border: none;
        }
    </style>
</head>

<body>

    <!-- Ambient Lights -->
    <div class="glow-orb-1"></div>
    <div class="glow-orb-2"></div>

    <div class="login-container">
        <div class="card login-card border-0">
            <div class="card-body p-4">

                <!-- Header / Logo -->
                <div class="text-center">
                    <div class="logo-container">
                        <i class="bi bi-globe2"></i>
                    </div>
                    <h3 class="judul">SIMRPG</h3>
                    <p class="subjudul">Sistem Monitoring Risiko Rantai Pasok Global</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-custom mb-3.5 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-octagon-fill text-danger fs-5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login.proses') }}" method="POST">
                    @csrf

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label class="form-label-custom">Email</label>
                        <div class="input-wrapper">
                            <input type="email" name="email" class="form-control-custom" placeholder="Masukkan email Anda" required value="maissy@gmail.com">
                            <i class="bi bi-envelope"></i>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="mb-3">
                        <label class="form-label-custom">Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" class="form-control-custom" placeholder="Masukkan password Anda" required value="maissy123">
                            <i class="bi bi-lock"></i>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="d-flex justify-content-between align-items-center mb-3.5">
                        <div class="form-check m-0">
                            <input class="form-check-input" type="checkbox" id="ingat" name="remember">
                            <label class="form-check-label" for="ingat">Ingat Saya</label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-login w-100">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Masuk ke Sistem
                    </button>
                </form>

                <!-- Demo Account Notice -->
                <div class="mt-3.5 p-3 d-flex align-items-start gap-2.5" style="border-radius: 10px; background-color: rgba(71, 85, 105, 0.05); border: 1px dashed rgba(71, 85, 105, 0.25);">
                    <i class="bi bi-info-circle-fill" style="font-size: 0.95rem; margin-top: 1px; color: #475569;"></i>
                    <div style="font-size: 0.7rem; color: #475569; line-height: 1.4;">
                        <span class="fw-bold">Akun Demo Aktif:</span><br>
                        Email: <code class="text-dark bg-white px-1 py-0.5 rounded border border-light" style="font-size: 0.65rem;">maissy@gmail.com</code><br>
                        Password: <code class="text-dark bg-white px-1 py-0.5 rounded border border-light" style="font-size: 0.65rem;">maissy123</code>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center footer border-top border-light pt-2.5">
                    © 2026 Global Supply Chain Risk Monitoring System
                </div>

            </div>
        </div>
    </div>

</body>

</html>