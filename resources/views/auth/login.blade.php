<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SIMRPG</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #090f1e 0%, #1e3a8a 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            color: #f8fafc;
        }

        /* Abstract Premium Background Elements */
        .ambient-glow-1 {
            position: absolute;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 60%);
            top: -20vh;
            left: -15vw;
            border-radius: 50%;
            z-index: 1;
            animation: pulse-glow 8s ease-in-out infinite alternate;
        }

        .ambient-glow-2 {
            position: absolute;
            width: 40vw;
            height: 40vw;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 60%);
            bottom: -15vh;
            right: -10vw;
            border-radius: 50%;
            z-index: 1;
            animation: pulse-glow 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes pulse-glow {
            0% { transform: scale(1) translate(0, 0); opacity: 0.8; }
            100% { transform: scale(1.1) translate(20px, 20px); opacity: 1; }
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 380px; /* Reduced from 420px */
            padding: 15px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            padding: 30px 32px; /* Reduced from 40px */
            overflow: hidden;
            position: relative;
        }

        /* Top highlight inside the card */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #10b981, #3b82f6);
            background-size: 200% auto;
            animation: gradient-shift 3s linear infinite;
        }

        @keyframes gradient-shift {
            to { background-position: 200% center; }
        }

        .logo-box {
            width: 52px; /* Reduced */
            height: 52px; /* Reduced */
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px; /* Reduced */
            color: #ffffff;
            margin: 0 auto 16px auto;
            box-shadow: 0 8px 20px -5px rgba(59, 130, 246, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-title {
            font-size: 1.35rem; /* Reduced */
            font-weight: 700;
            color: #ffffff;
            text-align: center;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .login-subtitle {
            font-size: 0.8rem; /* Reduced */
            color: #94a3b8;
            text-align: center;
            margin-bottom: 24px; /* Reduced */
            line-height: 1.5;
        }

        .form-label-custom {
            font-size: 0.75rem; /* Reduced */
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 6px;
            display: block;
        }

        .input-group-custom {
            margin-bottom: 18px;
        }

        .input-wrapper-custom {
            position: relative;
        }

        .input-wrapper-custom i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1rem;
            transition: color 0.3s ease;
            pointer-events: none;
            z-index: 2;
        }

        .form-control-custom {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 10px 14px 10px 42px;
            font-size: 0.88rem;
            color: #ffffff;
            transition: all 0.3s ease;
            box-sizing: border-box;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Fix Browser Autofill styling for dark theme */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #0f172a inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(15, 23, 42, 0.8);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25), inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-control-custom:focus + i {
            color: #3b82f6;
        }
        
        .form-control-custom::placeholder {
            color: #475569;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px -6px rgba(37, 99, 235, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px -6px rgba(37, 99, 235, 0.8);
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }
        
        .btn-login:active {
            transform: translateY(1px);
        }

        .btn-login .spinner-border {
            display: none;
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }

        .btn-login.is-loading .btn-text {
            display: none;
        }
        
        .btn-login.is-loading .spinner-border {
            display: inline-block;
        }

        .form-check-label {
            font-size: 0.85rem;
            color: #94a3b8;
            cursor: pointer;
        }

        .form-check-input {
            background-color: rgba(15, 23, 42, 0.6);
            border-color: rgba(255, 255, 255, 0.2);
            cursor: pointer;
        }
        
        .form-check-input:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
        
        .form-check-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }
        
        .alert-error i {
            font-size: 1.1rem;
            color: #ef4444;
        }

        .footer {
            position: absolute;
            bottom: 24px;
            left: 0;
            width: 100%;
            text-align: center;
            color: #64748b;
            font-size: 0.75rem;
            z-index: 10;
        }
    </style>
</head>

<body>

    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="login-container">
        <div class="login-card">
            
            <div class="logo-box">
                <i class="bi bi-globe2"></i>
            </div>
            
            <h1 class="login-title">SIMRPG</h1>
            <p class="login-subtitle">Silakan masuk menggunakan akun Anda untuk mengakses sistem monitoring.</p>

            @if(session('error'))
                <div class="alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>Email atau password tidak valid.</div>
                </div>
            @endif

            <form action="{{ route('login.proses') }}" method="POST" id="loginForm">
                @csrf

                <div class="input-group-custom">
                    <label class="form-label-custom">Email Address</label>
                    <div class="input-wrapper-custom">
                        <input type="email" name="email" class="form-control-custom" placeholder="contoh@simrpg.com" required autocomplete="email" autofocus>
                        <i class="bi bi-envelope"></i>
                    </div>
                </div>

                <div class="input-group-custom">
                    <label class="form-label-custom">Password</label>
                    <div class="input-wrapper-custom">
                        <input type="password" name="password" class="form-control-custom" placeholder="••••••••" required autocomplete="current-password">
                        <i class="bi bi-lock"></i>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ingat Saya</label>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span class="btn-text d-flex align-items-center gap-2"><i class="bi bi-box-arrow-in-right"></i> Login ke Sistem</span>
                    <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>
            </form>
            
        </div>
    </div>

    <div class="footer">
        &copy; 2026 SIMRPG &ndash; Global Supply Chain Risk Intelligence Platform
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('is-loading');
            btn.disabled = true;
        });
    </script>
</body>

</html>