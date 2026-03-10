<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - @yield('title', 'Dashboard')</title>

    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.css') }}" rel="stylesheet">
    
    <style>
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }
        
        #wrapper {
            display: flex;
        }
        
        .sidebar {
            position: fixed !important;
            left: 0;
            top: 0;
            width: 6.5rem;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            flex-shrink: 0;
            will-change: transform;
            backface-visibility: hidden;
            perspective: 1000px;
            contain: layout style paint;
        }
        
        #wrapper #content-wrapper {
            margin-left: 6.5rem;
            width: calc(100% - 6.5rem);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: margin-left;
            backface-visibility: hidden;
            perspective: 1000px;
            contain: layout style paint;
            flex: 1;
        }
        
        #wrapper #content-wrapper #content {
            will-change: contents;
            contain: content;
        }
        
        #wrapper #content-wrapper .container-fluid {
            will-change: contents;
            backface-visibility: hidden;
            perspective: 1000px;
        }
        
        .container-fluid {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transform: translateZ(0);
            contain: layout style paint;
        }
        
        .sidebar .nav-item .collapse {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            will-change: opacity, visibility;
            backface-visibility: hidden;
        }
        
        .sidebar .nav-item .nav-link {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: color, background-color;
            backface-visibility: hidden;
        }
        
        .sidebar .nav-item .collapse.show {
            opacity: 1;
            visibility: visible;
        }
        
        .sidebar .nav-item .collapse:not(.show) {
            opacity: 0;
            visibility: hidden;
        }
        
        /* Smooth content rendering */
        img, picture, video, canvas {
            display: block;
            max-width: 100%;
            height: auto;
            will-change: auto;
        }
        
        a, button {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: color, background-color, transform;
            backface-visibility: hidden;
        }
        
        input, textarea, select {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            backface-visibility: hidden;
        }
        
        input:focus, textarea:focus, select:focus {
            will-change: box-shadow, border-color;
        }
        
        @media (min-width: 768px) {
            .sidebar {
                width: 14rem !important;
            }
            
            #wrapper #content-wrapper {
                margin-left: 14rem;
                width: calc(100% - 14rem);
            }
            
            .sidebar .nav-item .collapse {
                position: relative !important;
                left: 0 !important;
                top: 0 !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
        }
        
        @media (max-width: 767.98px) {
            .sidebar.toggled {
                width: 0 !important;
                overflow: hidden;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .sidebar:not(.toggled) {
                transform: translateX(0);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            #wrapper #content-wrapper {
                margin-left: 0;
                width: 100%;
            }
        }
        
        /* Smooth scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }
        
        /* Content scrollbar smooth */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.02);
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.2);
        }
    </style>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body id="page-top">

    <div id="wrapper">

        @include('layouts.sidebar')

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                @include('layouts.header')

                <div class="container-fluid">
                    @yield('content') 
                </div>
                </div>
            @include('layouts.footer')

        </div>
        </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>

    <script src="{{ asset('assets/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('assets/js/demo/chart-pie-demo.js') }}"></script>

    <script>
        // Ensure modals are direct children of <body> to avoid stacking/context z-index issues
        document.addEventListener('DOMContentLoaded', function () {
            try {
                document.querySelectorAll('.modal').forEach(function (m) {
                    if (m.parentNode && m.parentNode !== document.body) document.body.appendChild(m);
                });
            } catch (e) {
                // ignore
            }
        });

        // For dynamic cases, ensure modals are appended to body on show (requires jQuery + Bootstrap)
        if (window.jQuery) {
            window.jQuery(document).on('show.bs.modal', '.modal', function () {
                var m = window.jQuery(this);
                if (m.parent()[0] !== document.body) m.appendTo('body');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>