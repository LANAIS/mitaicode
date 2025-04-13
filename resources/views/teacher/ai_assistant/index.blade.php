@extends('layouts.app')

@section('title', 'Asistente de IA para Profesores')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h2 mb-3">Asistente de IA para Profesores</h1>
            <p class="text-muted">Herramientas de IA especializadas para mejorar tus desafíos educativos y ejercicios.</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 g-4">
        <!-- Generador de Ideas -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lightbulb text-primary me-2"></i> Generador de Ideas
                        </h5>
                        <span class="badge bg-primary">Recomendado</span>
                    </div>
                    <p class="card-text">Obtén ideas originales para nuevos desafíos educativos basados en tus objetivos pedagógicos y el nivel de tus estudiantes.</p>
                    <div class="mt-auto pt-3">
                        <a href="{{ route('teacher.ai_assistant.idea_generator') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-right me-2"></i> Ir al Generador de Ideas
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Generador de Variantes -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sync-alt text-success me-2"></i> Generador de Variantes
                        </h5>
                        <span class="badge bg-success">Útil</span>
                    </div>
                    <p class="card-text">Crea variantes de tus ejercicios existentes para ofrecer diferentes niveles de dificultad, contextos o enfoques pedagógicos.</p>
                    <div class="mt-auto pt-3">
                        <a href="{{ route('teacher.ai_assistant.variant_generator') }}" class="btn btn-outline-success">
                            <i class="fas fa-arrow-right me-2"></i> Ir al Generador de Variantes
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Generador de Estructura -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-alt text-info me-2"></i> Generador de Estructura
                        </h5>
                        <span class="badge bg-info">Nuevo</span>
                    </div>
                    <p class="card-text">Crea estructuras completas para tus desafíos educativos con todas las secciones necesarias, desde objetivos hasta evaluación.</p>
                    <div class="mt-auto pt-3">
                        <a href="{{ route('teacher.ai_assistant.structure_generator') }}" class="btn btn-outline-info">
                            <i class="fas fa-arrow-right me-2"></i> Ir al Generador de Estructura
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Verificador de Calidad -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-check-double text-warning me-2"></i> Verificador de Calidad
                        </h5>
                    </div>
                    <p class="card-text">Evalúa la calidad pedagógica de tus ejercicios, recibe feedback específico y sugerencias para mejorarlos antes de presentarlos a tus estudiantes.</p>
                    <div class="mt-auto pt-3">
                        <a href="{{ route('teacher.ai_assistant.quality_checker') }}" class="btn btn-outline-warning">
                            <i class="fas fa-arrow-right me-2"></i> Ir al Verificador de Calidad
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sección de consejos y mejores prácticas -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="h5 mb-4">Consejos para aprovechar el Asistente de IA</h4>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-graduation-cap text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="h6">Define claramente tus objetivos pedagógicos</h5>
                                    <p class="small text-muted">Cuanto más específicos sean tus objetivos, mejores resultados obtendrás del asistente.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-sliders-h text-success"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="h6">Personaliza y ajusta los resultados</h5>
                                    <p class="small text-muted">Los resultados generados son puntos de partida. Personalízalos según las necesidades específicas de tu clase.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-users text-warning"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="h6">Considera la diversidad de tus estudiantes</h5>
                                    <p class="small text-muted">Utiliza el generador de variantes para crear ejercicios adaptados a diferentes niveles y estilos de aprendizaje.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-info bg-opacity-10 p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-clipboard-check text-info"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="h6">Verifica antes de publicar</h5>
                                    <p class="small text-muted">Utiliza siempre el verificador de calidad antes de presentar los ejercicios a tus estudiantes.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 