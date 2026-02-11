<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login - Secure Access</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        /* 1. BACKGROUND UTAMA (Biru Elegan & Partikel Bergerak) */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to left, #0f2027, #203a43, #2c5364); /* Opsi 1: Dark Teal Blue */
            background: linear-gradient(135deg, #004e92, #000428); /* Opsi 2: Royal Blue Deep */
            height: 100vh;
            overflow: hidden; /* Agar scrollbar tidak muncul */
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Animasi Kotak-kotak Melayang (Background Animation) */
        .circles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0; /* Di belakang konten */
        }

        .circles li {
            position: absolute;
            display: block;
            list-style: none;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.1); /* Putih transparan */
            animation: animate 25s linear infinite;
            bottom: -150px;
            border-radius: 5px; /* Sedikit melengkung */
        }

        .circles li:nth-child(1) { left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
        .circles li:nth-child(2) { left: 10%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
        .circles li:nth-child(3) { left: 70%; width: 20px; height: 20px; animation-delay: 4s; }
        .circles li:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 18s; }
        .circles li:nth-child(5) { left: 65%; width: 20px; height: 20px; animation-delay: 0s; }
        .circles li:nth-child(6) { left: 75%; width: 110px; height: 110px; animation-delay: 3s; }
        .circles li:nth-child(7) { left: 35%; width: 150px; height: 150px; animation-delay: 7s; }
        .circles li:nth-child(8) { left: 50%; width: 25px; height: 25px; animation-delay: 15s; animation-duration: 45s; }
        .circles li:nth-child(9) { left: 20%; width: 15px; height: 15px; animation-delay: 2s; animation-duration: 35s; }
        .circles li:nth-child(10) { left: 85%; width: 150px; height: 150px; animation-delay: 0s; animation-duration: 11s; }

        @keyframes animate {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; border-radius: 0; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; border-radius: 50%; }
        }

        /* 2. CARD DESIGN (Putih Bersih) */
        .card {
            border: none;
            border-radius: 20px; /* Sudut halus */
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            background: #ffffff;
            z-index: 1; /* Di atas animasi background */
            overflow: hidden;
            transform: translateY(30px);
            opacity: 0;
            animation: slideUp 0.8s ease forwards;
        }

        @keyframes slideUp {
            to { transform: translateY(0); opacity: 1; }
        }

        /* 3. INPUT FIELDS */
        .form-control-user {
            border-radius: 10px; /* Modern rounded box */
            padding: 1.2rem 1rem;
            border: 2px solid #e1e5ea;
            background-color: #f8f9fc;
            transition: 0.3s;
            color: #2c3e50;
        }

        .form-control-user:focus {
            background-color: #fff;
            border-color: #2575fc; /* Biru Fokus */
            box-shadow: 0 0 0 4px rgba(37, 117, 252, 0.1);
        }

        /* 4. TOMBOL ELEGANT */
        .btn-primary {
            background: linear-gradient(to right, #6a11cb 0%, #2575fc 100%); /* Gradasi Biru Ungu Halus */
            background: linear-gradient(to right, #0052D4, #4364F7, #6FB1FC); /* Gradasi Full Biru */
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(0, 82, 212, 0.3);
            transition: 0.4s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 82, 212, 0.4);
            background-position: right center;
        }

        /* 5. GAMBAR SAMPING (Biru Nuansa) */
        .bg-login-image {
            /* Gambar Abstract Blue Tech / Building */
            background: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-position: center;
            background-size: cover;
        }
        
        .bg-login-image::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 78, 146, 0.4); /* Overlay biru transparan */
        }

        /* Typography & Links */
        h1.h4 {
            color: #004e92;
            font-weight: 700;
        }

        .small {
            font-size: 0.85rem;
        }
        
        a.small:hover {
            text-decoration: none;
            color: #004e92;
            font-weight: 600;
        }

        /* Custom Checkbox Biru */
        .custom-control-input:checked ~ .custom-control-label::before {
            border-color: #2575fc;
            background-color: #2575fc;
        }

    </style>

</head>

<body>

    <ul class="circles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image position-relative"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 mb-2">Welcome Back!</h1>
                                        <p class="mb-4 text-muted small">Please login to your dashboard.</p>
                                    </div>

                                    @if($errors->any())
                                        <div class="alert alert-danger small py-2 border-0 shadow-sm rounded">
                                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first() }}
                                        </div>
                                    @endif

                                    <form class="user" action="{{ route('login') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address..." value="{{ old('email') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user"
                                                id="exampleInputPassword" placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label text-muted" for="customCheck">Remember Me</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            LOGIN
                                        </button>
                                    </form>
                                    
                                    <hr class="my-4">
                                    
                                    <a href="#" class="btn btn-google btn-user btn-block btn-outline-secondary border-0 shadow-sm mb-2" style="background:#fff; color:#555;">
                                        <i class="fab fa-google fa-fw text-danger"></i> Login with Google
                                    </a>
                                    
                                    <div class="text-center mt-3">
                                        <a class="small text-secondary" href="#">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small text-secondary" href="#">Create an Account!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

</body>

</html>