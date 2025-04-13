@extends('layouts.app')

@section('title', 'Evaluación Automática')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.challenges.index') }}">Desafíos</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.challenges.show', $challenge->id) }}">{{ $challenge->title }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.challenges.exercise', $exercise->id) }}">{{ $exercise->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Evaluación Automática</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h2">Resultado de la Evaluación con IA</h1>
            <span class="badge bg-{{ $challenge->challenge_type == 'python' ? 'primary' : 'success' }} py-2 px-3">
                {{ $challenge->challenge_type == 'python' ? 'Python' : 'Prompts IA' }}
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Contenido de la entrega -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $challenge->challenge_type == 'python' ? 'Código Enviado' : 'Prompt Enviado' }}</h5>
                    <small class="text-muted">{{ $submission->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded">{{ $challenge->challenge_type == 'python' ? $submission->submitted_code : $submission->submitted_prompt }}</pre>
                </div>
            </div>

            <!-- Resultado de la evaluación automática -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-robot me-2"></i> Evaluación por Inteligencia Artificial</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Puntuación</h6>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 15px;">
                                    <div class="progress-bar bg-{{ $submission->score >= 70 ? 'success' : ($submission->score >= 50 ? 'warning' : 'danger') }}" 
                                         role="progressbar" 
                                         style="width: {{ $submission->score }}%;" 
                                         aria-valuenow="{{ $submission->score }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="fw-bold fs-5">{{ $submission->score }}/100</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Estado</h6>
                            <p>
                                <span class="badge bg-{{ $submission->score >= 60 ? 'success' : 'danger' }} py-2 px-3 fs-6">
                                    {{ $submission->score >= 60 ? 'APROBADO' : 'NO APROBADO' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="border-bottom pb-2">Feedback de la IA</h6>
                        <div class="p-3 border rounded bg-light">
                            {!! nl2br(e($submission->feedback)) !!}
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i> Esta evaluación fue realizada automáticamente por un modelo de Inteligencia Artificial. Si tienes alguna duda sobre la calificación, consulta con tu profesor.
                        </div>
                    </div>

                    <!-- Puntos ganados -->
                    @if($submission->score >= 60)
                    <div class="mt-4">
                        <div class="alert alert-success">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-trophy fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">¡Enhorabuena! Has ganado puntos</h6>
                                    <p class="mb-0">Has sumado <strong>{{ $submission->score }}</strong> puntos a tu ranking por completar este ejercicio correctamente.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Información del ejercicio -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del ejercicio</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $exercise->title }}</h6>
                    <p class="text-muted">{{ Str::limit($exercise->description, 100) }}</p>
                    
                    <hr>
                    
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <span class="text-muted"><i class="fas fa-tasks me-2"></i> Desafío:</span>
                            <span>{{ $challenge->title }}</span>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted"><i class="fas fa-clock me-2"></i> Evaluado:</span>
                            <span>{{ $submission->updated_at->diffForHumans() }}</span>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted"><i class="fas fa-clipboard-check me-2"></i> Método:</span>
                            <span class="fw-bold text-info">Evaluación automática con IA</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Progreso del desafío -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Progreso del desafío</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Ejercicios completados:</span>
                        <span class="fw-bold">{{ $progress->completed_exercises }}/{{ $progress->total_exercises }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-success" 
                             role="progressbar" 
                             style="width: {{ ($progress->total_exercises > 0) ? ($progress->completed_exercises / $progress->total_exercises) * 100 : 0 }}%;" 
                             aria-valuenow="{{ $progress->completed_exercises }}" 
                             aria-valuemin="0" 
                             aria-valuemax="{{ $progress->total_exercises }}">
                        </div>
                    </div>
                    
                    @if($progress->status == 'completed')
                    <div class="alert alert-success">
                        <i class="fas fa-medal me-2"></i> ¡Has completado este desafío!
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Acciones -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.challenges.exercise', $exercise->id) }}" class="btn btn-primary">
                            <i class="fas fa-pen me-1"></i> Realizar otro intento
                        </a>
                        <a href="{{ route('student.challenges.show', $challenge->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver al desafío
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 