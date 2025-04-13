<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="Index,Allow">
    <meta name="description" content="Plataforma de programación visual por bloques para niños"/>
    <meta name="keywords" content="mitaicode,robot,arduino,programacion,bloques,niños,educación" />
    <meta name="author" content="Mitaí Code"/>
    <!-- Evitar caché del navegador -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Mitaí Code - Programación para Niños</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/mitai-logo-128x128.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/landing.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="{{ asset('js/auth.js') }}"></script>
            <style>
    /* Estilos básicos de respaldo en caso de que no se cargue el CSS principal */
    body {
        font-family: 'Nunito', sans-serif;
        color: #333;
        line-height: 1.6;
    }
    .auth-page {
        background-color: #f8f9fa;
    }
    .navbar {
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .hero-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #f0f8ff 0%, #e0f0ff 100%);
    }
            </style>
    </head>
<body class="auth-page">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('assets/images/mitai-logo-128x128.svg') }}" alt="Mitaí Code" height="40">
                <span>Mitaí Code</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#funciones">Características</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#objetivo">Objetivo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonios">Testimonios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#registro">Registro</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary" href="{{ route('dashboard') }}">Mi Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-outline-primary">Cerrar Sesión</button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-4">{{ $settings->hero_title }}</h1>
                    <p class="lead mb-4">{{ $settings->hero_subtitle }}</p>
                    <div class="d-grid gap-2 d-md-flex">
                        <a href="{{ url($settings->primary_button_url) }}" class="btn btn-primary btn-lg px-4 me-md-2">{{ $settings->primary_button_text }}</a>
                        <a href="{{ url($settings->secondary_button_url) }}" class="btn btn-secondary btn-lg px-4">{{ $settings->secondary_button_text }}</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="hero-image">
                        <img src="{{ asset($settings->logo_path) }}" alt="Mitaí Code" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Características/Funciones -->
    <section id="funciones" class="features-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">{{ $settings->features_title }}</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-wrapper">
                            <i class="{{ $settings->feature1_icon }}"></i>
                        </div>
                        <h3>{{ $settings->feature1_title }}</h3>
                        <p>{{ $settings->feature1_description }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-wrapper">
                            <i class="{{ $settings->feature2_icon }}"></i>
                        </div>
                        <h3>{{ $settings->feature2_title }}</h3>
                        <p>{{ $settings->feature2_description }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-wrapper">
                            <i class="{{ $settings->feature3_icon }}"></i>
                        </div>
                        <h3>{{ $settings->feature3_title }}</h3>
                        <p>{{ $settings->feature3_description }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-wrapper">
                            <i class="{{ $settings->feature4_icon }}"></i>
                        </div>
                        <h3>{{ $settings->feature4_title }}</h3>
                        <p>{{ $settings->feature4_description }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-wrapper">
                            <i class="{{ $settings->feature5_icon }}"></i>
                        </div>
                        <h3>{{ $settings->feature5_title }}</h3>
                        <p>{{ $settings->feature5_description }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-wrapper">
                            <i class="{{ $settings->feature6_icon }}"></i>
                        </div>
                        <h3>{{ $settings->feature6_title }}</h3>
                        <p>{{ $settings->feature6_description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Objetivo Educativo -->
    <section id="objetivo" class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-3">{{ $settings->goal_title }}</h2>
                    <p class="text-muted">{{ $settings->goal_subtitle }}</p>
                </div>
            </div>
            
            <div class="row mb-5">
                <div class="col-12">
                    <div class="progress" style="height: 30px;">
                        @php
                            $progressPercentage = $settings->goal_students_target > 0 
                                ? ($settings->current_students / $settings->goal_students_target) * 100 
                                : 0;
                        @endphp
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" 
                            style="width: {{ $progressPercentage }}%;" 
                            aria-valuenow="{{ $settings->current_students }}" 
                            aria-valuemin="0" 
                            aria-valuemax="{{ $settings->goal_students_target }}">
                            {{ round($progressPercentage) }}%
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span>0</span>
                        <span>{{ number_format($settings->current_students) }}</span>
                        <span>{{ number_format($settings->goal_students_target) }} estudiantes</span>
                    </div>
                </div>
            </div>
            
            <div class="row text-center">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="p-4 rounded shadow-sm bg-white">
                        <h3 class="display-5 text-primary fw-bold">{{ number_format($settings->current_students) }}</h3>
                        <p class="mb-0">Estudiantes Activos</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="p-4 rounded shadow-sm bg-white">
                        <h3 class="display-5 text-primary fw-bold">{{ number_format($settings->current_projects) }}</h3>
                        <p class="mb-0">Proyectos Creados</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 rounded shadow-sm bg-white">
                        <h3 class="display-5 text-primary fw-bold">{{ number_format($settings->current_badges) }}</h3>
                        <p class="mb-0">Insignias Ganadas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonios -->
    <section id="testimonios" class="py-5">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-3">{{ $settings->testimonials_title }}</h2>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-quote-left text-primary fa-2x opacity-25"></i>
                            </div>
                            <p class="mb-4">{{ $settings->testimonial1_content }}</p>
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-primary text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $settings->testimonial1_author }}</h6>
                                    <small class="text-muted">{{ $settings->testimonial1_role }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-quote-left text-primary fa-2x opacity-25"></i>
                            </div>
                            <p class="mb-4">{{ $settings->testimonial2_content }}</p>
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-primary text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $settings->testimonial2_author }}</h6>
                                    <small class="text-muted">{{ $settings->testimonial2_role }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="fas fa-quote-left text-primary fa-2x opacity-25"></i>
                            </div>
                            <p class="mb-4">{{ $settings->testimonial3_content }}</p>
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-primary text-white rounded-circle p-3 me-3">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $settings->testimonial3_author }}</h6>
                                    <small class="text-muted">{{ $settings->testimonial3_role }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Registro -->
    <section id="registro" class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-3">{{ $settings->register_title }}</h2>
                    <p>{{ $settings->register_subtitle }}</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-4">
                            <form action="{{ route('register') }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="role" id="role-student" value="student" checked>
                                            <label class="form-check-label" for="role-student">
                                                {{ $settings->student_label }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="role" id="role-teacher" value="teacher">
                                            <label class="form-check-label" for="role-teacher">
                                                {{ $settings->teacher_label }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" placeholder="Nombre de usuario" required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" placeholder="Nombre" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" placeholder="Apellido" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Correo electrónico" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Campos adicionales para estudiantes -->
                                <div id="student-fields" class="mb-3" style="display: block;">
                                    <div class="card card-body bg-light mb-3">
                                        <div class="mb-3">
                                            <input type="number" class="form-control" name="age" value="{{ old('age') }}" placeholder="Edad">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <input type="email" class="form-control" name="parent_email" value="{{ old('parent_email') }}" placeholder="Correo del Padre/Tutor (Opcional)">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Campos adicionales para profesores -->
                                <div id="teacher-fields" class="mb-3" style="display: none;">
                                    <div class="card card-body bg-light mb-3">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="institution" value="{{ old('institution') }}" placeholder="Institución">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Contraseña" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirmar Contraseña" required>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">{{ $settings->register_button_text }}</button>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <p class="mb-0">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-primary">Inicia sesión</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="text-white mb-4">Mitaí Code</h5>
                    <p>{{ $settings->footer_description }}</p>
                </div>
                
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="text-white mb-4">Enlaces rápidos</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50">Editor de bloques</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Modo Aventura</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}" class="text-white-50">Iniciar sesión</a></li>
                        <li class="mb-2"><a href="{{ route('register') }}" class="text-white-50">Registrarse</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4">
                    <h5 class="text-white mb-4">Contacto</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <a href="mailto:{{ $settings->contact_email }}" class="text-white-50">{{ $settings->contact_email }}</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <span class="text-white-50">{{ $settings->contact_phone }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 bg-secondary">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0">© {{ date('Y') }} {{ $settings->footer_copyright }}</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-white-50 me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white-50 me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white-50 me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white-50"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar/ocultar campos específicos para docentes
            const teacherRadio = document.getElementById('role-teacher');
            const studentRadio = document.getElementById('role-student');
            const teacherFields = document.getElementById('teacher-fields');
            const studentFields = document.getElementById('student-fields');
            
            if (teacherRadio && studentRadio && teacherFields && studentFields) {
                teacherRadio.addEventListener('change', function() {
                    teacherFields.style.display = 'block';
                    studentFields.style.display = 'none';
                });
                
                studentRadio.addEventListener('change', function() {
                    teacherFields.style.display = 'none';
                    studentFields.style.display = 'block';
                });
            }
        });
    </script>
    </body>
</html>
