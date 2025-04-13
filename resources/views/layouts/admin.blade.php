<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MitaiCode') }} - Panel de Administración - @yield('title', 'Dashboard')</title>

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
        
        /* Sidebar Styles */
        .sidebar {
            background-color: #4e73df;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            color: #fff;
            z-index: 1000;
            padding-top: 20px;
            transition: all 0.3s;
        }
        
        .sidebar .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 20px;
            margin-bottom: 30px;
        }
        
        .sidebar .logo img {
            height: 40px;
            margin-right: 10px;
        }
        
        .sidebar .nav-item {
            width: 100%;
        }
        
        .sidebar .nav-link {
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: #fff;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            flex: 1;
            padding: 20px;
            transition: all 0.3s;
        }
        
        /* Cards */
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #eaecf4;
            padding: 15px 20px;
        }
        
        /* Footer */
        footer {
            margin-top: auto;
            margin-left: 250px;
            width: calc(100% - 250px);
            background-color: #f8f9fc;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #eaecf4;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            
            .sidebar.active {
                margin-left: 0;
            }
            
            .main-content, footer {
                margin-left: 0;
                width: 100%;
            }
            
            .main-content.shifted, footer.shifted {
                margin-left: 250px;
                width: calc(100% - 250px);
            }
            
            .menu-toggle {
                display: block !important;
            }
        }
        
        .menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="{{ asset('assets/images/mitai-logo-128x128.svg') }}" alt="MitaiCode">
            <h5 class="m-0">MitaiCode Admin</h5>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('admin.site-settings.edit') }}" class="nav-link {{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Configuración del Sitio
                </a>
            </li>
            
            <li class="nav-item {{ Request::is('admin/analytics*') ? 'active' : '' }}">
                <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAnalytics"
                    aria-expanded="{{ Request::is('admin/analytics*') ? 'true' : 'false' }}" aria-controls="collapseAnalytics">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Analíticas</span>
                </a>
                <div id="collapseAnalytics" class="collapse {{ Request::is('admin/analytics*') ? 'show' : '' }}">
                    <div class="bg-white py-2 ps-4 rounded">
                        <h6 class="collapse-header text-muted small text-uppercase mt-2 mb-2">Analíticas:</h6>
                        <a class="nav-link py-1 {{ Request::is('admin/analytics') && !request()->segment(3) ? 'text-primary' : 'text-dark' }}" href="{{ route('admin.analytics.index') }}">
                            <i class="fas fa-fw fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                        <a class="nav-link py-1 {{ Request::is('admin/analytics/users') ? 'text-primary' : 'text-dark' }}" href="{{ route('admin.analytics.users') }}">
                            <i class="fas fa-fw fa-users me-1"></i> Usuarios
                        </a>
                        <a class="nav-link py-1 {{ Request::is('admin/analytics/content') ? 'text-primary' : 'text-dark' }}" href="{{ route('admin.analytics.content') }}">
                            <i class="fas fa-fw fa-book me-1"></i> Contenido
                        </a>
                    </div>
                </div>
            </li>
            
            <li class="nav-item {{ Request::is('admin/notifications*') ? 'active' : '' }}">
                <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#collapseNotifications"
                    aria-expanded="{{ Request::is('admin/notifications*') ? 'true' : 'false' }}" aria-controls="collapseNotifications">
                    <i class="fas fa-fw fa-bell"></i>
                    <span>Notificaciones</span>
                </a>
                <div id="collapseNotifications" class="collapse {{ Request::is('admin/notifications*') ? 'show' : '' }}">
                    <div class="bg-white py-2 ps-4 rounded">
                        <h6 class="collapse-header text-muted small text-uppercase mt-2 mb-2">Gestión de Emails:</h6>
                        <a class="nav-link py-1 {{ Request::is('admin/notifications') && !request()->has('type') ? 'text-primary' : 'text-dark' }}" href="{{ route('admin.notifications.index') }}">
                            <i class="fas fa-fw fa-envelope me-1"></i> Todas las notificaciones
                        </a>
                        <a class="nav-link py-1 {{ Request::is('admin/notifications/create') ? 'text-primary' : 'text-dark' }}" href="{{ route('admin.notifications.create') }}">
                            <i class="fas fa-fw fa-plus me-1"></i> Nueva notificación
                        </a>
                        <div class="dropdown-divider my-2"></div>
                        <h6 class="collapse-header text-muted small text-uppercase mt-2 mb-2">Tipos de Notificación:</h6>
                        <a class="nav-link py-1 {{ Request::is('admin/notifications') && request('type') == 'welcome' ? 'text-primary' : 'text-dark' }}" href="{{ route('admin.notifications.index') }}?type=welcome">
                            <i class="fas fa-fw fa-hand-paper me-1"></i> Bienvenida
                        </a>
                        <a class="nav-link py-1 {{ Request::is('admin/notifications') && request('type') == 'reminder' ? 'text-primary' : 'text-dark' }}" href="{{ route('admin.notifications.index') }}?type=reminder">
                            <i class="fas fa-fw fa-clock me-1"></i> Recordatorios
                        </a>
                        <a class="nav-link py-1 {{ Request::is('admin/notifications') && request('type') == 'inactive' ? 'text-primary' : 'text-dark' }}" href="{{ route('admin.notifications.index') }}?type=inactive">
                            <i class="fas fa-fw fa-user-slash me-1"></i> Reactivación
                        </a>
                    </div>
                </div>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('hackathons.index') }}" class="nav-link {{ request()->routeIs('hackathons.*') ? 'active' : '' }}">
                    <i class="fas fa-laptop-code"></i> Hackathones
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('challenges.index') }}" class="nav-link {{ request()->routeIs('challenges.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i> Desafíos
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('admin.store-items.index') }}" class="nav-link {{ request()->routeIs('admin.store-items.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i> Tienda
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ url('/') }}" class="nav-link">
                    <i class="fas fa-home"></i> Volver al Sitio
                </a>
            </li>
            
            <li class="nav-item mt-3">
                <form method="POST" action="{{ route('logout') }}" class="nav-link">
                    @csrf
                    <button type="submit" class="btn btn-link text-white p-0">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Menu Toggle Button (visibles en móviles) -->
    <button class="btn btn-primary rounded-circle shadow menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">@yield('title', 'Dashboard')</h1>
            </div>
            
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <span>&copy; {{ date('Y') }} MitaiCode. Todos los derechos reservados.</span>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts -->
    <script>
        // Funcionalidad del menú Toggle para móviles
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('shifted');
            document.querySelector('footer').classList.toggle('shifted');
        });

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
    
    <!-- Scripts adicionales específicos de la vista -->
    @yield('scripts')
</body>
</html> 