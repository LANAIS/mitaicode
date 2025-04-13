@extends('layouts.app')

@section('title', 'Detalles de Entrega')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.challenges.index') }}">Desafíos</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.challenges.show', $submission->exercise->challenge->id) }}">{{ $submission->exercise->challenge->title }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.challenges.exercise', $submission->exercise->id) }}">{{ $submission->exercise->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Entrega #{{ $submission->id }}</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h2">Detalles de Entrega</h1>
            <span class="badge bg-{{ $submission->exercise->challenge->challenge_type == 'python' ? 'primary' : 'success' }} py-2 px-3">
                {{ $submission->exercise->challenge->challenge_type == 'python' ? 'Python' : 'Prompts IA' }}
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Contenido de la entrega -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $submission->exercise->challenge->challenge_type == 'python' ? 'Código Enviado' : 'Prompt Enviado' }}</h5>
                    <small class="text-muted">{{ $submission->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded">{{ $submission->exercise->challenge->challenge_type == 'python' ? $submission->submitted_code : $submission->submitted_prompt }}</pre>
                </div>
            </div>

            <!-- Estado y calificación -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Estado y Evaluación</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Estado</h6>
                            <p>
                                <span class="badge bg-{{ $submission->status == 'graded' ? 'success' : 'secondary' }} py-2">
                                    {{ $submission->status == 'graded' ? 'Calificado' : 'Pendiente de revisión' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Calificación</h6>
                            @if($submission->status == 'graded')
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $submission->score >= 70 ? 'success' : ($submission->score >= 50 ? 'warning' : 'danger') }}" 
                                             role="progressbar" 
                                             style="width: {{ $submission->score }}%;" 
                                             aria-valuenow="{{ $submission->score }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="fw-bold">{{ $submission->score }}/100</span>
                                </div>
                            @else
                                <p class="text-muted">No disponible</p>
                            @endif
                        </div>
                    </div>

                    @if($submission->status == 'graded')
                        <div class="mt-3">
                            <h6>Feedback</h6>
                            <div class="p-3 border rounded">
                                {!! nl2br(e($submission->feedback)) !!}
                            </div>
                        </div>
                    @endif

                    @if($submission->exercise->challenge->challenge_type == 'python' && $submission->output)
                        <div class="mt-3">
                            <h6>Resultado de ejecución</h6>
                            <pre class="bg-dark text-light p-3 rounded">{{ $submission->output }}</pre>
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
                    <h6>{{ $submission->exercise->title }}</h6>
                    <p class="text-muted">{{ Str::limit($submission->exercise->description, 100) }}</p>
                    
                    <hr>
                    
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <span class="text-muted"><i class="fas fa-tasks me-2"></i> Desafío:</span>
                            <span>{{ $submission->exercise->challenge->title }}</span>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted"><i class="fas fa-clock me-2"></i> Enviado:</span>
                            <span>{{ $submission->created_at->diffForHumans() }}</span>
                        </li>
                        @if($submission->status == 'graded')
                            <li class="mb-2">
                                <span class="text-muted"><i class="fas fa-check-circle me-2"></i> Calificado:</span>
                                <span>{{ $submission->updated_at->diffForHumans() }}</span>
                            </li>
                        @endif
                        <li class="mb-2">
                            <span class="text-muted"><i class="fas fa-hashtag me-2"></i> Intento:</span>
                            <span>#{{ $submissionNumber }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Acciones -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.challenges.exercise', $submission->exercise->id) }}" class="btn btn-primary">
                            <i class="fas fa-pen me-1"></i> Realizar otro intento
                        </a>
                        <a href="{{ route('student.challenges.show', $submission->exercise->challenge->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver al desafío
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 