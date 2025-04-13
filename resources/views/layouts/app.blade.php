<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MitaiCode') }} - @yield('title', 'Plataforma Educativa')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/mitai-logo-128x128.svg') }}" type="image/svg+xml">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-brand img {
            margin-right: 0.5rem;
        }
        main {
            flex: 1 0 auto;
        }
        footer {
            margin-top: auto;
        }
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="min-vh-100 d-flex flex-column">
        <!-- Navegación -->
        @include('layouts.navigation')

        <!-- Encabezado de la página -->
        <header class="bg-light py-3 border-bottom">
            <div class="container">
                <h2 class="h4 mb-0 fw-bold">
                    @yield('header', 'Dashboard')
                </h2>
            </div>
        </header>

        <!-- Contenido de la página -->
        <main class="py-4 flex-grow-1">
            <div class="container">
                <div class="card">
                    <div class="card-body">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>

        <!-- Pie de página -->
        <footer class="bg-light py-3 border-top">
            <div class="container">
                <div class="text-center text-muted small">
                    &copy; {{ date('Y') }} MitaiCode - Plataforma Educativa de Programación
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <!-- Confetti effects -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <!-- Particles.js for animations -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    
    <!-- Audio for celebrations -->
    <audio id="success-sound" src="{{ asset('sounds/success.mp3') }}" preload="auto"></audio>
    <audio id="level-up-sound" src="{{ asset('sounds/level-up.mp3') }}" preload="auto"></audio>
    
    <!-- Custom styles -->
    @yield('styles')
    
    <!-- Scripts adicionales específicos de la vista -->
    @yield('scripts')

    <script>
        // Cerrar alertas automáticamente después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert.alert-success, .alert.alert-info');
                alerts.forEach(function(alert) {
                    const closeButton = alert.querySelector('.btn-close');
                    if (closeButton) {
                        closeButton.click();
                    } else {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);
        });
    </script>
</body>
</html> 