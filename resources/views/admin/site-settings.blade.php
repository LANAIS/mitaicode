@extends('layouts.admin')

@section('title', 'Configuración del Sitio')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Configuración del Sitio</h2>
                    <p class="text-muted">Personalice la apariencia y contenido de la página principal</p>
                </div>
                <div class="card-body">
                    <form id="site-settings-form" action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        
                        <!-- Debug info -->
                        <div class="alert alert-info mb-4">
                            <h5><i class="fas fa-info-circle"></i> Información del Sistema</h5>
                            <ul class="mb-0">
                                <li><strong>ID de la configuración:</strong> {{ $settings->id ?? 'No disponible' }}</li>
                                <li><strong>Último valor guardado de Estudiantes actuales:</strong> {{ $settings->current_students ?? 'No disponible' }}</li>
                                <li><strong>Última actualización:</strong> {{ $settings->updated_at ?? 'No disponible' }}</li>
                            </ul>
                        </div>

                        <!-- Mensaje de mejoras al sistema -->
                        <div class="alert alert-success mb-4">
                            <h5><i class="fas fa-check-circle"></i> Mejoras Recientes</h5>
                            <p>Se han corregido los problemas con la sección de "Objetivo Educativo". Ahora puedes actualizar correctamente todos los campos, incluyendo la misión, metas de estudiantes y estadísticas actuales.</p>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <!-- Tabs de navegación -->
                        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button" role="tab" aria-controls="hero" aria-selected="true">Hero</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="features-tab" data-bs-toggle="tab" data-bs-target="#features" type="button" role="tab" aria-controls="features" aria-selected="false">Características</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="goal-tab" data-bs-toggle="tab" data-bs-target="#goal" type="button" role="tab" aria-controls="goal" aria-selected="false">Objetivo Educativo</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="testimonials-tab" data-bs-toggle="tab" data-bs-target="#testimonials" type="button" role="tab" aria-controls="testimonials" aria-selected="false">Testimonios</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Registro</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="footer-tab" data-bs-toggle="tab" data-bs-target="#footer" type="button" role="tab" aria-controls="footer" aria-selected="false">Pie de Página</button>
                            </li>
                        </ul>
                        
                        <!-- Contenido de las tabs -->
                        <div class="tab-content" id="settingsTabsContent">
                            <!-- Tab de Hero -->
                            <div class="tab-pane fade show active" id="hero" role="tabpanel" aria-labelledby="hero-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Sección Hero</h4>
                                        
                                        <div class="mb-3">
                                            <label for="hero_title" class="form-label">Título del Hero</label>
                                            <input type="text" class="form-control @error('hero_title') is-invalid @enderror" 
                                                id="hero_title" name="hero_title" value="{{ old('hero_title', $settings->hero_title) }}">
                                            @error('hero_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="hero_subtitle" class="form-label">Subtítulo del Hero</label>
                                            <textarea class="form-control @error('hero_subtitle') is-invalid @enderror" 
                                                    id="hero_subtitle" name="hero_subtitle" rows="3">{{ old('hero_subtitle', $settings->hero_subtitle) }}</textarea>
                                            @error('hero_subtitle')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Logo</h4>
                                        
                                        <div class="mb-3">
                                            <label for="logo" class="form-label">Logo del Sitio</label>
                                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                                id="logo" name="logo" accept="image/*">
                                            @error('logo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            
                                            <div class="mt-2">
                                                <p>Logo actual:</p>
                                                <img src="{{ asset($settings->logo_path) }}" alt="Logo actual" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Botón Principal</h4>
                                        
                                        <div class="mb-3">
                                            <label for="primary_button_text" class="form-label">Texto del Botón Principal</label>
                                            <input type="text" class="form-control @error('primary_button_text') is-invalid @enderror" 
                                                id="primary_button_text" name="primary_button_text" value="{{ old('primary_button_text', $settings->primary_button_text) }}">
                                            @error('primary_button_text')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="primary_button_url" class="form-label">URL del Botón Principal</label>
                                            <input type="text" class="form-control @error('primary_button_url') is-invalid @enderror" 
                                                id="primary_button_url" name="primary_button_url" value="{{ old('primary_button_url', $settings->primary_button_url) }}">
                                            @error('primary_button_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Botón Secundario</h4>
                                        
                                        <div class="mb-3">
                                            <label for="secondary_button_text" class="form-label">Texto del Botón Secundario</label>
                                            <input type="text" class="form-control @error('secondary_button_text') is-invalid @enderror" 
                                                id="secondary_button_text" name="secondary_button_text" value="{{ old('secondary_button_text', $settings->secondary_button_text) }}">
                                            @error('secondary_button_text')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="secondary_button_url" class="form-label">URL del Botón Secundario</label>
                                            <input type="text" class="form-control @error('secondary_button_url') is-invalid @enderror" 
                                                id="secondary_button_url" name="secondary_button_url" value="{{ old('secondary_button_url', $settings->secondary_button_url) }}">
                                            @error('secondary_button_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab de Características -->
                            <div class="tab-pane fade" id="features" role="tabpanel" aria-labelledby="features-tab">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h4 class="mb-3">Título de la Sección</h4>
                                        <div class="mb-3">
                                            <label for="features_title" class="form-label">Título de la sección de características</label>
                                            <input type="text" class="form-control @error('features_title') is-invalid @enderror" 
                                                id="features_title" name="features_title" value="{{ old('features_title', $settings->features_title) }}">
                                            @error('features_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Característica 1 -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Característica 1</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="feature1_title" class="form-label">Título</label>
                                                    <input type="text" class="form-control @error('feature1_title') is-invalid @enderror" 
                                                        id="feature1_title" name="feature1_title" value="{{ old('feature1_title', $settings->feature1_title) }}">
                                                    @error('feature1_title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="feature1_description" class="form-label">Descripción</label>
                                                    <textarea class="form-control @error('feature1_description') is-invalid @enderror" 
                                                            id="feature1_description" name="feature1_description" rows="3">{{ old('feature1_description', $settings->feature1_description) }}</textarea>
                                                    @error('feature1_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="feature1_icon" class="form-label">Ícono</label>
                                                    <select class="form-select @error('feature1_icon') is-invalid @enderror" 
                                                            id="feature1_icon" name="feature1_icon">
                                                        @foreach($availableIcons as $value => $label)
                                                            <option value="{{ $value }}" {{ old('feature1_icon', $settings->feature1_icon) == $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('feature1_icon')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="icon-preview text-center mt-4">
                                                    <i class="{{ $settings->feature1_icon }} fa-3x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Característica 2 -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Característica 2</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="feature2_title" class="form-label">Título</label>
                                                    <input type="text" class="form-control @error('feature2_title') is-invalid @enderror" 
                                                        id="feature2_title" name="feature2_title" value="{{ old('feature2_title', $settings->feature2_title) }}">
                                                    @error('feature2_title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="feature2_description" class="form-label">Descripción</label>
                                                    <textarea class="form-control @error('feature2_description') is-invalid @enderror" 
                                                            id="feature2_description" name="feature2_description" rows="3">{{ old('feature2_description', $settings->feature2_description) }}</textarea>
                                                    @error('feature2_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="feature2_icon" class="form-label">Ícono</label>
                                                    <select class="form-select @error('feature2_icon') is-invalid @enderror" 
                                                            id="feature2_icon" name="feature2_icon">
                                                        @foreach($availableIcons as $value => $label)
                                                            <option value="{{ $value }}" {{ old('feature2_icon', $settings->feature2_icon) == $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('feature2_icon')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="icon-preview text-center mt-4">
                                                    <i class="{{ $settings->feature2_icon }} fa-3x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Característica 3 -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Característica 3</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="feature3_title" class="form-label">Título</label>
                                                    <input type="text" class="form-control @error('feature3_title') is-invalid @enderror" 
                                                        id="feature3_title" name="feature3_title" value="{{ old('feature3_title', $settings->feature3_title) }}">
                                                    @error('feature3_title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="feature3_description" class="form-label">Descripción</label>
                                                    <textarea class="form-control @error('feature3_description') is-invalid @enderror" 
                                                            id="feature3_description" name="feature3_description" rows="3">{{ old('feature3_description', $settings->feature3_description) }}</textarea>
                                                    @error('feature3_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="feature3_icon" class="form-label">Ícono</label>
                                                    <select class="form-select @error('feature3_icon') is-invalid @enderror" 
                                                            id="feature3_icon" name="feature3_icon">
                                                        @foreach($availableIcons as $value => $label)
                                                            <option value="{{ $value }}" {{ old('feature3_icon', $settings->feature3_icon) == $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('feature3_icon')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="icon-preview text-center mt-4">
                                                    <i class="{{ $settings->feature3_icon }} fa-3x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Característica 4 -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Característica 4</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="feature4_title" class="form-label">Título</label>
                                                    <input type="text" class="form-control @error('feature4_title') is-invalid @enderror" 
                                                        id="feature4_title" name="feature4_title" value="{{ old('feature4_title', $settings->feature4_title) }}">
                                                    @error('feature4_title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="feature4_description" class="form-label">Descripción</label>
                                                    <textarea class="form-control @error('feature4_description') is-invalid @enderror" 
                                                            id="feature4_description" name="feature4_description" rows="3">{{ old('feature4_description', $settings->feature4_description) }}</textarea>
                                                    @error('feature4_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="feature4_icon" class="form-label">Ícono</label>
                                                    <select class="form-select @error('feature4_icon') is-invalid @enderror" 
                                                            id="feature4_icon" name="feature4_icon">
                                                        @foreach($availableIcons as $value => $label)
                                                            <option value="{{ $value }}" {{ old('feature4_icon', $settings->feature4_icon) == $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('feature4_icon')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="icon-preview text-center mt-4">
                                                    <i class="{{ $settings->feature4_icon }} fa-3x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Característica 5 -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Característica 5</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="feature5_title" class="form-label">Título</label>
                                                    <input type="text" class="form-control @error('feature5_title') is-invalid @enderror" 
                                                        id="feature5_title" name="feature5_title" value="{{ old('feature5_title', $settings->feature5_title) }}">
                                                    @error('feature5_title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="feature5_description" class="form-label">Descripción</label>
                                                    <textarea class="form-control @error('feature5_description') is-invalid @enderror" 
                                                            id="feature5_description" name="feature5_description" rows="3">{{ old('feature5_description', $settings->feature5_description) }}</textarea>
                                                    @error('feature5_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="feature5_icon" class="form-label">Ícono</label>
                                                    <select class="form-select @error('feature5_icon') is-invalid @enderror" 
                                                            id="feature5_icon" name="feature5_icon">
                                                        @foreach($availableIcons as $value => $label)
                                                            <option value="{{ $value }}" {{ old('feature5_icon', $settings->feature5_icon) == $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('feature5_icon')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="icon-preview text-center mt-4">
                                                    <i class="{{ $settings->feature5_icon }} fa-3x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Característica 6 -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Característica 6</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="feature6_title" class="form-label">Título</label>
                                                    <input type="text" class="form-control @error('feature6_title') is-invalid @enderror" 
                                                        id="feature6_title" name="feature6_title" value="{{ old('feature6_title', $settings->feature6_title) }}">
                                                    @error('feature6_title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="feature6_description" class="form-label">Descripción</label>
                                                    <textarea class="form-control @error('feature6_description') is-invalid @enderror" 
                                                            id="feature6_description" name="feature6_description" rows="3">{{ old('feature6_description', $settings->feature6_description) }}</textarea>
                                                    @error('feature6_description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="feature6_icon" class="form-label">Ícono</label>
                                                    <select class="form-select @error('feature6_icon') is-invalid @enderror" 
                                                            id="feature6_icon" name="feature6_icon">
                                                        @foreach($availableIcons as $value => $label)
                                                            <option value="{{ $value }}" {{ old('feature6_icon', $settings->feature6_icon) == $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('feature6_icon')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="icon-preview text-center mt-4">
                                                    <i class="{{ $settings->feature6_icon }} fa-3x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab de Objetivo Educativo -->
                            <div class="tab-pane fade" id="goal" role="tabpanel" aria-labelledby="goal-tab">
                                @include('admin.sections.goal')
                            </div>
                            
                            <!-- Tab de Testimonios -->
                            <div class="tab-pane fade" id="testimonials" role="tabpanel" aria-labelledby="testimonials-tab">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h4 class="mb-3">Título de la Sección</h4>
                                        
                                        <div class="mb-3">
                                            <label for="testimonials_title" class="form-label">Título de la sección de testimonios</label>
                                            <input type="text" class="form-control @error('testimonials_title') is-invalid @enderror" 
                                                id="testimonials_title" name="testimonials_title" value="{{ old('testimonials_title', $settings->testimonials_title) }}">
                                            @error('testimonials_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Testimonio 1 -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Testimonio 1</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="testimonial1_content" class="form-label">Contenido del testimonio</label>
                                            <textarea class="form-control @error('testimonial1_content') is-invalid @enderror" 
                                                    id="testimonial1_content" name="testimonial1_content" rows="3">{{ old('testimonial1_content', $settings->testimonial1_content) }}</textarea>
                                            @error('testimonial1_content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="testimonial1_author" class="form-label">Nombre del autor</label>
                                                    <input type="text" class="form-control @error('testimonial1_author') is-invalid @enderror" 
                                                        id="testimonial1_author" name="testimonial1_author" value="{{ old('testimonial1_author', $settings->testimonial1_author) }}">
                                                    @error('testimonial1_author')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="testimonial1_role" class="form-label">Rol o posición</label>
                                                    <input type="text" class="form-control @error('testimonial1_role') is-invalid @enderror" 
                                                        id="testimonial1_role" name="testimonial1_role" value="{{ old('testimonial1_role', $settings->testimonial1_role) }}">
                                                    @error('testimonial1_role')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Testimonio 2 -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Testimonio 2</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="testimonial2_content" class="form-label">Contenido del testimonio</label>
                                            <textarea class="form-control @error('testimonial2_content') is-invalid @enderror" 
                                                    id="testimonial2_content" name="testimonial2_content" rows="3">{{ old('testimonial2_content', $settings->testimonial2_content) }}</textarea>
                                            @error('testimonial2_content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="testimonial2_author" class="form-label">Nombre del autor</label>
                                                    <input type="text" class="form-control @error('testimonial2_author') is-invalid @enderror" 
                                                        id="testimonial2_author" name="testimonial2_author" value="{{ old('testimonial2_author', $settings->testimonial2_author) }}">
                                                    @error('testimonial2_author')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="testimonial2_role" class="form-label">Rol o posición</label>
                                                    <input type="text" class="form-control @error('testimonial2_role') is-invalid @enderror" 
                                                        id="testimonial2_role" name="testimonial2_role" value="{{ old('testimonial2_role', $settings->testimonial2_role) }}">
                                                    @error('testimonial2_role')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Testimonio 3 -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Testimonio 3</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="testimonial3_content" class="form-label">Contenido del testimonio</label>
                                            <textarea class="form-control @error('testimonial3_content') is-invalid @enderror" 
                                                    id="testimonial3_content" name="testimonial3_content" rows="3">{{ old('testimonial3_content', $settings->testimonial3_content) }}</textarea>
                                            @error('testimonial3_content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="testimonial3_author" class="form-label">Nombre del autor</label>
                                                    <input type="text" class="form-control @error('testimonial3_author') is-invalid @enderror" 
                                                        id="testimonial3_author" name="testimonial3_author" value="{{ old('testimonial3_author', $settings->testimonial3_author) }}">
                                                    @error('testimonial3_author')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="testimonial3_role" class="form-label">Rol o posición</label>
                                                    <input type="text" class="form-control @error('testimonial3_role') is-invalid @enderror" 
                                                        id="testimonial3_role" name="testimonial3_role" value="{{ old('testimonial3_role', $settings->testimonial3_role) }}">
                                                    @error('testimonial3_role')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab de Registro -->
                            <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h4 class="mb-3">Sección de Registro</h4>
                                        
                                        <div class="mb-3">
                                            <label for="register_title" class="form-label">Título de la sección</label>
                                            <input type="text" class="form-control @error('register_title') is-invalid @enderror" 
                                                id="register_title" name="register_title" value="{{ old('register_title', $settings->register_title) }}">
                                            @error('register_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="register_subtitle" class="form-label">Subtítulo / Descripción</label>
                                            <textarea class="form-control @error('register_subtitle') is-invalid @enderror" 
                                                    id="register_subtitle" name="register_subtitle" rows="3">{{ old('register_subtitle', $settings->register_subtitle) }}</textarea>
                                            @error('register_subtitle')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="student_label" class="form-label">Etiqueta para estudiantes</label>
                                            <input type="text" class="form-control @error('student_label') is-invalid @enderror" 
                                                id="student_label" name="student_label" value="{{ old('student_label', $settings->student_label) }}">
                                            @error('student_label')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="teacher_label" class="form-label">Etiqueta para docentes</label>
                                            <input type="text" class="form-control @error('teacher_label') is-invalid @enderror" 
                                                id="teacher_label" name="teacher_label" value="{{ old('teacher_label', $settings->teacher_label) }}">
                                            @error('teacher_label')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="register_button_text" class="form-label">Texto del botón de registro</label>
                                            <input type="text" class="form-control @error('register_button_text') is-invalid @enderror" 
                                                id="register_button_text" name="register_button_text" value="{{ old('register_button_text', $settings->register_button_text) }}">
                                            @error('register_button_text')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab de Pie de Página -->
                            <div class="tab-pane fade" id="footer" role="tabpanel" aria-labelledby="footer-tab">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h4 class="mb-3">Información del Pie de Página</h4>
                                        
                                        <div class="mb-3">
                                            <label for="footer_description" class="form-label">Descripción de la empresa</label>
                                            <textarea class="form-control @error('footer_description') is-invalid @enderror" 
                                                    id="footer_description" name="footer_description" rows="3">{{ old('footer_description', $settings->footer_description) }}</textarea>
                                            @error('footer_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Información de Contacto</h5>
                                        
                                        <div class="mb-3">
                                            <label for="contact_email" class="form-label">Email de contacto</label>
                                            <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                                id="contact_email" name="contact_email" value="{{ old('contact_email', $settings->contact_email) }}">
                                            @error('contact_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="contact_phone" class="form-label">Teléfono de contacto</label>
                                            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" 
                                                id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}">
                                            @error('contact_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Copyright</h5>
                                        
                                        <div class="mb-3">
                                            <label for="footer_copyright" class="form-label">Texto de copyright</label>
                                            <input type="text" class="form-control @error('footer_copyright') is-invalid @enderror" 
                                                id="footer_copyright" name="footer_copyright" value="{{ old('footer_copyright', $settings->footer_copyright) }}">
                                            @error('footer_copyright')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">El año actual se añadirá automáticamente antes de este texto.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botón de envío prominente -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save mr-1"></i> Guardar Cambios y Limpiar Caché
                                </button>
                                <div class="mt-3 text-muted small">
                                    <p>Ten en cuenta:</p>
                                    <ul>
                                        <li>Este formulario guarda directamente en la base de datos usando SQL.</li>
                                        <li>Se limpiará automáticamente toda la caché del sistema.</li>
                                        <li>Los cambios tardarán unos segundos en ser visibles en la página principal.</li>
                                        <li>Si el problema persiste, usa el <a href="/debug-settings" target="_blank">panel de depuración</a>.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Debug buttons -->
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <span class="badge bg-info">Actualmente en versión de depuración</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Vista previa del logo al seleccionarlo
    document.getElementById('logo').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.img-thumbnail').src = e.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
    
    // Actualizar vista previa de los íconos al cambiar la selección
    document.getElementById('feature1_icon').addEventListener('change', function() {
        updateIconPreview(this, 1);
    });
    
    document.getElementById('feature2_icon').addEventListener('change', function() {
        updateIconPreview(this, 2);
    });
    
    document.getElementById('feature3_icon').addEventListener('change', function() {
        updateIconPreview(this, 3);
    });
    
    document.getElementById('feature4_icon').addEventListener('change', function() {
        updateIconPreview(this, 4);
    });
    
    document.getElementById('feature5_icon').addEventListener('change', function() {
        updateIconPreview(this, 5);
    });
    
    document.getElementById('feature6_icon').addEventListener('change', function() {
        updateIconPreview(this, 6);
    });
    
    function updateIconPreview(selectElement, featureNum) {
        const iconClass = selectElement.value;
        const previewElement = selectElement.closest('.row').querySelector('.icon-preview i');
        
        // Eliminar todas las clases excepto 'fa-3x'
        previewElement.className = '';
        previewElement.classList.add('fa-3x');
        
        // Agregar la nueva clase de ícono
        const iconClasses = iconClass.split(' ');
        iconClasses.forEach(cls => {
            previewElement.classList.add(cls);
        });
    }
</script>
@endsection 