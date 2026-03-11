{{-- 
    File: auth/login.blade.php
    Fungsi: Menampilkan halaman antarmuka login. Tersedia form untuk 
    memasukkan kredensial (email & password) berserta gaya visual khusus.
--}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Platform Aplikasi Absensi Siswa">
    <meta name="author" content="">

    <title>Login - Absensi Siswa</title>

    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #4e73df 0%, #224aba 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #4e73df 0%, #2e59a7 50%, #1a2c5e 100%);
            animation: gradientShift 15s ease infinite;
            z-index: -1;
        }

        @keyframes gradientShift {
            0%, 100% { background: linear-gradient(135deg, #4e73df 0%, #2e59a7 50%, #1a2c5e 100%); }
            50% { background: linear-gradient(135deg, #2e59a7 0%, #1a2c5e 50%, #4e73df 100%); }
        }

        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            width: 100%;
            padding: 20px;
        }

        .card {
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95) !important;
            animation: slideUp 0.8s ease-out;
            overflow: hidden;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-body {
            padding: 60px 50px !important;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #4e73df 0%, #224aba 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #6c757d;
            font-size: 15px;
            font-weight: 500;
            margin: 0;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border: 2px solid #e9ecef !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #f8f9fa !important;
        }

        .form-control:focus {
            border-color: #4e73df !important;
            background-color: #fff !important;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1) !important;
        }

        .form-control::placeholder {
            color: #999;
            font-weight: 400;
        }

        .input-group-icon {
            position: relative;
        }

        .input-group-icon i {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #4e73df;
            font-size: 18px;
            opacity: 0.6;
            transition: opacity 0.3s ease;
        }

        .form-control:focus ~ i,
        .input-group-icon:focus-within i {
            opacity: 1;
        }

        .form-control-with-icon {
            padding-right: 45px;
        }

        .alert {
            border: none !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            font-size: 14px;
            animation: slideDown 0.5s ease-out;
            margin-bottom: 24px !important;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background-color: #fff5f5 !important;
            color: #721c24 !important;
            border: 1px solid #f8d7da !important;
        }

        .custom-checkbox {
            margin-bottom: 20px;
        }

        .custom-control-label {
            font-size: 14px;
            color: #555;
            font-weight: 500;
            cursor: pointer;
            margin-top: 2px;
        }

        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #4e73df !important;
            border-color: #4e73df !important;
        }

        .btn-login {
            width: 100%;
            padding: 12px 20px !important;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #4e73df 0%, #224aba 100%);
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(78, 115, 223, 0.3);
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(78, 115, 223, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }

        .login-footer a {
            color: #4e73df;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-footer a:hover {
            color: #224aba;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 40px 30px !important;
            }

            .login-header h1 {
                font-size: 26px;
            }

            .btn-login {
                padding: 10px 15px !important;
                font-size: 14px;
            }
        }

        /* Loading animation untuk button */
        .btn-login.loading {
            opacity: 0.8;
            pointer-events: none;
        }

        .btn-login.loading::after {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            margin-left: 8px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="card" style="width: 100%; max-width: 450px;">
            <div class="card-body">
                <!-- Header -->
                <div class="login-header">
                    <h1>
                        <i class="fas fa-user-check" style="margin-right: 10px;"></i>
                        SISAB
                    </h1>
                    <p>Sistem Manajemen Kehadiran Siswa</p>
                </div>

                <!-- Alert Error -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Form Login -->
                <form action="{{ route('login') }}" method="POST" id="loginForm">
                    @csrf

                    <!-- Email Input -->
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope" style="margin-right: 6px; color: #4e73df;"></i>
                            Email / Login
                        </label>
                        <div class="input-group-icon">
                            <input type="email" 
                                   class="form-control form-control-with-icon @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Masukkan email Anda"
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock" style="margin-right: 6px; color: #4e73df;"></i>
                            Kata Sandi
                        </label>
                        <div class="input-group-icon">
                            <input type="password" 
                                   class="form-control form-control-with-icon @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Masukkan kata sandi Anda"
                                   required>
                            <i class="fas fa-eye toggle-password" style="cursor: pointer;" onclick="togglePassword()"></i>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                            <label class="custom-control-label" for="remember">
                                <i class="fas fa-check-circle" style="margin-right: 6px; opacity: 0.6;"></i>
                                Ingat Saya
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-login" id="submitBtn">
                        <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                        Masuk
                    </button>
                </form>

                <!-- Footer -->
                <div class="login-footer">
                    <p style="margin: 0; font-size: 12px; color: #999;">
                        © 2026 Sistem Manajemen Absensi Siswa
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Form Submission Handler
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        // Add focus effects
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.parentElement.style.opacity = '1';
            });
        });
    </script>

</body>

</html> 